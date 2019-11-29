<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class SpentPoints implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity->where(function ($q) {
            $q->where(function ($q1) {
                $q1->whereNotNull('merchant_reward_id');
                $q1->whereNotNull('coupon_id');
                $q1->where([
                    'rollback' => 0,
                ]);
                $q1->where('point_value', '<', 0);
            });
        });
    }
}
