<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class WithTierName implements CriterionInterface
{
    public function apply($entity)
    {
        $tableName = $entity->getModel()->getTable();
        $query =
            "select `tiers`.`name`
from `tiers` where `tiers`.`id` = `$tableName`.`tier_id`";

        return $entity->selectSub($query, 'vip_tier');
    }
}
