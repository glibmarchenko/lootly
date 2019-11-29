<?php

namespace App\Transformers;

use App\Models\TierHistory;
use League\Fractal\TransformerAbstract;

class VIPProfileTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(TierHistory $tierHistory)
    {
        return [
            'current' => $tierHistory->new_tier ? $tierHistory->new_tier->name : 'N/A',
            'previous' => $tierHistory->old_tier ? $tierHistory->old_tier->name : 'N/A',
            'date' => $tierHistory->created_at . ""
        ];
    }
}
