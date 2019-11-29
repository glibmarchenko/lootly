<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class ByMerchant implements CriterionInterface
{
    protected $merchantId;

    public function __construct($merchantId)
    {
        $this->merchantId = $merchantId;
    }

    public function apply($entity)
    {
        $tableName = $entity->getModel()->getTable();
        return $entity->where([$tableName . '.merchant_id' => $this->merchantId]);
    }
}
