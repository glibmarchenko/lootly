<?php

namespace App\Transformers;

use App\Models\Order;
use League\Fractal\TransformerAbstract;

class ReferralActivityTransformer extends TransformerAbstract
{
    public function transform(Order $item)
    {
        return [
            'order_id'           => $item->id,
            'ecommerce_order_id' => $item->order_id,
            'total_price'        => $item->total_price,
            'coupon_id'          => $item->coupon_id,
            'customer_id'        => $item->customer->id,
            'customer_name'      => $item->customer->name,
            'referrer_id'        => $item->referral->id,
            'referrer_name'      => $item->referral->name,
            'created_at'         => $item->created_at,
            'created'            => $item->created_at ? $item->created_at->format("Y-m-d H:i:s") : null,
            'updated_at'         => $item->updated_at,
            'updated'            => $item->updated_at ? $item->updated_at->format("Y-m-d H:i:s") : null,
        ];
    }
}