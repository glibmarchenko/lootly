<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;
use Carbon\Carbon;

class CreatedBetween implements CriterionInterface
{

    protected $start;
    protected $end;

    public function __construct(Carbon $start, Carbon $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function apply($entity)
    {
        return $entity->whereBetween('created_at', [$this->start, $this->end]);
    }
}
