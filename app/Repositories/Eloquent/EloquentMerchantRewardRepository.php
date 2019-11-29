<?php

namespace App\Repositories\Eloquent;

use App\Models\MerchantReward;
use App\Repositories\Contracts\MerchantRewardRepository;
use App\Repositories\RepositoryAbstract;

class EloquentMerchantRewardRepository extends RepositoryAbstract implements MerchantRewardRepository
{

    public function entity()
    {
        return MerchantReward::class;
    }

    public function getTypeId($type)
    {
        return $this->entity->getTypeId($type);

    }

    public function createPoint($merchantRewardId, array $properties)
    {
        return $this->find($merchantRewardId)->point()->create($properties);
    }

}