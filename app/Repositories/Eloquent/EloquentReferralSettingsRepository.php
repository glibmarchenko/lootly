<?php

namespace App\Repositories\Eloquent;

use App\Models\ReferralSetting;
use App\Repositories\Contracts\ReferralSettingsRepository;
use App\Repositories\RepositoryAbstract;

class EloquentReferralSettingsRepository extends RepositoryAbstract implements ReferralSettingsRepository
{
    public function entity()
    {
        return ReferralSetting::class;
    }
}