<?php

namespace App\Transformers;

use App\Models\Order;
use League\Fractal\TransformerAbstract;

class ReferralOrdersProfileTransformer extends TransformerAbstract
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
            'referred' => $order->customer ? $order->customer->name : '',
            'referred_id' => $order->customer ? $order->customer->id : '', 
            'date' => $order->created_at . '',
        ];
    }
}
