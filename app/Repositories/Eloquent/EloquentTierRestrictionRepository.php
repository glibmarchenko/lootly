<?php

namespace App\Repositories\Eloquent;

use App\Models\TierRestriction;
use App\Repositories\Contracts\TierRestrictionRepository;
use App\Repositories\RepositoryAbstract;

class EloquentTierRestrictionRepository extends RepositoryAbstract implements TierRestrictionRepository
{
    public function entity()
    {
        return TierRestriction::class;
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