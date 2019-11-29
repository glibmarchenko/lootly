<?php

namespace App\Repositories\Eloquent;

use App\Models\NotificationSettings;
use App\Repositories\Contracts\NotificationSettingsRepository;
use App\Repositories\RepositoryAbstract;

class EloquentNotificationSettingsRepository extends RepositoryAbstract implements NotificationSettingsRepository
{
    public function entity()
    {
        return NotificationSettings::class;
    }
}