<?php

namespace App\Repositories\Contracts;

interface MerchantActionRestrictionRepository
{
    public function deleteWhereNotIn($column, array $values);

    public function updateOrCreate(array $conditions, array $data);
}