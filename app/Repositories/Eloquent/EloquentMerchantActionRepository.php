<?php

namespace App\Repositories\Eloquent;

use App\Models\MerchantAction;
use App\Repositories\Contracts\MerchantActionRepository;
use App\Repositories\RepositoryAbstract;

class EloquentMerchantActionRepository extends RepositoryAbstract implements MerchantActionRepository
{

    public function entity()
    {
        return MerchantAction::class;
    }

    public function createPoint($merchantActionId, array $properties)
    {
        return $this->find($merchantActionId)->point()->create($properties);
    }

    public function updateOrCreate(array $conditions, array $data)
    {
        return $this->entity->updateOrCreate($conditions, $data);
    }

}
