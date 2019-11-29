<?php

namespace App\Repositories\Eloquent;

use App\Models\TierSettings;
use App\Repositories\Contracts\TierSettingsRepository;
use App\Repositories\RepositoryAbstract;

class EloquentTierSettingsRepository extends RepositoryAbstract implements TierSettingsRepository
{
    public function entity()
    {
        return TierSettings::class;
    }
}