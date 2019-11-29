<?php

namespace App\Contracts\Repositories;

interface UserMerchantRepository
{

    public function create($userObj, $merchantObj);


    public function get($id);



}
