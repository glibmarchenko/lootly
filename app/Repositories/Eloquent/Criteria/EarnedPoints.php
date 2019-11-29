<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class EarnedPoints implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity->where(function ($q) {
            $q->where(function ($q1) {
                // $q1->where('point_value', '>=', 0);
                $q1->whereNotNull('merchant_action_id');
                $q1->where([
                    'rollback' => 0,
                ]);
            });
            $q->orWhere(function ($q1) {
                $q1->where('point_value', '<', 0);
                $q1->where([
                    'rollback' => 1,
                ]);
            });
        });
    }
}
