<?php

namespace App\Transformers;

use App\Models\MerchantActionRestriction;
use App\Models\TierRestriction;
use League\Fractal\TransformerAbstract;

class TierRestrictionTransformer extends TransformerAbstract
{
    public function transform(TierRestriction $item)
    {
        return [
            'id'           => $item->id,
            'merchant_id'  => $item->merchant_id,
            'tier_id'      => $item->tier_id,
            'type'         => $item->type,
            'restrictions' => $item->restrictions,
            'created_at'   => $item->created_at,
            'updated_at'   => $item->updated_at,
        ];
    }
}