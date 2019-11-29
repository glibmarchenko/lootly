<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CouponService extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'coupon_service';
    }
}