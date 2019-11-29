<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class LowestSpendValueFirst implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity->orderBy('spend_value', 'asc');
    }
}
