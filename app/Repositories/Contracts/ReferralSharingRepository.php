<?php

namespace App\Repositories\Contracts;

interface ReferralSharingRepository
{
    public function updateOrCreate(array $conditions, array $data);
}