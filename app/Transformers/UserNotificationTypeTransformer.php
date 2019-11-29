<?php

namespace App\Transformers;

use App\Models\UserNotificationType;
use League\Fractal\TransformerAbstract;

class UserNotificationTypeTransformer extends TransformerAbstract
{

    public function transform(UserNotificationType $item)
    {
        return [
            'id' => $item->id,
            'slug' => $item->slug,
            'title' => $item->title,
            'description' => $item->description,
            'status' => $item->status ? true : false,
            'active_by_default:' => $item->active_by_default ? true : false,
            'active' => isset($item->pivot) ? ($item->pivot->active ? true : false) : false,
        ];
    }

}