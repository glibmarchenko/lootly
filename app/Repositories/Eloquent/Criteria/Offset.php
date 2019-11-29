<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class Offset implements CriterionInterface
{
    protected $offset;

    public function __construct($offset)
    {
        $this->offset = $offset;
    }

    public function apply($entity)
    {
        if(isset($this->offset)){
            return $entity->skip($this->offset);
        }
        return $entity;
    }
}