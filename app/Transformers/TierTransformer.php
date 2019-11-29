<?php

namespace App\Transformers;

use App\Models\Tier;
use League\Fractal\TransformerAbstract;

class TierTransformer extends TransformerAbstract
{
    public function transform(Tier $item)
    {
        return [
            'id'                      => $item->id,
            'merchant_id'             => $item->merchant_id,
            'name'                    => $item->name,
            'currency'                => $item->name,
            'status'                  => $item->status,
            'text_email'              => $item->text_email,
            'requirement_text'        => $item->requirement_text,
            'email_notification'      => $item->email_notification,
            'multiplier_text'         => $item->multiplier_text,
            'multiplier_text_default' => $item->multiplier_text_default,
            'spend_value'             => $item->spend_value,
            'multiplier'              => $item->multiplier,
            'rolling_days'            => $item->rolling_days,
            'image_url'               => $item->image_url,
            'image_name'              => $item->image_name,
            'default_icon_color'      => $item->default_icon_color,
            'benefits'                => $item->tierBenefits ?? null,

            'restrictions_enabled' => $item->restrictions_enabled,
            'created_at'           => $item->created_at,
            'updated_at'           => $item->updated_at,
        ];
    }
}