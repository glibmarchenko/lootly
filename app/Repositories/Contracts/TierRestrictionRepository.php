<?php

namespace App\Repositories\Contracts;

interface TierRestrictionRepository
{
    public function deleteWhereNotIn($column, array $values);

    public function updateOrCreate(array $conditions, array $data);
}