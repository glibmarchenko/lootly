<?php

namespace App\Repositories;


use App\Contracts\Repositories\UserNotificationTypeRepository as UserNotificationTypeContract;
use App\Models\UserNotificationType;

class UserNotificationTypeRepository implements UserNotificationTypeContract
{
    public function all()
    {
        return UserNotificationType::all();
    }

    public function get()
    {
        return UserNotificationType::where('status', 1)->get();
    }

    public function getSlugList()
    {
        return UserNotificationType::where('status', 1)->get()->pluck('id', 'slug')->toArray();
    }

    public function find($id)
    {
        return UserNotificationType::where('id', $id)->first();
    }

}
