<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class UpdateLock implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity->lockForUpdate();
    }
}
