<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class SelectByFields implements CriterionInterface
{
    protected $fieldsArray;

    public function __construct (array $fields = null) {
        $this->fieldsArray = $fields;
    }

    public function apply($entity)
    {
        if(empty($this->fieldsArray)) {
            return $entity->select('*');
        }
        return $entity->select(...$this->fieldsArray);
    }
}
