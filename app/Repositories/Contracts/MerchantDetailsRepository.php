<?php

namespace App\Repositories\Contracts;

interface MerchantDetailsRepository
{
    public function updateOrCreate(array $conditions, array $data);
}