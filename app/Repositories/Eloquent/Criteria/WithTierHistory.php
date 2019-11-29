<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class WithTierHistory implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity->with([
            'tier_history' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }
        ]);
    }
}
