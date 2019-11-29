<?php

namespace App\Repositories;

use App\Merchant;
use App\Models\Coupon;
use Laravel\Spark\Contracts\Repositories\CouponRepository as CouponRepositoryContract;

class CouponRepository implements CouponRepositoryContract
{
    /**
     * @param string $code
     */
    public function canBeRedeemed($code)
    {
        // TODO: Implement canBeRedeemed() method.
    }

    /**
     * @param string $code
     */
    public function find($code)
    {
        // TODO: Implement find() method.
    }

    /**
     * @param mixed $billable
     */
    public function forBillable($billable)
    {
        // TODO: Implement forBillable() method.
    }

    /**
     * @param string $code
     */
    public function valid($code)
    {
        // TODO: Implement valid() method.
    }

    public function findInMerchant(Merchant $merchant, $discount_code)
    {
        return Coupon::where([
            'merchant_id' => $merchant->id,
            'coupon_code' => $discount_code,
        ])->orderBy('created_at', 'desc')->first();
    }
}
