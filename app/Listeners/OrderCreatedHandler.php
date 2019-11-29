<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Events\ReferralSenderRewardCouponGenerated;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\RewardRepository;
use App\Repositories\Contracts\TierSettingsRepository;
use App\Repositories\Eloquent\Criteria\WithSenderReward;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class OrderCreatedHandler
{
    protected $customers;

    protected $rewards;

    protected $tierSettings;

    /**
     * Create the event listener.
     *
     * @param \App\Repositories\Contracts\CustomerRepository     $customers
     * @param \App\Repositories\Contracts\RewardRepository       $rewards
     * @param \App\Repositories\Contracts\TierSettingsRepository $tierSettings
     */
    public function __construct(
        CustomerRepository $customers,
        RewardRepository $rewards,
        TierSettingsRepository $tierSettings
    ) {
        Log::info( 'Order created construct' );
        $this->customers = $customers;
        $this->rewards = $rewards;
        $this->tierSettings = $tierSettings;
    }

    /**
     * Handle the event.
     *
     * @param  OrderCreated $event
     *
     * @return void
     */
    public function handle(OrderCreated $event)
    {
        Log::info( 'Order created handler' );

        // Get Customer Data
        try {
            $customer = $this->customers->withCriteria([
                new WithSenderReward(),
            ])->findWhereFirst([
                'id' => $event->order->customer_id,
            ]);
        } catch (\Exception $exception) {
            Log::error('OrderCreated Event Error: Customer #'.$event->order->customer_id.' not found for order #'.$event->order->id.'.');
        }

        if (isset($customer) && $customer) {

            Log::info( 'customer isset' );
            // Check if this is first order
            $orders_count = intval($customer->orders_count);

            if ($orders_count == 1) {
                // Check if Sender Rewards enabled
                if (isset($customer->merchant->rewards) && count($customer->merchant->rewards)) {
                    $merchant_reward = $customer->merchant->rewards[0];
                    // Check if user have Referrer
                    try {
                        $referrer = $this->customers->findReferrer($customer->id);
                    } catch (\Exception $e) {
                        // Log
                    }
                    if (isset($referrer) && $referrer) {

                        Log::info( 'isset referrer' );
                        // Check if order customer ID != referrer ID
                        if ($customer->id !== $referrer->id) {

                            try {
                                $reward = $this->rewards->find($merchant_reward->reward_id);
                            } catch (\Exception $e) {
                                //
                            }
                            // Reward type checking: coupon | points
                            if (isset($reward) && $reward) {
                                if (in_array($reward->slug, config('rewards.sender_rewards.reward_types.coupon', []))) {
                                    // Generate coupon
                                    try {
                                        $generatedCoupon = app('coupon_service')->generateRewardCoupon($merchant_reward->id, $referrer->id, null, null, ['available_for_owner_only' => true]);
                                        Log::info('OrderCreated Event: Coupon #'.$generatedCoupon->id.' successfully generated.');
                                        // Send email to customer
                                        event(new ReferralSenderRewardCouponGenerated($referrer, $merchant_reward, $generatedCoupon, $customer));
                                    } catch (\Exception $exception) {
                                        Log::error('OrderCreated Event Error: Cannot generate discount with reward #'.$merchant_reward->id.' for customer #'.$referrer->id.'. '.$exception->getMessage());
                                    }
                                } elseif (in_array($reward->slug, config('rewards.sender_rewards.reward_types.points', []))) {

                                    // Add points
                                    $addedPoints = app('customer_service')->addRewardPoints($merchant_reward, $referrer);

                                    //If points were added & email setting is on - send email
                                    if( $addedPoints && boolval( $merchant_reward->send_email_notification ) ) {
                                        Log::info( 'ReferralSenderEarnedPoints event' );
                                        event( new( ReferralSenderEarnedPoints( $referrer, $merchant_reward, $customer ) ) );
                                    }
                                    Log::info('OrderCreated Event: Add point record #'.$addedPoints->id.' successfully stored.');
                                } else {
                                    Log::info('OrderCreated Event: Sender Reward Type Not Defined');
                                }
                            } else {
                                Log::error('OrderCreated Event Error: Cannot get reward info (Reward #'.$merchant_reward->reward_id.')');
                            }
                        }
                    }
                }
            }

            try {
                $tierSettings = $this->tierSettings->findWhereFirst([
                    'merchant_id' => $customer->merchant_id,
                ]);
                if (isset($tierSettings) && $tierSettings && strtolower($tierSettings->program_status) == 'enabled' && $tierSettings->requirement_type == 'amount-spent') {
                    app('customer_service')->updateTier($customer->merchant_id, $customer->id);
                } else {
                    Log::info('OrderObserver: VIP System is disabled');
                }
            } catch (\Exception $exception) {
                Log::info('Order Create Listener: '.$exception->getMessage());
            }
        }
    }
}
