<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class HasCustomerWhere implements CriterionInterface
{
    protected $conditions;

    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
    }

    public function apply($entity)
    {
        return $entity->whereHas('customer', function ($q) {
            $q->where($this->conditions);
        });
    }
}
