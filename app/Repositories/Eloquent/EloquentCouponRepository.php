<?php

namespace App\Repositories\Eloquent;

use App\Models\Coupon;
use App\Repositories\Contracts\CouponRepository;
use App\Repositories\RepositoryAbstract;

class EloquentCouponRepository extends RepositoryAbstract implements CouponRepository
{
    public function entity()
    {
        return Coupon::class;
    }
}