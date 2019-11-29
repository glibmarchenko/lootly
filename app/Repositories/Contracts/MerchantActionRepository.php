<?php

namespace App\Repositories\Contracts;

interface MerchantActionRepository
{
    public function createPoint($merchantActionId, array $properties);

    public function updateOrCreate(array $conditions, array $data);
}