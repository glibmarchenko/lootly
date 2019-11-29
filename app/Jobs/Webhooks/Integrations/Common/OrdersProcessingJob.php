<?php

namespace App\Jobs\Webhooks\Integrations\Common;

use App\Events\OrderCreated;
use App\Models\MerchantReward;
use App\Repositories\Contracts\CouponRepository;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\IntegrationRepository;
use App\Repositories\Contracts\MerchantActionRepository;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\ReferralRepository;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\CouponCodeInArray;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\HasActionWithType;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrdersProcessingJob implements ShouldQueue
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

    protected $integrations;

    protected $coupons;

    protected $referrals;

    protected $merchantActions;

    protected $customers;

    protected $merchants;

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
     * @param \App\Repositories\Contracts\IntegrationRepository    $integrations
     * @param \App\Repositories\Contracts\CouponRepository         $coupons
     * @param \App\Repositories\Contracts\ReferralRepository       $referrals
     * @param \App\Repositories\Contracts\MerchantActionRepository $merchantActions
     * @param \App\Repositories\Contracts\CustomerRepository       $customers
     *
     * @return void
     */
    public function handle(
        MerchantRepository $merchants,
        IntegrationRepository $integrations,
        CouponRepository $coupons,
        ReferralRepository $referrals,
        MerchantActionRepository $merchantActions,
        CustomerRepository $customers
    ) {
        $this->merchants = $merchants;
        $this->integrations = $integrations;
        $this->referrals = $referrals;
        $this->coupons = $coupons;
        $this->merchantActions = $merchantActions;
        $this->customers = $customers;

        if (! isset($this->data->lootly_merchant_id)) {
            Log::error("Invalid request data");

            return;
        }

        $merchant = $this->merchants->find($this->data->lootly_merchant_id);

        if ($merchant) {
            $integrationSettings = [];
            try {
                $storeIntegration = app('merchant_service')->getStoreIntegration($merchant->id);

                $settings = $storeIntegration->pivot->settings ?? null;

                $integrationSettings = json_decode($settings, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $integrationSettings = [];
                }
            } catch (\Exception $e) {
                Log::error('Error while trying to get integration settings (Merchant: #'.$merchant->id.'):'.$e->getMessage());

                return;
            }

            if (! isset($integrationSettings['order_settings']['reward_status']) || ! in_array(strtolower($integrationSettings['order_settings']['reward_status']), [
                    'processing',
                    'any',
                ])) {
                Log::info('Order status for webhook proceeding not matching to selected (Merchant #'.$merchant->id.').');

                return;
            }

            $validator = Validator::make((array) $this->data, [
                'id'                  => 'bail|required|max:191',
                'total_price'         => 'bail|required|numeric',
                // The sum of all line item prices, discounts, shipping, taxes, and tips
                'total_tax'           => 'bail|required|numeric',
                // The sum of all the taxes applied to the order
                'total_discounts'     => 'bail|required|numeric',
                // The total discounts applied to the price of the order
                'subtotal_price'      => 'bail|required|numeric',
                // The price of the order after discounts but before shipping, taxes, and tips
                'taxes_included'      => 'bail|required|boolean',
                'discount_codes'      => 'array',
                'customer.id'         => 'bail|required|max:191',
                'customer.email'      => 'bail|required|email',
                'customer.first_name' => 'max:191',
                'customer.last_name'  => 'max:191',
                'customer.birthday'   => 'date',
            ]);

            if (! $validator->fails()) {
                try {
                    // Checking if order already exists
                    $orderCheck = app('order_service')->findWhere($merchant->id, [
                        'customer' => [
                            'email' => $this->data->customer['email'],
                            //'ecommerce_id' => $this->data->customer['id'],
                        ],
                        'order'    => [
                            'order_id' => $this->data->id,
                        ],
                    ]);

                    if ($orderCheck) {
                        Log::info('Order #'.$orderCheck->order_id.' already exists.');

                        return;
                    }

                    $customerStructure = [
                        'name'         => ($this->data->customer['first_name'] ?? '').' '.($this->data->customer['last_name'] ?? ''),
                        'email'        => $this->data->customer['email'],
                        'ecommerce_id' => $this->data->customer['id'] ?? null,
                    ];

                    if (isset($this->data->customer['birthday'])) {
                        $customerStructure['birthday'] = Carbon::createFromTimestamp(strtotime($this->data->customer['birthday']))
                            ->format('Y-m-d');
                    }

                    if (isset($this->data->customer['default_address'])) {
                        if (isset($this->data->customer['default_address']['country']) && trim($this->data->customer['default_address']['country'])) {
                            $customerStructure['country'] = trim($this->data->customer['default_address']['country']);
                        }
                        if (isset($this->data->customer['default_address']['zip']) && trim($this->data->customer['default_address']['zip'])) {
                            $customerStructure['zipcode'] = trim($this->data->customer['default_address']['zip']);
                        }
                    }

                    // Create/Update Customer
                    $customer = app('customer_service')->updateOrCreate($merchant, $customerStructure);

                    if ($customer) {
                        $orderStructure = [
                            'order_id'        => $this->data->id,
                            'total_price'     => floatval($this->data->total_price ?? 0),
                            // Final total price (discounted + taxes + shipping)
                            'total_tax'       => floatval($this->data->total_tax ?? 0),
                            // Total tax price
                            'total_discounts' => floatval($this->data->total_discounts ?? 0),
                            // Total discount price
                        ];
                        $orderStructure['subtotal_price'] = floatval($this->data->subtotal_price ?? 0) + $orderStructure['total_discounts']; // Price without discounts, shipping and taxes (unless taxes already included)
                        if (boolval($this->data->taxes_included) && $this->data->taxes_included !== 'false') {
                            $orderStructure['subtotal_price'] -= $orderStructure['total_tax']; // Price without discounts, shipping and taxes
                        }
                        $orderStructure['total_shipping'] = $orderStructure['total_price'] + $orderStructure['total_discounts'] - $orderStructure['subtotal_price'] - $orderStructure['total_tax']; // Total shipping

                        if (isset($this->data->discount_codes) && count($this->data->discount_codes)) {
                            $discount_codes = array_map(function ($item) {
                                return isset($item['code']) ? $item['code'] : null;
                            }, $this->data->discount_codes);
                            $discount_codes = array_filter($discount_codes);

                            try {
                                $coupon = $this->coupons->withCriteria([
                                    new ByMerchant($merchant->id),
                                    new CouponCodeInArray($discount_codes),
                                    new EagerLoad(['merchant_reward']),
                                ])->findWhereFirst([
                                    'is_used' => 0,
                                ]);

                                if ($coupon) {
                                    $this->coupons->clearEntity();
                                    $this->coupons->update($coupon->id, [
                                        'is_used' => 1,
                                    ]);
                                    $orderStructure['coupon_id'] = $coupon->id;

                                    try {
                                        // Check if coupon reward is receiver reward
                                        if ($coupon->merchant_reward && $coupon->merchant_reward->type_id == MerchantReward::REWARD_TYPE_REFERRAL_RECEIVER) {
                                            // Get coupon owner customer ID
                                            if (! $coupon->created_by_customer_id) {
                                                $couponCustomerId = $coupon->customer_id;
                                                // Check referrer
                                                try {
                                                    $this->referrals->clearEntity();
                                                    $parentRef = $this->referrals->withCriteria([
                                                        new LatestFirst(),
                                                    ])->findWhereFirst([
                                                        'invited_customer_id' => $couponCustomerId,
                                                    ]);
                                                    if ($parentRef) {
                                                        $orderStructure['referring_customer_id'] = $parentRef->referral_customer_id;
                                                    }
                                                } catch (\Exception $e) {
                                                    //
                                                }
                                            } else {
                                                $orderStructure['referring_customer_id'] = $coupon->created_by_customer_id;
                                            }
                                        }
                                    } catch (\Exception $e) {

                                    }
                                }
                            } catch (\Exception $e) {
                                //
                            }
                        }

                        // Create order
                        $order = app('order_service')->create($customer, $orderStructure);

                        if ($order) {
                            // Dispatch event for referral sender reward, etc.
                            try {
                                event(new OrderCreated($order));
                            } catch (\Exception $exception) {
                                Log::error('Something went wrong. '.$exception->getMessage());
                            }

                            $orderActions = $this->merchantActions->withCriteria([
                                new ByMerchant($merchant->id),
                                new EagerLoad(['action']),
                                new HasActionWithType('Orders'),
                            ])->findWhere([
                                'active_flag' => 1,
                            ]);

                            Log::info('Checking actions with type Orders. Found: '.count($orderActions));
                            if (count($orderActions)) {
                                $actionData = [
                                    'merchant'             => $merchant,
                                    'order_data'           => $orderStructure,
                                    'local_order_obj'      => $order,
                                    'integration_settings' => $integrationSettings,
                                ];

                                foreach ($orderActions as $orderAction) {
                                    app('action_service')->validateAndCreditPoints($orderAction, $customer, $actionData);
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::info('Error during customer processing for merchant #'.$merchant->id.': '.$e->getMessage());
                }
            } else {
                Log::error('Invalid request data: '.$validator->errors()->first());
            }
        } else {
            Log::error('Merchant with id '.$this->data->lootly_merchant_id.' not found.');
        }
    }
}