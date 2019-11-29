<?php

namespace App\Repositories\Eloquent;

use App\Models\Plan;
use App\Repositories\Contracts\PlanRepository;
use App\Repositories\RepositoryAbstract;

class EloquentPlanRepository extends RepositoryAbstract implements PlanRepository
{
    public function entity()
    {
        return Plan::class;
    }
}