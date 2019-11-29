<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class WithTrashedMerchantRewards implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity->with([
            'merchant_reward' => function ($q) {
                $q->withTrashed();
                $q->with(['reward']);
            }
        ]);
    }
}
