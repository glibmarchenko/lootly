<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\MerchantReward;
use Carbon\Carbon;

class PopularSpendingRewardsTransformer extends TransformerAbstract
{
    protected $start;
    protected $end;

    public function __construct(\DatePeriod $period){
        $this->start = Carbon::instance($period->getStartDate())->startOfDay();
        $this->end = Carbon::instance($period->getStartDate()->add($period->getDateInterval()))->endOfDay();
    }

    /**
     * A Fractal transformer.
     *
     * @param MerchantReward $reward
     * @return array
     */
    public function transform(MerchantReward $reward)
    {
        $coupons = $reward->coupons;
        if(isset($coupons)){
            $coupons = $coupons
                ->where('created_at', '>', $this->start)
                ->where('created_at', '<', $this->end);

            return [
                'name' => $reward->getRewardDisplayNameAttribute(),
                'points_required' => $reward->points_required,
                'redemption_count' => $coupons->where('is_used', '=', 1)->count(),
                'reward_type' => $reward->reward_type,
                'rewards_issued' => $coupons->count(),
            ];
        }
        return [
            'name' => $reward->getRewardDisplayNameAttribute(),
            'points_required' => $reward->points_required,
            'redemption_count' => 0,
            'reward_type' => $reward->reward_type,
            'rewards_issued' => 0,
        ];
    }
}

