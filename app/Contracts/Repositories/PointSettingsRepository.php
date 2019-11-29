<?php

namespace App\Contracts\Repositories;

interface PointSettingsRepository
{

    /**
     * @param $id
     * @return mixed
     */
    public function find($id);


    /**
     * @param array $data
     * @param $merchantObj
     * @return mixed
     */
    public function update(array $data, $merchantObj);


}
