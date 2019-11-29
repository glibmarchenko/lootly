<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class ByTier implements CriterionInterface
{
    protected $tierId;

    public function __construct($tierId)
    {
        $this->tierId = $tierId;
    }

    public function apply($entity)
    {
        return $entity->where(['tier_id' => $this->tierId]);
    }
}
