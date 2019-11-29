<?php

namespace App\Transformers;

use App\Models\Coupon;
use League\Fractal\TransformerAbstract;

class CouponTransformer extends TransformerAbstract
{
    public function transform(Coupon $coupon)
    {
        return [
            'coupon_code'        => $coupon->coupon_code,
            'created_at'         => $coupon->created_at,
            'customer_id'        => $coupon->customer_id,
            'customer_name'      => $coupon->customer->name ?? null,
            'id'                 => $coupon->id,
            'is_used'            => $coupon->is_used,
            'created_by'         => $coupon->created_by_customer_id,
            'merchant_id'        => $coupon->merchant_id,
            'merchant_reward_id' => $coupon->merchant_reward_id,
            'merchant_reward'    => isset($coupon->merchant_reward) ? $coupon->merchant_reward : null,
            'shop_coupon_id'     => $coupon->shop_coupon_id,
            'updated_at'         => $coupon->updated_at,
        ];
    }
}