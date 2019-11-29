<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class WhereMerchantIntegration implements CriterionInterface
{
    protected $conditions;

    protected $table_name = 'merchant_integrations';

    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
    }

    public function apply($entity)
    {
        $merchant_conditions = [];
        foreach ($this->conditions as $key => $value) {
            $merchant_conditions[$this->table_name.'.'.$key] = $value;
        }

        return $entity->where($merchant_conditions);
    }
}
