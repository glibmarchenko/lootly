<?php

namespace App\Jobs\Webhooks\Integrations\Bigcommerce;

use App\Repositories\Contracts\CouponRepository;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\IntegrationRepository;
use App\Repositories\Contracts\MerchantActionRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\Contracts\ReferralRepository;
use App\Repositories\Contracts\PointRepository;
use App\Helpers\EcommerceIntegration\BigcommerceEcommerceIntegrationService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Jobs\Webhooks\Integrations\Traits\RestrictionsTrait;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\HasActionWhere;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Models\Integration;
use App\Transformers\MerchantIntegrationTransformer;

class OrderStatusUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RestrictionsTrait;

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

    protected $points;

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
     * @param \App\Repositories\Contracts\IntegrationRepository    $integrations
     * @param \App\Repositories\Contracts\CouponRepository         $coupons
     * @param \App\Repositories\Contracts\ReferralRepository       $referrals
     * @param \App\Repositories\Contracts\MerchantActionRepository $merchantActions
     * @param \App\Repositories\Contracts\CustomerRepository       $customers
     *
     * @return void
     */
    public function handle(
        IntegrationRepository $integrations,
        CouponRepository $coupons,
        ReferralRepository $referrals,
        MerchantActionRepository $merchantActions,
        CustomerRepository $customers,
        BigcommerceEcommerceIntegrationService $bcService,
        PointRepository $points
    ) {
        $this->integrations = $integrations;
        $this->referrals = $referrals;
        $this->coupons = $coupons;
        $this->merchantActions = $merchantActions;
        $this->customers = $customers;
        $this->bcService = $bcService;
        $this->points = $points;

        $merchantModel = new MerchantRepository();

        $shopDomain = $this->data->producer;
        $merchant = $bcService->getMerchant( $shopDomain );
        Log::info( 'Order status changed at ' . $shopDomain . ' to ' . $this->data->data['status']['new_status_id'] );
        if ( $merchant ) {
            $bigcommerceIntegration = $this->bcService->getBigcommerceIntegration();
            $checkIntegration = $merchantModel->findIntegrationWithToken( $merchant, $bigcommerceIntegration );
            $token = trim($checkIntegration->pivot->token);

            $order_info = $this->bcService->makeApiCall( 'v2/orders/' . $this->data->data['id'], null, $shopDomain, $token );

            //Log::info( 'Order info' );
            //Log::info( print_r( $order_info, true ) );

            $integrationSettings = json_decode( $merchant->pivot->settings ?? null, true );

            // Checking if order already exists
            $orderCheck = app('order_service')->findWhere($merchant->id, [
                'customer' => [
                    //'email' => $this->data->customer['email'],
                    'ecommerce_id' => $order_info->customer_id,
                ],
                'order'    => [
                    'order_id' => $this->data->data['id'],
                ],
            ]);

            $settings = fractal( $checkIntegration )->transformWith(new MerchantIntegrationTransformer)->toArray();
            $settings = $settings['data']['settings']['order_settings'];

            //Reward for order
            if( ( $this->data->data['status']['new_status_id'] == 10 && $settings['reward_status'] == 'completed' )
                || ( $this->data->data['status']['new_status_id'] == 2 && $settings['reward_status'] == 'shipped' ) ) {

                if ( ( !isset( $integrationSettings['order_settings']['include_previous_orders'] ) ||
                        !boolval( $integrationSettings['order_settings']['include_previous_orders'] ) ) &&
                    $order_info->customer_id == 0 ) {
                    Log::info('Include previous purchases as a guest - Disabled (Bigcommerce integration #'.$bigcommerceIntegration->id.', merchant #'.$merchant->id.').');
                }
                else {
                    if ( $orderCheck ) {
                        if( $orderCheck->status == 'refunded' ) {
                            $orderCheck->delete();
                            Log::info('Previous refunded order was deleted');
                        }
                        elseif( $orderCheck->status == 'completed' ) {
                            return;
                        }
                    }

                    if( $order_info->customer_id == 0 ) {
                        $name = $order_info->billing_address->first_name . ' ' . $order_info->billing_address->last_name;
                        $email = $order_info->billing_address->email;
                    }
                    else {
                        $customer_info = $this->bcService->makeApiCall( 'v2/customers/' . $order_info->customer_id, null, $shopDomain, $token );
                        $name = $customer_info->first_name . ' ' . $customer_info->last_name;
                        $email = $customer_info->email;
                    }

                    $customerStructure = [
                        'name'         => $name,
                        'email'        => $email,
                        'ecommerce_id' => $order_info->customer_id,
                        'birthday'     => '0000-00-00'
                    ];

                    // Create/Update customer
                    $customer = app('customer_service')->updateOrCreate($merchant, $customerStructure);

                    /*Log::info( 'Order info' );
                    Log::info( print_r( $order_info, true ) );*/

                    $data_to_pass = (object)[];
                    $data_to_pass->id = $order_info->id;
                    $data_to_pass->total_price = $order_info->total_inc_tax;
                    $data_to_pass->total_tax = $settings['include_shipping'] ? $order_info->total_tax : $order_info->subtotal_tax;
                    $data_to_pass->total_discounts = $order_info->discount_amount + $order_info->coupon_discount;
                    $data_to_pass->taxes_included = $settings['include_taxes'] ? true : false;
                    $data_to_pass->subtotal_price = $order_info->subtotal_ex_tax;

                    $this->checkAndReward( $merchant, $data_to_pass, $this->coupons, $this->referrals, $customer, $this->merchantActions, $integrationSettings, 'bigcommerce' );
                    Log::info( 'check & reward completed' );
                }
            }

            //Subtract  points (refund)
            elseif( ( ( $this->data->data['status']['new_status_id'] == 4 || $this->data->data['status']['new_status_id'] == 14 ) && $settings['subtract_status'] == 'refunded' )
                    || ( $this->data->data['status']['new_status_id'] == 5 && $settings['subtract_status'] == 'cancelled' ) ) {

                $this->subtract_points( $orderCheck, $merchant, $this->data->data['status']['new_status_id'] == 14, $order_info->refunded_amount );
            }
        }
    }

    private function subtract_points( $orderCheck, $merchant, $partial, $refunded_amount ) {

        // Find order by order_id and update status to "refunded"
        $order = $orderCheck;

       /* Log::info( 'order' );
        Log::info( print_r( $order, true) );*/

        if (isset($order) && $order) {

            if ( $order->status == "refunded" ) {
                Log::info("Order #".$order->id." was refunded/cancelled before");

                return;
            }

            // Refund order
            $purchaseAction = $this->merchantActions->withCriteria([
                new ByMerchant( $merchant->id ),
                new EagerLoad( ['action'] ),
                new HasActionWhere([
                    'type' => 'Orders',
                    'url'  => 'make-a-purchase',
                ]),
            ])->findWhereFirst([
                'active_flag' => 1,
            ]);

            if (isset($purchaseAction) && $purchaseAction) {
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

                    $order->status = "refunded";
                    $order->save();


                    try {

                        if( $partial ) {

                            $ref = floatval($refunded_amount) - $order->refunded_total;
                            $ref_koef = $ref / $order->total_price;
                            if ($ref_koef > 1) {
                                $ref_koef = 1;
                            }

                            $deduct_point_value = floor($ref_koef * $point->point_value);
                        }
                        else {
                            $deduct_point_value = $point->point_value;
                        }

                        Log::info( 'Point value: ' . $point->point_value );
                        Log::info( 'Deducted order amount: ' . $deduct_point_value );

                        $this->points->clearEntity();
                        $deducted = $this->points->rollbackPoints($point->id, [
                            'point_value'             => -$deduct_point_value,
                            'total_order_amount'      => $point->point_value,
                            'rewardable_order_amount' => $point->point_value,
                            'comment'                 => '',
                            'title'                   => 'Order Refund'
                        ]);
                        if ($deducted) {
                            $deductedPoints[] = $deducted->id;
                        }
                        Log::info( 'Order was refunded' );
                    } catch (\Exception $e) {
                        $errors[] = [
                            'id'      => $point->id,
                            'message' => $e->getMessage(),
                        ];
                    }
                }
            }

        } else {
            Log::info('No order with #'.($this->data->order_id ?? '').' found.');
        }
    }
}
