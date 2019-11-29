<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class BetweenDates implements CriterionInterface
{
    protected $start;
    protected $end;
    protected $dateCol;

    public function __construct($start, $end, $dateCol = 'created_at')
    {
        $this->start = $start;
        $this->end = $end;
        $this->dateCol = $dateCol;
    }

    public function apply($entity)
    {
        $tableName = $entity->getModel()->getTable();
        if(isset($this->start) && isset($this->end)){
            return $entity
                ->where($tableName . '.' . $this->dateCol, '>', $this->start)
                ->where($tableName . '.' . $this->dateCol, '<', $this->end);
        }
        return $entity;
    }
}