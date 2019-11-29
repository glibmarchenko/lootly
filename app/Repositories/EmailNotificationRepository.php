<?php

namespace App\Repositories;


use App\Models\EmailNotification;
use App\Contracts\Repositories\EmailNotificationRepository as EmailNotificationRepositoryContract;


class EmailNotificationRepository implements EmailNotificationRepositoryContract
{
    public $baseQuery;

    public function __construct()
    {
        $this->baseQuery = EmailNotification::query();
    }

    public function findByType($type)
    {
        $item = $this->baseQuery
            ->where('alias', '=', $type)
            ->where('status', '=', true)
            ->first();

        return $item;
    }
}
