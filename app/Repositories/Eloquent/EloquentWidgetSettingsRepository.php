<?php

namespace App\Repositories\Eloquent;

use App\Models\WidgetSettings;
use App\Repositories\Contracts\WidgetSettingsRepository;
use App\Repositories\RepositoryAbstract;

class EloquentWidgetSettingsRepository extends RepositoryAbstract implements WidgetSettingsRepository
{
    public function entity()
    {
        return WidgetSettings::class;
    }

    public function getDefaults()
    {
        $entity = $this->entity();

        $default_settings = new $entity;
        $default_settings->fill($entity::DEFAULT_SETTINGS);
        return $default_settings;
    }
}