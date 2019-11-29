<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class HasActiveIntegrationWhere implements CriterionInterface
{
    protected $conditions;

    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
    }

    public function apply($entity)
    {
        return $entity->whereHas('integrations', function ($q) {
            $q->where([
                'merchant_integrations.status' => 1,
                'integrations.status'          => 1,
            ]);
            $q->where($this->conditions);
        });
    }
}
