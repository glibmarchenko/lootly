<?php

namespace App\Repositories\Eloquent;

use App\Models\CustomerTransactionFlag;
use App\Repositories\Contracts\CustomerTransactionFlagRepository;
use App\Repositories\RepositoryAbstract;

class EloquentCustomerTransactionFlagRepository extends RepositoryAbstract implements CustomerTransactionFlagRepository
{
    public function entity()
    {
        return CustomerTransactionFlag::class;
    }

    public function updateOrCreate(array $conditions, array $data)
    {
        return $this->entity->updateOrCreate($conditions, $data);
    }
}