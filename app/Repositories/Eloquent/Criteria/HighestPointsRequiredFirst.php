<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class HighestPointsRequiredFirst implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity->orderBy('points_required', 'desc');
    }
}
