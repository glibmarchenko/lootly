<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class HasActionWithType implements CriterionInterface
{
    protected $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function apply($entity)
    {
        return $entity->whereHas('action', function ($q) {
            $q->where('type', $this->type);
        });
    }
}
