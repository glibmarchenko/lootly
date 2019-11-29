<?php

namespace App\Listeners;

use App\Events\CustomerSpentPointsForReward;
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

class CustomerSpentPointsForRewardHandler
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
     * @param \App\Events\CustomerSpentPointsForReward $event
     */
    public function handle(CustomerSpentPointsForReward $event)
    {
        $customer = $this->customers->find($event->point->customer_id);

        if ($customer) {
            if (boolval($event->merchantReward->send_email_notification)) {

                $merchant = $this->merchants->withCriteria([
                    new EagerLoad(['points_settings']),
                ])->find($customer->merchant_id);

                $pointSettings = $merchant->points_settings;
                if (! $pointSettings) {
                    $pointSettings = $this->pointSettings->getDefaults();
                }

                $pointsBalance = $event->point->point_value;
                try {
                    $pointsBalance = $this->points->withCriteria([
                        new ByCustomer($customer->id),
                    ])->all()->sum('point_value');
                } catch (\Exception $e) {
                    Log::error('Can\'t get customer point balance. '.$e->getMessage());
                }

                $tags = [
                    '{points-spent-action}' => $event->merchantReward->reward_email_text,
                    // !!! IMPORTANT TO BE ON TOP because it may include other tags !!!
                    '{customer}'            => trim($customer->name) ?: 'Dear customer',
                    '{points}'              => abs($event->point->point_value),
                    '{points-name}'         => ($event->point->point_value == 1) ? $pointSettings->name : $pointSettings->plural_name,
                    '{coupon-code}'         => $event->coupon->coupon_code,
                    '{point-balance}'       => $pointsBalance,
                    '{reward-name}'         => $event->merchantReward->reward_name,
                    '{reward-icon}'         => $this->formatRewardIconOutput(app('email_notification_settings_service')->getRewardIconUrl($event->merchantReward, 'reward-icon', 'points_spent')),
                    '{button-link}'         => $merchant->website,
                ];

                // Send email notification to customer
                app('email_notification')->send('points_spent', $merchant, $customer->name, $customer->email, $tags);
            }

            // Trigger Zapier webhook
            if ($merchant->integrations->filter(function ($integration) {return $integration->slug == 'zapier';})->count()) {
                app('App\Services\Zapier\ZapierHook')->send($merchant, 'points-spent', [
                    'email'  => $customer->email,
                    'points' => $event->point->point_value,
                    'reward' => $event->merchantReward->reward_name,
                ]);
            }
        }
    }

    protected function formatRewardIconOutput($url)
    {
        return trim($url) ? '<img src="'.$url.'" height="84" border="0" alt="" />' : '';
    }
}
