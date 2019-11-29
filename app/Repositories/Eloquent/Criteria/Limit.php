<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class Limit implements CriterionInterface
{
    protected $limit;

    public function __construct($limit)
    {
        $this->limit = $limit;
    }

    public function apply($entity)
    {
        if(isset($this->limit)){
            return $entity->take($this->limit);
        }
        return $entity;
    }
}
