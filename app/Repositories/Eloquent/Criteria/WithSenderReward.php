<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class WithSenderReward implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity->with([
            'merchant.rewards' => function ($q) {
                $q->where('type_id', 2);
                $q->where('active_flag', 1);
            }
        ]);
    }
}
