<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class SumByRelation implements CriterionInterface
{
    protected $relatedTable;
    protected $colName;
    protected $relationCol;
    protected $asCol;

    /**
     * Sum by $colName from table $relatedTable 
    */
    public function __construct($relatedTable, $colName, $asCol = null, $relationCol = null)
    {
        $this->relatedTable = $relatedTable;
        $this->colName = $colName;
        $this->relationCol = $relationCol;
        $this->asCol = $asCol ?? $colName;
    }

    public function apply($entity)
    {
        $tableName = $entity->getModel()->getTable();
        if(empty($this->relationCol)){
            $this->relationCol = \substr($tableName, 0, -1) . '_id';
        }
        $transformArray = [
            '$relTable' => $this->relatedTable,
            '$sumCol' => $this->colName,
            '$relCol' => $this->relationCol,
            '$curTable' => $tableName,
        ];
        $template = 'select sum(`$relTable`.`$sumCol`) from `$relTable` where `$relTable`.`$relCol` = `$curTable`.`id`';
        $query = \strtr($template, $transformArray);
        return $entity->selectSub($query, $this->asCol);
    }
}
