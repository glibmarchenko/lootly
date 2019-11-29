<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class ValidOrders implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity->where(function ($q) {
            $q->whereNull('status');
            $q->orWhere(function($q1){
                $q1->where('status', '!=', 'voided');
                $q1->where('status', '!=', 'refunded');
            });
        });
    }
}
