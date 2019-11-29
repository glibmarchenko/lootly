<?php

namespace App\Transformers;

use App\Models\Order;
use League\Fractal\TransformerAbstract;

class ReferralsActivityExportTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param Order $item
     * @return array
     */
    public function transform(Order $item)
    {
        return [
            'sender_id'          => $item->customer->id,
            'sender_name'        => $item->customer->name,
            'receiver_id'        => $item->referral->id,
            'receiver_name'      => $item->referral->name,
            'order_number'       => $item->order_id,
            'order_total'        => $item->total_price,
            'date'               => $item->created_at ? $item->created_at->format("Y-m-d H:i:s") : null,
        ];
    }
}
