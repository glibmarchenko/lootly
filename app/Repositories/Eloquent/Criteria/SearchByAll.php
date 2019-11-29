<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class SearchByAll implements CriterionInterface
{
    protected $search;
    protected $columns;

    public function __construct(string $search, array $columns)
    {
        $this->search = $search;
        $this->columns = $columns;
    }

    public function apply($entity)
    {   
        $entity->where(function ($query) {
            foreach ($this->columns as $column){
                if(\strpos($column, '.') === false) {
                    $query->orWhere($column, 'LIKE', '%' . $this->search . '%');
                } else {
                    $columnData = \preg_split('/\./', $column);  // split on table name and column name
                    $query->orWhereHas($columnData[0], function($q) use ($columnData) {
                        $q->where($columnData[1], 'LIKE', '%' . $this->search . '%');
                    });
                }
            }
        });
        return $entity;
    }
}
