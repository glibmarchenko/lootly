<?php

namespace App\Transformers;

use App\Models\Order;
use League\Fractal\TransformerAbstract;

class OrdersProfileTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Order $order)
    {
        return [
            'order_id' => $order->order_id,
            'index' => '#' . $order->id,
            'amount' => $order->total_price,
            'coupon' => $order->coupon->coupon_code,
            'date' => $order->created_at . '',
        ];
    }
}
