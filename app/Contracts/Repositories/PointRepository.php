<?php

namespace App\Contracts\Repositories;

interface PointRepository
{
    /**
     * @param $id
     * @return mixed
     */
    public function find($id);


    /**
     * @param $user
     * @param $points
     * @return mixed
     */
    public function create($user,$customerObj, $points);


    /**
     * @param $user
     * @param $points
     * @return mixed
     */
    public function update($user, $points);


    /**
     * @param $user
     * @return mixed
     */
    public function delete($user);


    /**
     * @param $merchant_id
     * @param $customer_id
     * @return mixed
     */
    public function add($merchant_id, $customer_id);

    /**
     * @return mixed
     */
//    public function getByEvent($merchantObj);
}
