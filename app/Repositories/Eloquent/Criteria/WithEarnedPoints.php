<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class WithEarnedPoints implements CriterionInterface
{
    public function apply($entity)
    {        
        $tableName = $entity->getModel()->getTable();
        $relatedField = \substr($tableName, 0, -1) . '_id';
        $query =
"select sum(`points`.`point_value`)
from `points` where `points`.`$relatedField` = `$tableName`.`id`
and ((`point_value` >= 0 and (`rollback` = 0)) or (`point_value` < 0 and (`rollback` = 1)))";
        return $entity->selectSub($query, 'earned_points');
    }
}
