<?php

namespace App\Transformers;

use App\Models\Tag;
use League\Fractal\TransformerAbstract;

class TagTransformer extends TransformerAbstract
{

    public function transform(Tag $tag)
    {
        //dd($customer);
        return [
            'id' => $tag->id,
            'merchant_id' => $tag->merchant_id,
            'name' => $tag->name,
            'created_at' => $tag->created_at,
            'updated_at' => $tag->updated_at,
        ];
    }

}