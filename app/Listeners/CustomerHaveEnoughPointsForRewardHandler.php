<?php

namespace App\Listeners;

use App\Events\CustomerHaveEnoughPointsForReward;
use App\Events\CustomerSpentPointsForReward;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\MerchantRewardRepository;
use App\Repositories\NotificationSettingsRepository;
use App\Repositories\Contracts\PointRepository;
use App\Repositories\Contracts\PointSettingsRepository;
use App\Repositories\Eloquent\Criteria\ByCustomer;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class CustomerHaveEnoughPointsForRewardHandler
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
     * @param \App\Events\CustomerHaveEnoughPointsForReward $event
     */
    public function handle(CustomerHaveEnoughPointsForReward $event)
    {
        $notificationSettingsRepo = new NotificationSettingsRepository();
        $customer = $event->customer;
        $pointsBalance = $event->pointsBalance;

        if ($customer) {

            $updateLastAvailableRewardId = false;

            if ($customer->last_available_reward_id) {
                $this->merchantRewards->clearEntity();
                try {
                    $lastAvailableReward = $this->merchantRewards->find($customer->last_available_reward_id);
                } catch (\Exception $e) {
                    $updateLastAvailableRewardId = true;
                }

                if (isset($lastAvailableReward) && $lastAvailableReward) {
                    if ($lastAvailableReward->points_required >= $event->merchantReward->points_required) {
                        // DO NOT SEND SAME EMAIL AGAIN
                        return;
                    } else {
                        $updateLastAvailableRewardId = true;
                    }
                }
            } else {
                $updateLastAvailableRewardId = true;
            }

            if ($updateLastAvailableRewardId) {
                $this->customers->clearEntity();
                $this->customers->update($customer->id, [
                    'last_available_reward_id' => $event->merchantReward->id,
                ]);
            }

            $merchant = $this->merchants->withCriteria([
                new EagerLoad(['points_settings']),
            ])->find($customer->merchant_id);

            $pointSettings = $merchant->points_settings;
            if (! $pointSettings) {
                $pointSettings = $this->pointSettings->getDefaults();
            }

            $tags = [
                '{customer}'      => trim($customer->name) ?: 'Dear customer',
                '{points-name}'   => ($pointsBalance == 1) ? $pointSettings->name : $pointSettings->plural_name,
                '{point-balance}' => $pointsBalance,
                '{reward-name}'   => $event->merchantReward->reward_name,
                '{reward-icon}'   => $this->formatRewardIconOutput(app('email_notification_settings_service')->getRewardIconUrl($event->merchantReward, 'reward-icon', 'points_reward_available')),
                '{button-link}'   => $merchant->website.(strpos($merchant->website, '?') !== false ? '&' : '?').'lourl=get-coupon',
            ];

            $notificationSettings = $notificationSettingsRepo->findByType($merchant, 'points_reward_available');
            if($notificationSettings->status == 1) {
                // Send email notification to customer
                app('email_notification')->send('points_reward_available', $merchant, $customer->name, $customer->email, $tags);
            }

            return;
        }
    }

    protected function formatRewardIconOutput($url)
    {
        return trim($url) ? '<img src="'.$url.'" height="84" border="0" alt="" />' : '';
    }
}
