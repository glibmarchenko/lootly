<?php

namespace App\Transformers;

use App\Models\PointSetting;
use League\Fractal\TransformerAbstract;

class PointSettingsTransformer extends TransformerAbstract
{
    protected $availableIncludes = [];

    public function transform(PointSetting $r)
    {
        return [
            'id'                    => $r->id,
            'name'                  => $r->name,
            'plural_name'           => $r->plural_name,
            'currency'              => $r->currency,
            'merchant_id'           => $r->merchant_id,
            'status'                => $r->status,
            'experient_after'       => $r->experient_after,
            'experient_status'      => $r->experient_status,
            'reminder_status'       => $r->reminder_status,
            'final_reminder_status' => $r->final_reminder_status,
            'reminder_day'          => $r->reminder_day,
            'final_reminder_day'    => $r->final_reminder_day,
            'created_at'            => $r->created_at,
            'updated_at'            => $r->updated_at,
        ];
    }
}