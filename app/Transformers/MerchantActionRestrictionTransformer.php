<?php

namespace App\Transformers;

use App\Models\MerchantActionRestriction;
use League\Fractal\TransformerAbstract;

class MerchantActionRestrictionTransformer extends TransformerAbstract
{
    public function transform(MerchantActionRestriction $item)
    {
        return [
            'id'                 => $item->id,
            'merchant_id'        => $item->merchant_id,
            'merchant_action_id' => $item->merchant_action_id,
            'type'               => $item->type,
            'restrictions'       => $item->restrictions,
            'created_at'         => $item->created_at,
            'updated_at'         => $item->updated_at,
        ];
    }
}