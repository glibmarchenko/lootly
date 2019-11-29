<?php

namespace App\Transformers;

use App\Models\TierHistory;
use League\Fractal\TransformerAbstract;

class VipActivityExportTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(TierHistory $tierHistory)
    {   
        $customer = $tierHistory->customer;
        return [
            'Customer ID' => $customer->id,
            'Customer Name' => $customer->name,
            'Activity' => $tierHistory->activity,
            'Current Tier' => $tierHistory->new_tier ? $tierHistory->new_tier->name : null,
            'Previous Tier' => $tierHistory->old_tier ? $tierHistory->old_tier->name : null,
            'Date Joined VIP' => $customer->tier_history->sortBy('created_at')->first()->created_at,
            'Date Joined Current Tier' => $tierHistory->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
