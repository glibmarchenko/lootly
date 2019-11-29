<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;
use Carbon\Carbon;

class WithEarnedPointsInYear implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity->with([
            'earned_points_in_year' => function ($r) {
                $r->where(function ($q) {
                    $q->where(function ($q1) {
                        $q1->where('point_value', '>=', 0);
                        $q1->where([
                            'rollback' => 0,
                        ]);
                    });
                    $q->orWhere(function ($q1) {
                        $q1->where('point_value', '<', 0);
                        $q1->where([
                            'rollback' => 1,
                        ]);
                    });
                });
                $r->where('created_at', '>=', Carbon::now()->subYear());
            },
        ]);
    }
}
