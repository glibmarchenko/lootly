<?php

namespace App\Transformers;

use App\Models\MerchantRewardRestriction;
use League\Fractal\TransformerAbstract;

class MerchantRewardRestrictionTransformer extends TransformerAbstract
{
    public function transform(MerchantRewardRestriction $item)
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
