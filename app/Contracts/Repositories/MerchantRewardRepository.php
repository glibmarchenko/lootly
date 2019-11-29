<?php

namespace App\Contracts\Repositories;

interface MerchantRewardRepository
{


    /**
     * Get the coupon data for the given code.
     *
     * @param  string $code
     * @return mixed
     */
    public function find();

    /**
     * Get the current coupon for the given billable entity.
     *
     * @param  mixed $billable
     * @return mixed
     */
    public function get($merchantObj,$rewardTypeId);

    /**
     * Get all merchant rewards
     *
     * @param  mixed $merchant
     * @return mixed
     */
    public function all($merchant);

    /**
     * @param array $data
     * @return mixed
     */
    public function create($merchantObj,  $actionObj, array $data,$rewardTypeId);
}
