<?php

namespace App\Repositories;

use Laravel\Spark\Notification;
use Laravel\Spark\Repositories\NotificationRepository as SparkNotificationRepository;

class NotificationRepository extends SparkNotificationRepository
{
    public function get()
    {
      return  Notification::query()
            ->whereNull('user_id')
            ->get();
    }
}
