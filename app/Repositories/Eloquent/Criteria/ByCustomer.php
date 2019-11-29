<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class ByCustomer implements CriterionInterface
{
    protected $customerId;

    public function __construct($customerId)
    {
        $this->customerId = $customerId;
    }

    public function apply($entity)
    {
        return $entity->where(['customer_id' => $this->customerId]);
    }
}
