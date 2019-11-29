<?php

namespace App\Contracts\Repositories;

interface RewardCouponRepository
{
    public function get();

    public function find($id);

    public function findAvailableByMerchantRewardId($merchantRewardId);

    public function countAvailableByMerchantRewardId($merchantRewardId);

    public function setRedeemed($id);
}
