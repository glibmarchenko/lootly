<?php

namespace App\Contracts\Repositories;

interface PaymentRepository
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
    public function create($charge_plan, $merchant_id);


    /**
     * @param $user
     * @param $points
     * @return mixed
     */
    public function update();


    /**
     * @param $user
     * @return mixed
     */
    public function delete($id);


}
