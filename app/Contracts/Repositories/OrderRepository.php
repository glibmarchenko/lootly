<?php

namespace App\Contracts\Repositories;

interface OrderRepository
{

    /**
     * @param $id
     * @return mixed
     */
    public function find($id);


    /**
     * @param $customer
     * @param $data
     * @return mixed
     */
    public function create($customer, array $data = []);


    /**
     * @param $user
     * @param $points
     * @return mixed
     */
    public function update($orderId, array $data = []);


    /**
     * @param $user
     * @return mixed
     */
    public function delete($user);


}
