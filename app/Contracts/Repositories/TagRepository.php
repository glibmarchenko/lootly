<?php

namespace App\Contracts\Repositories;

interface TagRepository
{

    /**
     * @param $merchantObj
     * @return mixed
     */
    public function all($merchantObj);


}
