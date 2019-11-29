<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class WhereNotNull implements CriterionInterface
{
    protected $column;

    public function __construct($column)
    {
        $this->column = $column;
    }

    public function apply($entity)
    {
        return $entity->whereNotNull($this->column);
    }
}
