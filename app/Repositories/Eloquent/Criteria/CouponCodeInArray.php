<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class CouponCodeInArray implements CriterionInterface
{
    protected $codes;

    public function __construct(array $codes)
    {
        $this->codes = $codes;
    }

    public function apply($entity)
    {
        return $entity->whereIn('coupon_code', $this->codes);
    }
}
