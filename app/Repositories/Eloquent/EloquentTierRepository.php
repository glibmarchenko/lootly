<?php

namespace App\Repositories\Eloquent;

use App\Models\Tier;
use App\Repositories\Contracts\TierRepository;
use App\Repositories\RepositoryAbstract;

class EloquentTierRepository extends RepositoryAbstract implements TierRepository
{
    public function entity()
    {
        return Tier::class;
    }
}