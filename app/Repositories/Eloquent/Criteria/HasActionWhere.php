<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class HasActionWhere implements CriterionInterface
{
    protected $conditions;

    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
    }

    public function apply($entity)
    {
        return $entity->whereHas('action', function ($q) {
            $q->where($this->conditions);
        });
    }
}
