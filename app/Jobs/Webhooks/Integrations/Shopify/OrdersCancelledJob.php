<?php

namespace App\Jobs\Webhooks\Integrations\Shopify;

use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\MerchantActionRepository;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\PointRepository;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\HasActionWhere;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Repositories\IntegrationRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class OrdersCancelledJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The webhook data.
     *
     * @var object
     */
    protected $data;

    /**
     * Request headers.
     *
     * @var string
     */
    protected $headers;

    protected $integrationType;

    protected $merchants;

    protected $merchantActions;

    protected $points;

    protected $customers;

    /**
     * Create a new job instance.
     *
     * @param object $data    The webhook data (JSON decoded)
     * @param array  $headers Request headers
     * @param string $integrationType
     */
    public function __construct($data, array $headers, $integrationType = null)
    {
        $this->data = $data;
        $this->headers = $headers;
        $this->integrationType = $integrationType;
    }

    /**
     * Execute the job.
     *
     * @param \App\Repositories\Contracts\MerchantRepository       $merchants
     * @param \App\Repositories\Contracts\MerchantActionRepository $merchantActions
     * @param \App\Repositories\Contracts\PointRepository          $points
     * @param \App\Repositories\Contracts\CustomerRepository       $customers
     *
     * @return void
     */
    public function handle(
        MerchantRepository $merchants,
        MerchantActionRepository $merchantActions,
        PointRepository $points,
        CustomerRepository $customers
    ) {
        $this->merchants = $merchants;
        $this->merchantActions = $merchantActions;
        $this->points = $points;
        $this->customers = $customers;

        $shopDomain = isset($this->headers['x-shopify-shop-domain']) ? ($this->headers['x-shopify-shop-domain'][0] ?? '') : '';

        Log::info('Cancelled order at '.$shopDomain.'.');

        $integrationModel = new IntegrationRepository();

        $shopifyIntegration = $integrationModel->findActiveBySlug('shopify');

        if ($shopifyIntegration) {
            $targetMerchants = $integrationModel->getMerchantsWithActiveIntegration($shopifyIntegration, $shopDomain);

            if ($targetMerchants && count($targetMerchants)) {
                foreach ($targetMerchants as $merchant) {
                    $integrationSettings = [];
                    try {
                        $settings = $merchant->pivot->settings ?? null;
                        $integrationSettings = json_decode($settings, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            $integrationSettings = [];
                        }
                    } catch (\Exception $e) {
                        Log::error('Error while trying to convert shopify integration settings from JSON to array (Merchant: #'.$merchant->id.'):'.$e->getMessage());
                    }

                    if (! isset($integrationSettings['order_settings']['subtract_status']) || strtolower($integrationSettings['order_settings']['subtract_status']) != 'voided') {
                        Log::info('Order status for webhook proceeding not matching to selected (Shopify integration #'.$shopifyIntegration->id.', merchant #'.$merchant->id.').');
                        continue;
                    }

                    try {
                        if (isset($this->data->id) && trim($this->data->id)) {
                            $order_id = trim($this->data->id);

                            // Find order by order_id and update status to "voided"

                            $this->merchants->clearEntity();
                            try {
                                $order = $this->merchants->orders($merchant->id)->withCriteria([
                                    new LatestFirst(),
                                    new EagerLoad(['customer']),
                                ])->findWhereFirst([
                                    'order_id' => $order_id,
                                ]);
                            } catch (\Exception $e) {
                                // No such order
                            }

                            if (isset($order) && $order) {
                                if ($order->status == "voided" || $order->status == "refunded") {
                                    Log::info("Order #".$order->id." was cancelled/refunded before");

                                    return;
                                }
                                // Cancel order

                                try {
                                    $canceled = app('order_service')->cancel($order);
                                } catch (\Exception $e) {
                                    Log::error('Can not cancel order #'.$order->id.'. '.$e->getMessage());
                                }
                                if (isset($canceled) && $canceled) {
                                    // @todo: OrderCanceledEvent

                                    $deductedPoints = [];
                                    $errors = [];

                                    // Find points connected with order and deduct points (new records with negative value)
                                    // Get purchase merchant_action_id
                                    $this->merchantActions->clearEntity();
                                    try {
                                        $purchaseAction = $this->merchantActions->withCriteria([
                                            new ByMerchant($merchant->id),
                                            new EagerLoad(['action']),
                                            new HasActionWhere([
                                                'type' => 'Orders',
                                                'url'  => 'make-a-purchase',
                                            ]),
                                        ])->findWhereFirst([
                                            'active_flag' => 1,
                                        ]);
                                    } catch (\Exception $e) {
                                        // No active "make-a-purchase" action
                                    }

                                    if (isset($purchaseAction) && $purchaseAction) {
                                        // Get points earned for purchase action
                                        try {
                                            $this->points->clearEntity();
                                            $point = $this->points->withCriteria([
                                                new LatestFirst(),
                                            ])->findWhereFirst([
                                                'order_id'           => $order->id,
                                                'merchant_action_id' => $purchaseAction->id,
                                                [
                                                    'point_value',
                                                    '>',
                                                    0,
                                                ],
                                            ]);
                                            if ($point) {
                                                try {
                                                    $this->points->clearEntity();
                                                    $deducted = $this->points->rollbackPoints($point->id, ['comment' => 'Cancelled']);
                                                    $deductedPoints[] = $deducted->id;
                                                } catch (\Exception $e) {
                                                    $errors[] = [
                                                        'id'      => $point->id,
                                                        'message' => $e->getMessage(),
                                                    ];
                                                }
                                            }
                                        } catch (\Exception $exception) {
                                            //
                                        }
                                    }

                                    // Check goal on order count
                                    $this->merchantActions->clearEntity();
                                    try {
                                        $goalOrdersAction = $this->merchantActions->withCriteria([
                                            new ByMerchant($merchant->id),
                                            new EagerLoad(['action']),
                                            new HasActionWhere([
                                                'type' => 'Orders',
                                                'url'  => 'goal-orders',
                                            ]),
                                        ])->findWhereFirst([
                                            'active_flag' => 1,
                                        ]);
                                    } catch (\Exception $e) {
                                        // No active "goal-orders" action
                                    }

                                    if (isset($goalOrdersAction) && $goalOrdersAction) {
                                        // Get points earned for goal orders action
                                        try {
                                            $this->points->clearEntity();
                                            $point = $this->points->withCriteria([
                                                new LatestFirst(),
                                            ])->findWhereFirst([
                                                'customer_id'        => $order->customer->id,
                                                'merchant_action_id' => $goalOrdersAction->id,
                                            ]);
                                            if ($point && $point->point_value > 0) {
                                                // Count completed orders
                                                try {
                                                    $this->customers->clearEntity();
                                                    $ordersCount = $this->customers->countValidOrders($order->customer->id);
                                                    if (isset($goalOrdersAction->goal) && intval($ordersCount) < intval($goalOrdersAction->goal)) {
                                                        try {
                                                            $this->points->clearEntity();
                                                            $deducted = $this->points->rollbackPoints($point->id, ['comment' => 'Cancelled']);
                                                            $deductedPoints[] = $deducted->id;
                                                        } catch (\Exception $e) {
                                                            $errors[] = [
                                                                'id'      => $point->id,
                                                                'message' => $e->getMessage(),
                                                            ];
                                                        }
                                                    }
                                                } catch (\Exception $e) {
                                                    //
                                                }
                                            }
                                        } catch (\Exception $exception) {
                                            //
                                        }
                                    }

                                    // Check goal on spent amount
                                    $this->merchantActions->clearEntity();
                                    try {
                                        $goalSpendAction = $this->merchantActions->withCriteria([
                                            new ByMerchant($merchant->id),
                                            new EagerLoad(['action']),
                                            new HasActionWhere([
                                                'type' => 'Orders',
                                                'url'  => 'goal-spend',
                                            ]),
                                        ])->findWhereFirst([
                                            'active_flag' => 1,
                                        ]);
                                    } catch (\Exception $e) {
                                        // No active "goal-spend" action
                                    }

                                    if (isset($goalSpendAction) && $goalSpendAction) {
                                        // Get points earned for goal spend action
                                        try {
                                            $this->points->clearEntity();
                                            $point = $this->points->withCriteria([
                                                new LatestFirst(),
                                            ])->findWhereFirst([
                                                'customer_id'        => $order->customer->id,
                                                'merchant_action_id' => $goalSpendAction->id,
                                            ]);
                                            if ($point && $point->point_value > 0) {
                                                // Calculate spend amount
                                                try {
                                                    $this->customers->clearEntity();
                                                    $totalSpentAmount = $this->customers->getTotalSpent($order->customer->id);
                                                    if (isset($goalSpendAction->goal) && floatval($totalSpentAmount) < floatval($goalSpendAction->goal)) {
                                                        try {
                                                            $this->points->clearEntity();
                                                            $deducted = $this->points->rollbackPoints($point->id, ['comment' => 'Cancelled']);
                                                            $deductedPoints[] = $deducted->id;
                                                        } catch (\Exception $e) {
                                                            $errors[] = [
                                                                'id'      => $point->id,
                                                                'message' => $e->getMessage(),
                                                            ];
                                                        }
                                                    }
                                                } catch (\Exception $e) {
                                                    //
                                                }
                                            }
                                        } catch (\Exception $exception) {
                                            //
                                        }
                                    }

                                    Log::info('Deducted points: '.implode(', ', $deductedPoints));
                                    Log::info(count($errors).' errors'.(count($errors) ? ': '.print_r($errors, true) : ''));
                                }
                            } else {
                                Log::info('No order with #'.($this->data->id ?? '').' found.');
                            }
                        }
                    } catch (\Exception $e) {
                        Log::info('Error during order (#'.($this->data->id ?? '').') canceling for merchant #'.$merchant->id.': '.$e->getMessage());
                    }
                }
            } else {
                Log::warning('No merchants with active Shopify integration');
            }
        } else {
            Log::warning('No integration with slug "shopify"');
        }
    }
}