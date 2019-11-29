<?php
namespace App\Jobs\Webhooks\Integrations\Traits;

use App\Events\OrderCreated;
use App\Models\MerchantReward;
use App\Models\Tag;
use App\Models\MerchantActionRestriction;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\CouponCodeInArray;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\HasActionWithType;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

trait RestrictionsTrait {

    public function checkAndReward( $merchant, $data, $coupons, $referrals, $customer, $merchantActions, $integrationSettings, $integrationName ) {

        Log::info( '--- Check and reward ---' . $integrationName );

        $passes_restrictions = true;
        if( $integrationName != 'bigcommerce' ) {
            $passes_restrictions = $this->checkRestrictions( $merchant->id, $data->line_items, $customer, $integrationName );
        }

        if( $passes_restrictions ) {

            Log::info( '------passes restrictions: true------' );

           /* Log::info( 'data' );
            Log::info( print_r( $data, true ) );*/

            $orderStructure = [
                'order_id'        => $data->id,
                // Final total price (discounted + taxes + shipping)
                'total_price'     => floatval($data->total_price ?? 0),
                // Total tax price
                'total_tax'       => floatval($data->total_tax ?? 0),
                // Total discount price
                'total_discounts' => floatval($data->total_discounts ?? 0),
                'status' => 'completed',
            ];

            $orderStructure['subtotal_price'] = floatval($data->subtotal_price ?? 0) + $orderStructure['total_discounts']; // Price without discounts, shipping and taxes (unless taxes already included)
            /*if (boolval($data->taxes_included) && $data->taxes_included !== 'false') {
                $orderStructure['subtotal_price'] -= $orderStructure['total_tax']; // Price without discounts, shipping and taxes
            }*/

            /*Log::info( 'total_price: ' . $orderStructure['total_price'] );
            Log::info( 'total_discounts: ' . $orderStructure['total_discounts'] );
            Log::info( 'subtotal_price: ' . $orderStructure['subtotal_price'] );
            Log::info( 'total_tax: ' . $orderStructure['total_tax'] );*/

            $orderStructure['total_shipping'] = $orderStructure['total_price'] + $orderStructure['total_discounts'] - $orderStructure['subtotal_price'] - $orderStructure['total_tax']; // Total shipping

            if( $integrationName != 'bigcommerce' ) {
                if (isset($data->discount_codes) && count($data->discount_codes)) {
                    $discount_codes = array_map(function ($item) {
                        return isset($item['code']) ? $item['code'] : null;
                    }, $data->discount_codes);
                    $discount_codes = array_filter($discount_codes);

                    try {
                        $coupon = $coupons->withCriteria([
                            new ByMerchant($merchant->id),
                            new CouponCodeInArray($discount_codes),
                            new EagerLoad(['merchant_reward']),
                        ])->findWhereFirst([
                            'is_used' => 0,
                        ]);

                        if ($coupon) {
                            $coupons->clearEntity();
                            $coupons->update($coupon->id, [
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
                                            $referrals->clearEntity();
                                            $parentRef = $referrals->withCriteria([
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
            }

            // Create order
            $order = app('order_service')->create( $customer, $orderStructure);

            if ($order) {
                // Dispatch event for referral sender reward, etc.
                try {
                    Log::info('dispatching order created event');
                    event(new OrderCreated($order));
                } catch (\Exception $exception) {
                    Log::error('Something went wrong. '.$exception->getMessage());
                }

                $orderActions = $merchantActions->withCriteria([
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
    }

    private function checkRestrictions( $merchant_id, $line_items, $customer, $integrationName ) {

        $passes_restrictions = $this->checkProductRestrictions( $merchant_id, $line_items, $integrationName );
        if( $passes_restrictions ) {
            $passes_restrictions = $this->checkCustomerRestrictions( $merchant_id, $customer );
        }
        return $passes_restrictions;

    }

    private function checkProductRestrictions( $merchant_id, $line_items, $integrationName ) {

        $passes_restrictions = true;

        $product_restriction = MerchantActionRestriction::where( 'merchant_id', $merchant_id )->where( 'type', 'product' )->where( 'restrictions', '<>', '[]' )->first();

        if( $product_restriction ) {

            $product_IDs = [];
            foreach( $line_items as $product ) {
                $product_IDs[] = $product['product_id'];
            }

            $storeIntegration = app('merchant_service')->getStoreIntegration($merchant_id);

            $collection_names = app($integrationName . '_ecommerce_integration')->getСollections( $storeIntegration, $product_IDs );

            //Sorting out restrictions
            foreach( $product_restriction->restrictions as $product_restriction ) {

                //Checking product ID restrictions
                if( $product_restriction['condition'] == 'equals' && $product_restriction['type'] == 'product-id' ) {
                    if( !in_array( $product_restriction['value'][0], $product_IDs ) ){
                        $passes_restrictions = false;
                    }
                    break;
                }
                if( $product_restriction['condition'] == 'has' && $product_restriction['type'] == 'product-id' ) {
                    foreach( $product_restriction['value'] as $value ) {
                        if( !in_array( $value, $product_IDs ) ){
                            $passes_restrictions = false;
                            break 2;
                        }
                    }
                }
                if( $product_restriction['condition'] == 'has-none-of' && $product_restriction['type'] == 'product-id' ) {
                    foreach( $product_restriction['value'] as $value ) {
                        if( in_array( $value, $product_IDs ) ){
                            $passes_restrictions = false;
                            break 2;
                        }
                    }
                }

                //Checking collections restrictions
                if( $product_restriction['condition'] == 'equals' && $product_restriction['type'] == 'collection' ) {
                    if( !in_array( $product_restriction['value'][0], $collection_names ) ){
                        $passes_restrictions = false;
                        break;
                    }
                }
                if( $product_restriction['condition'] == 'has' && $product_restriction['type'] == 'collection' ) {
                    foreach( $product_restriction['value'] as $value ) {
                        if( !in_array( $value, $collection_names ) ){
                            $passes_restrictions = false;
                            break 2;
                        }
                    }
                }
                if( $product_restriction['condition'] == 'has-none-of' && $product_restriction['type'] == 'collection' ) {
                    foreach( $product_restriction['value'] as $value ) {
                        if( in_array( $value, $collection_names ) ){
                            $passes_restrictions = false;
                            break 2;
                        }
                    }
                }
            }
        }

        return $passes_restrictions;
    }

    private function checkCustomerRestrictions( $merchant_id, $customer ) {

        $passes_restrictions = true;

        $customer_restriction = MerchantActionRestriction::where( 'merchant_id', $merchant_id )->where( 'type', 'customer' )->where( 'restrictions', '<>', '[]' )->first();

        if( $customer_restriction ) {

            $customer_tags = [];
            foreach( $customer->tags as $tag ) {
                $customer_tags[] = $tag->name;
            }

            $tier = null;
            if( $customer->tier ) {
                $tier = $customer->tier->id;
            }

            //Sorting out restrictions
            foreach( $customer_restriction->restrictions as $customer_restriction ) {

                //Checking customer tags restrictions
                if( $customer_restriction['type'] == 'customer-tags' ) {

                    for( $i = 0; $i < count( $customer_restriction['value'] ); $i++ ) {
                        $tag_id = $customer_restriction['value'][$i];
                        $tag = Tag::find( $tag_id );
                        $customer_restriction['value'][$i] = $tag->name;
                    }

                    if( $customer_restriction['condition'] == 'equals' ) {
                        if( !in_array( $customer_restriction['value'][0], $customer_tags ) ){
                            $passes_restrictions = false;
                            break;
                        }
                    }
                    if( $customer_restriction['condition'] == 'has' ) {
                        foreach( $customer_restriction['value'] as $value ) {
                            if( !in_array( $value, $customer_tags ) ){
                                $passes_restrictions = false;
                                break 2;
                            }
                        }
                    }
                    if( $customer_restriction['condition'] == 'has-none-of' ) {
                        foreach( $customer_restriction['value'] as $value ) {
                            if( in_array( $value, $customer_tags ) ){
                                $passes_restrictions = false;
                                break 2;
                            }
                        }
                    }
                }

                //Checking vip tier restrictions (by tier id)
                if( $customer_restriction['type'] == 'vip-tier' ) {
                    if( $customer_restriction['condition'] == 'equals' ) {
                        if( $customer_restriction['value'][0] !== $tier ){
                            $passes_restrictions = false;
                            break;
                        }
                    }
                    if( $customer_restriction['condition'] == 'has' ) {
                        if( !in_array( $tier, $customer_restriction['value'] ) || is_null( $tier ) ) {
                            $passes_restrictions = false;
                            break;
                        }
                    }
                    if( $customer_restriction['condition'] == 'has-none-of' ) {
                        if( in_array( $tier, $customer_restriction['value'] ) && !is_null( $tier ) ){
                            $passes_restrictions = false;
                            break;
                        }
                    }
                }
            }
        }

        return $passes_restrictions;
    }
}
