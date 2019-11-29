<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class AdminAdjustPoints implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity
            ->whereNull('merchant_action_id')
            ->whereNull('merchant_reward_id');
    }
}
