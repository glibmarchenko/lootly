<?php

namespace App\Repositories\Contracts;

interface MerchantRewardRepository
{

    public function getTypeId($type);

    public function createPoint($merchantRewardId, array $properties);

}