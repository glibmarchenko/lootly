<?php

namespace App\Transformers;

use App\Models\Subscription;
use League\Fractal\TransformerAbstract;

class SubscriptionTransformer extends TransformerAbstract
{
    public function transform(Subscription $item)
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'quantity' => $item->quantity,
            'length' => $item->length,
            'status' => $item->status,
            'trial_ends_at' => $item->trial_ends_at
                ? $item->trial_ends_at->format(DATE_W3C)
                : null,
            'ends_at' => $item->ends_at
                ? $item->ends_at->format(DATE_W3C)
                : null,
            'trial_active' => $item->isTrial(),
        ];
    }
}
