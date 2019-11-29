<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class WithActionName implements CriterionInterface
{
    protected $actionName;
    protected $merchantId;

    /**
     * action type could be 'earning' or 'spending'
     */
    public function __construct($action, $merchantId) {
        $this->actionName = $action;
        $this->merchantId = $merchantId;
    }

    // public function apply($entity) {
    //     $tableName = $entity->getModel()->getTable();
    //     $query = $entity->leftJoin('merchant_actions', 'merchant_actions.id', '=', $tableName . '.merchant_action_id')
    //         ->where('merchant_actions.action_name', '=', $this->actionName);
    //     return $entity->leftJoin('merchant_rewards', 'merchant_rewards.id', '=', $tableName . '.merchant_reward_id')
    //         ->where('merchant_rewards.reward_name', '=', $this->actionName)
    //         ->union($query);
    // }

    public function apply($entity) {
        $tableName = $entity->getModel()->getTable();
        $actionsCount = \DB::table('merchant_actions')
            ->where('merchant_id', '=', $this->merchantId)
            ->where('action_name', '=', $this->actionName)->count();
        if($actionsCount != 0) {
            return $entity->leftJoin('merchant_actions', 'merchant_actions.id', '=', $tableName . '.merchant_action_id')
                ->where('merchant_actions.action_name', '=', $this->actionName);
        }

        $spendingCount = \DB::table('merchant_rewards')
            ->where('merchant_id', '=', $this->merchantId)
            ->where('reward_name', '=', $this->actionName)->count();
        if($spendingCount != 0) {
            return $entity->leftJoin('merchant_rewards', 'merchant_rewards.id', '=', $tableName . '.merchant_reward_id')
                ->where('merchant_rewards.reward_name', '=', $this->actionName);
        }
        return $entity;
    }
}
