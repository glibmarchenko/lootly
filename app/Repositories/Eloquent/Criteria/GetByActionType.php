<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class GetByActionType implements CriterionInterface
{
    protected $actionType;

    /**
     * action type could be 'earning' or 'spending'
     */
    public function __construct($type) {
        $this->actionType = $type;
    }
    public function apply($entity) {
        $tableName = $entity->getModel()->getTable();
        if($this->actionType == 'earned') {
            return $entity->where(function ($query) use ($tableName) {
                $query->whereNotNull('merchant_action_id')
                    ->orWhere($tableName . '.point_value', '>', 0);
            });
        } elseif($this->actionType == 'spent') {
            return $entity->where(function ($query) use ($tableName) {
                $query->whereNotNull('merchant_reward_id')
                    ->orWhere($tableName . '.point_value', '<', 0);
            });
        } else {
            return $entity;
        }
    }
}
