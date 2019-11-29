<?php

namespace App\Transformers;

use App\Models\TierHistory;
use League\Fractal\TransformerAbstract;

class VipActivitiesTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(TierHistory $tierHistory)
    {
        return [
            'customer_id' => $tierHistory->customer->id,
            'name' => $tierHistory->customer->name,
            'activity' => $tierHistory->activity,
            'current_tier' => $tierHistory->new_tier ? $tierHistory->new_tier->name : null,
            'previous_tier' => $tierHistory->old_tier ? $tierHistory->old_tier->name : null,
            'date' => $tierHistory->created_at . ""
        ];
    }
}
