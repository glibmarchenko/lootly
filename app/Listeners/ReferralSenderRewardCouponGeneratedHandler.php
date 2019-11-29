<?php

namespace App\Listeners;

use App\Events\ReferralSenderRewardCouponGenerated;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\MerchantRewardRepository;
use App\Repositories\Contracts\PointRepository;
use App\Repositories\Contracts\PointSettingsRepository;
use App\Repositories\Eloquent\Criteria\ByCustomer;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class ReferralSenderRewardCouponGeneratedHandler
{
    protected $customers;

    protected $merchants;

    protected $pointSettings;

    protected $points;

    protected $merchantRewards;

    /**
     * Create the event listener.
     *
     * @param \App\Repositories\Contracts\CustomerRepository       $customers
     * @param \App\Repositories\Contracts\MerchantRepository       $merchants
     * @param \App\Repositories\Contracts\PointSettingsRepository  $pointSettings
     *
     * @param \App\Repositories\Contracts\PointRepository          $points
     *
     * @param \App\Repositories\Contracts\MerchantRewardRepository $merchantRewards
     *
     */
    public function __construct(
        CustomerRepository $customers,
        MerchantRepository $merchants,
        PointSettingsRepository $pointSettings,
        PointRepository $points,
        MerchantRewardRepository $merchantRewards
    ) {
        $this->customers = $customers;
        $this->merchants = $merchants;
        $this->pointSettings = $pointSettings;
        $this->points = $points;
        $this->merchantRewards = $merchantRewards;
    }

    /**
     * @param \App\Events\ReferralSenderRewardCouponGenerated $event
     */
    public function handle(ReferralSenderRewardCouponGenerated $event)
    {
        $customer = $event->customer;

        if (boolval($event->merchantReward->send_email_notification)) {

            $merchant = $this->merchants->withCriteria([
                new EagerLoad(['points_settings']),
            ])->find($customer->merchant_id);

            $pointsBalance = 0;
            try {
                $pointsBalance = $this->points->withCriteria([
                    new ByCustomer($customer->id),
                ])->all()->sum('point_value');
            } catch (\Exception $e) {
                Log::error('Can\'t get customer point balance. '.$e->getMessage());
            }

            $tags = [
                '{reward-earning-text}' => $event->merchantReward->reward_email_text,
                // !!! IMPORTANT TO BE ON TOP because it may include other tags !!!
                '{customer}'            => trim($customer->name) ?: 'Dear customer',
                '{coupon-code}'         => $event->coupon->coupon_code,
                '{button-link}'         => $merchant->website,
                '{referral-name}'       => $event->referral->name,
                '{point-balance}'       => $pointsBalance,
                '{reward-text}'         => $event->merchantReward->reward_text,
                '{reward-name}'         => $event->merchantReward->reward_text,
                '{reward-icon}'         => $this->formatRewardIconOutput(app('email_notification_settings_service')->getRewardIconUrl($event->merchantReward, 'reward-icon', 'referral_sender_reward')),
            ];

            // Send email notification to customer
            app('email_notification')->send('referral_sender_reward', $merchant, $customer->name, $customer->email, $tags);
        }
    }

    protected function formatRewardIconOutput($url)
    {
        return trim($url) ? '<img src="'.$url.'" height="84" border="0" alt="" />' : '';
    }
}
