<?php

namespace App\Contracts\Repositories;

interface BillingRepository
{
    /**
     * @param $userObj
     * @param $data
     * @return mixed
     */
    public function add($userObj, $data);

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * @param $merchantObj
     * @return mixed
     */
    public function getByMerchant($merchantObj);

    /**
     * @param $userObj
     * @return mixed
     */
    public function get($userObj);
}
