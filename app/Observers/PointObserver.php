<?php

namespace App\Observers;

use App\Events\CustomerHaveEnoughPointsForReward;
use App\Events\CustomerSpentPoints;
use App\Models\MerchantReward;
use App\Models\Point;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\MerchantRewardRepository;
use App\Repositories\Contracts\PointRepository;
use App\Repositories\Contracts\TierSettingsRepository;
use App\Repositories\Eloquent\Criteria\ByCustomer;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\HighestPointsRequiredFirst;
use Illuminate\Support\Facades\Log;

class PointObserver
{
    protected $tierSettings;

    protected $customers;

    protected $points;

    protected $merchantRewards;

    public function __construct(
        TierSettingsRepository $tierSettings,
        CustomerRepository $customers,
        PointRepository $points,
        MerchantRewardRepository $merchantRewards
    ) {
        $this->tierSettings = $tierSettings;
        $this->customers = $customers;
        $this->points = $points;
        $this->merchantRewards = $merchantRewards;
    }

    /**
     * Handle to the point "created" event.
     *
     * @param  \App\Models\Point $point
     *
     * @return void
     */
    public function created(Point $point)
    {
        // Update VIP Tier if achieved
        $this->checkTierForUpdate($point);

        // Get available reward and send email notification
        $this->checkAvailableReward($point);
    }

    /**
     * Handle the point "updated" event.
     *
     * @param  \App\Models\Point $point
     *
     * @return void
     */
    public function updated(Point $point)
    {
        //
    }

    /**
     * Handle the point "deleted" event.
     *
     * @param  \App\Models\Point $point
     *
     * @return void
     */
    public function deleted(Point $point)
    {
        //
    }

    protected function checkTierForUpdate(Point $point)
    {
        try {
            try {
                $tierSettings = $this->tierSettings->findWhereFirst([
                    'merchant_id' => $point->merchant_id,
                ]);
            } catch (\Exception $exception) {
                return;
            }
            if (! isset($tierSettings) || ! $tierSettings || strtolower($tierSettings->program_status) != 'enabled' || $tierSettings->requirement_type != 'points-earned') {
                Log::info('PointObserver: VIP System is disabled (for points)');

                return;
            }
            $isAdminAction = false;
            if ($point->isAdminAction()) {
                $isAdminAction = true;
            }
            try {
                app('customer_service')->updateTier($point->merchant_id, $point->customer_id, $isAdminAction);
            } catch (\Throwable $e) {
                //dd($e);
            }
        } catch (\Exception $exception) {
            Log::error('Point Create Listener: An error occurred: '.$exception->getMessage());
        }
    }

    protected function checkAvailableReward(Point $point)
    {
        try {
            $customer = $this->customers->find($point->customer_id);

            if ($customer) {

                $pointsBalance = $point->point_value;
                try {
                    $pointsBalance = $this->points->withCriteria([
                        new ByCustomer($customer->id),
                    ])->all()->sum('point_value');
                } catch (\Exception $e) {
                    Log::error('Can\'t get customer point balance. '.$e->getMessage());
                }

                $availableReward = null;

                try {
                    $availableReward = $this->merchantRewards->withCriteria([
                        new ByMerchant($customer->merchant_id),
                        new HighestPointsRequiredFirst(),
                    ])->findWhereFirst([
                        [
                            'points_required',
                            '<=',
                            $pointsBalance,
                        ],
                        'active_flag' => 1,
                        'type_id'     => MerchantReward::REWARD_TYPE_POINT,
                    ]);
                } catch (\Exception $e) {
                    // No reward available
                }


                if ($point->point_value <= 0) {
                    event(new CustomerSpentPoints($availableReward, $customer, $pointsBalance));
                    return;
                }

                if ($availableReward) {
                    event(new CustomerHaveEnoughPointsForReward($availableReward, $customer, $pointsBalance));
                }
            }
        } catch (\Exception $e) {
            Log::error('Point Create Listener: An error occurred on available reward checking: '.$e->getMessage());
        }
    }
}
