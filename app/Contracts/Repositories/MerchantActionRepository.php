<?php

namespace App\Contracts\Repositories;

interface MerchantActionRepository
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
    public function get($merchantObj);

    /**
     * @param array $data
     * @return mixed
     */
    public function create($merchantObj,  $actionObj, array $data);
}
