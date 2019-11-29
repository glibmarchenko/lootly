<?php

namespace App\Listeners;

use App\Events\CustomerEarnedPointsForAction;
use App\Models\MerchantReward;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\MerchantRewardRepository;
use App\Repositories\Contracts\PointRepository;
use App\Repositories\Contracts\PointSettingsRepository;
use App\Repositories\Eloquent\Criteria\ByCustomer;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\OrderBy;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class CustomerEarnedPointsForActionHandler
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
     * @param \App\Events\CustomerEarnedPointsForAction $event
     */
    public function handle(CustomerEarnedPointsForAction $event)
    {
        $customer = $this->customers->find($event->point->customer_id);

        if ($customer) {

            $merchant = $this->merchants->withCriteria([
                new EagerLoad(['points_settings']),
            ])->find($customer->merchant_id);

            if (! $merchant->customer_accounts_enabled) {
                $this->merchants->clearEntity();
                $this->merchants->update($merchant->id, [
                    'customer_accounts_enabled' => 1,
                ]);
            }

            if (boolval($event->merchantAction->send_email_notification)) {

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

                $nextReward = null;

                try {
                    $this->merchantRewards->clearEntity();
                    $nextReward = $this->merchantRewards->withCriteria([
                        new ByMerchant($merchant->id),
                        new OrderBy('points_required', 'asc'),
                    ])->findWhereFirst([
                        [
                            'points_required',
                            '>',
                            intval($pointsBalance),
                        ],
                        'active_flag' => 1,
                        'type_id'     => MerchantReward::REWARD_TYPE_POINT,
                    ]);
                } catch (\Exception $e) {
                    // No reward available
                }

                $tags = [
                    '{points-earned-action}' => $event->merchantAction->reward_email_text,
                    // !!! IMPORTANT TO BE ON TOP because it may include other tags !!!
                    '{customer}'             => trim($customer->name) ?: 'Dear customer',
                    '{points}'               => $event->point->point_value,
                    '{points-name}'          => ($event->point->point_value == 1) ? $pointSettings->name : $pointSettings->plural_name,
                    '{point-balance}'        => $pointsBalance,
                    '{button-link}'          => $merchant->website.(strpos($merchant->website, '?') !== false ? '&' : '?').'lourl=earn-points',
                ];

                if ($nextReward) {
                    $tags['{next-reward}'] = $nextReward->reward_name;
                    $tags['{reward-icon}'] = $this->formatRewardIconOutput(app('email_notification_settings_service')->getRewardIconUrl($nextReward, 'reward-icon', 'points_earned'));
                    $tags['{need-points}'] = max(intval($nextReward->points_required) - intval($pointsBalance), 0);
                }

                // Send email notification to customer
                if ($event->merchantAction->action && $event->merchantAction->action->url !== 'create-account') {
                    app('email_notification')->send('points_earned', $merchant, $customer->name, $customer->email, $tags);
                }

                if ($event->merchantAction->zap_name && $merchant->integrations->filter(function ($integration) {
                        return $integration->slug == 'zapier';
                    })
                ) {
                    app('App\Services\Zapier\ZapierHook')->send($merchant, $event->merchantAction->zap_name, [
                        'email'  => $customer->email,
                        'points' => $pointsBalance,
                        'reason' => $event->merchantAction->action_name ?: ''
                    ]);
                }

            }
        }
    }

    protected function formatRewardIconOutput($url)
    {
        return trim($url) ? '<img src="'.$url.'" height="84" border="0" alt="" />' : '';
    }
}
