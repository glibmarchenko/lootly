<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class WithUsedCoupons implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity->with([
            'coupons' => function ($q) {
                $q->where('coupons.is_used', 1);
            }
        ]);
    }
}
