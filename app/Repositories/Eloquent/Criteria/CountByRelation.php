<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class CountByRelation implements CriterionInterface
{
    protected $relationName;
    protected $fieldName;

    /**
     * Creates new field $fieldName as count of relation $relationName
    */
    public function __construct($relationName, $fieldName = null)
    {
        $this->relationName = $relationName;
        $this->fieldName = $fieldName;
    }

    public function apply($entity)
    {
        if(empty($this->fieldName)){
            $entity->withCount($this->relationName);
        } else {
            $queryString = $this->relationName . ' as ' . $this->fieldName;
            $entity->withCount($queryString);
        }
        return $entity;
    }
}
