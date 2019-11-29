<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserNotificationType extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_notifications', 'user_notification_type_id', 'user_id')
            ->withPivot('active')
            ->withTimestamps();
    }
}
