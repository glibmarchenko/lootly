<?php

namespace App\Transformers;

use App\Models\Plan;
use League\Fractal\TransformerAbstract;

class PlanTransformer extends TransformerAbstract
{

    public function transform(Plan $item)
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'type' => $item->type,
            'price' => $item->price,
            'growth_order' => $item->growth_order,
            'features' => isset($item->features) ? $item->features : null,
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
        ];
    }

}