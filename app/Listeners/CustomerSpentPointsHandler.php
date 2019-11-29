<?php

namespace App\Listeners;

use App\Events\CustomerSpentPoints;
use App\Events\CustomerSpentPointsForReward;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\MerchantRewardRepository;
use App\Repositories\Contracts\PointRepository;
use App\Repositories\Contracts\PointSettingsRepository;
use App\Repositories\Eloquent\Criteria\ByCustomer;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\HighestPointsRequiredFirst;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class CustomerSpentPointsHandler
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
     * @param \App\Events\CustomerSpentPoints $event
     */
    public function handle(CustomerSpentPoints $event)
    {
        $customer = $event->customer;
        $availableReward = $event->merchantReward;

        if ($customer) {

            $resetLastAvailableRewardId = false;

            if ($customer->last_available_reward_id) {
                if (! $availableReward) {
                    $resetLastAvailableRewardId = true;
                }else {
                    try {
                        $lastAvailableReward = $this->merchantRewards->find($customer->last_available_reward_id);
                    } catch (\Exception $e) {
                        $resetLastAvailableRewardId = true;
                    }

                    if (isset($lastAvailableReward) && $lastAvailableReward) {
                        if ($availableReward->points_required < $lastAvailableReward->points_required) {
                            $resetLastAvailableRewardId = true;
                        }
                    }
                }
            }

            if ($resetLastAvailableRewardId) {
                $this->customers->clearEntity();

                $this->customers->update($customer->id, [
                    'last_available_reward_id' => null,
                ]);
            }
        }
    }
}
