<?php

namespace App\Contracts\Repositories;

interface EmailBlacklistRepository
{

    /**
     * @param $merchantObj
     * @param $type
     * @return mixed
     */
    public function check($merchantObj, $type);

    /**
     * @param $data
     * @param $merchantObj
     * @return mixed
     */
    public function create($data, $merchantObj);


}
