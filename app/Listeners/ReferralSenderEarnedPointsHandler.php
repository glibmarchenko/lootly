<?php

namespace App\Listeners;

use App\Events\ReferralSenderEarnedPoints;
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

class ReferralSenderEarnedPointsHandler
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
        Log::info( 'construct ReferralSenderEarnedPointsHandler' );
        $this->customers = $customers;
        $this->merchants = $merchants;
        $this->pointSettings = $pointSettings;
        $this->points = $points;
        $this->merchantRewards = $merchantRewards;
    }

    /**
     * @param \App\Events\ReferralSenderEarnedPoints $event
     */
    public function handle(ReferralSenderEarnedPoints $event)
    {
        Log::info( 'handle started' );
        $customer = $event->customer;

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

        $pointSettings = $merchant->points_settings;
        if (! $pointSettings) {
            $pointSettings = $this->pointSettings->getDefaults();
        }

        Log::info( 'tags = ' );

        $tags = [
            '{points-earned-action}' => $event->merchantAction->reward_email_text,
            // !!! IMPORTANT TO BE ON TOP because it may include other tags !!!
            '{customer}'             => trim($customer->name) ?: 'Dear customer',
            '{points}'               => $event->point->point_value,
            '{points-name}'          => ($event->point->point_value == 1) ? $pointSettings->name : $pointSettings->plural_name,
            '{point-balance}'        => $pointsBalance,
            '{button-link}'          => $merchant->website,
            '{referral-name}'        => $event->referral->name,
        ];

        Log::info( 'send email' );
        // Send email notification to customer
        app('email_notification')->send('referral_sender_reward', $merchant, $customer->name, $customer->email, $tags);
    }
}
