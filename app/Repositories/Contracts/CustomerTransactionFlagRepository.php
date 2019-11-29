<?php

namespace App\Repositories\Contracts;

interface CustomerTransactionFlagRepository
{
    public function updateOrCreate(array $conditions, array $data);
}