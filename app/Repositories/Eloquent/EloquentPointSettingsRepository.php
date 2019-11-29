<?php

namespace App\Repositories\Eloquent;

use App\Models\PointSetting;
use App\Repositories\Contracts\PointSettingsRepository;
use App\Repositories\RepositoryAbstract;

class EloquentPointSettingsRepository extends RepositoryAbstract implements PointSettingsRepository
{
    public function entity()
    {
        return PointSetting::class;
    }

    public function getDefaults()
    {
        $entity = $this->entity();

        $default_settings = new $entity;
        $default_settings->fill($entity::DEFAULT_SETTINGS);
        return $default_settings;
    }
}