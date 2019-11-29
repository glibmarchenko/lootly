<?php

namespace App\Repositories\Eloquent;

use App\Models\RewardCoupon;
use App\Repositories\Contracts\RewardCouponRepository;
use App\Repositories\RepositoryAbstract;

class EloquentRewardCouponRepository extends RepositoryAbstract implements RewardCouponRepository
{
    public function entity()
    {
        return RewardCoupon::class;
    }
}
