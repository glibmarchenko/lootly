<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class OrderBy implements CriterionInterface
{
    protected $column;
    protected $direction;

    public function __construct($column, $direction)
    {
        $this->column = $column;
        $this->direction = $direction;
    }

    public function apply($entity)
    {
        $tableName = $entity->getModel()->getTable();
        if(!empty($this->column) && !empty($this->direction))
            return $entity->orderBy($this->column, $this->direction);
        
            return $entity;
    }
}