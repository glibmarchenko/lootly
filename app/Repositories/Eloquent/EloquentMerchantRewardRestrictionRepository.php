<?php

namespace App\Repositories\Eloquent;

use App\Models\MerchantRewardRestriction;
use App\Repositories\Contracts\MerchantRewardRestrictionRepository;
use App\Repositories\RepositoryAbstract;

class EloquentMerchantRewardRestrictionRepository extends RepositoryAbstract implements MerchantRewardRestrictionRepository
{
    public function entity()
    {
        return MerchantRewardRestriction::class;
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
