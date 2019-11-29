<?php

namespace App\Repositories;

use App\Models\RewardCoupon;
use App\Contracts\Repositories\RewardCouponRepository as RewardCouponRepositoryContract;

class RewardCouponRepository implements RewardCouponRepositoryContract
{
    public function get()
    {
        return RewardCoupon::all();
    }

    public function find($id)
    {
        return RewardCoupon::where('id', $id)->first();
    }

    public function findAvailableByMerchantRewardId($merchantRewardId)
    {
        return RewardCoupon::where([
            'merchant_reward_id' => $merchantRewardId,
            'status' => RewardCoupon::STATUS_AVAILABLE,
        ])->first();
    }

    public function countAvailableByMerchantRewardId($merchantRewardId)
    {
        return RewardCoupon::where([
            'merchant_reward_id' => $merchantRewardId,
            'status' => RewardCoupon::STATUS_AVAILABLE,
        ])->count();
    }

    public function setRedeemed($id)
    {
        return RewardCoupon::where(['id' => $id])
            ->update(['status' => RewardCoupon::STATUS_REDEEMED]);
    }
}
