<?php

namespace App\Repositories\Eloquent;

use App\Models\MerchantActionRestriction;
use App\Repositories\Contracts\MerchantActionRestrictionRepository;
use App\Repositories\RepositoryAbstract;

class EloquentMerchantActionRestrictionRepository extends RepositoryAbstract implements MerchantActionRestrictionRepository
{
    public function entity()
    {
        return MerchantActionRestriction::class;
    }

    public function deleteWhereNotIn($column, array $values)
    {
        $this->entity->whereNotIn($column, $values)->delete();
    }

    public function updateOrCreate(array $conditions, array $data)
    {
        return $this->entity->updateOrCreate($conditions, $data);
    }
}