<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;
use Carbon\Carbon;

class InPastYear implements CriterionInterface
{
    public function __construct()
    {
        //
    }

    public function apply($entity)
    {
        return $entity->where('created_at', '>=', Carbon::now()->subYear());
    }
}
