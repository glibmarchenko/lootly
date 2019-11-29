<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class WithCustomerName implements CriterionInterface
{
    public function apply($entity)
    {
        $tableName = $entity->getModel()->getTable();
        $query =
            "select `customers`.`name`
from `customers` where `customers`.`id` = `$tableName`.`customer_id`";
        return $entity->selectSub($query, 'customer_name');
    }
}
