<?php

namespace App\Repositories\Eloquent;

use App\Models\Reward;
use App\Repositories\Contracts\RewardRepository;
use App\Repositories\RepositoryAbstract;

class EloquentRewardRepository extends RepositoryAbstract implements RewardRepository
{
    public function entity()
    {
        return Reward::class;
    }
}