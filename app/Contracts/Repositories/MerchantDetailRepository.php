<?php

namespace App\Contracts\Repositories;

interface MerchantDetailRepository
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
    public function create($merchantObj);


    /**
     * @param $user
     * @param $points
     * @return mixed
     */
    public function update($shopifyAppObj, $merchantObj, $accessToken);


    /**
     * @param $user
     * @return mixed
     */
    public function delete($user);


}
