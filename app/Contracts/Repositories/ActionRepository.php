<?php

namespace App\Contracts\Repositories;

interface ActionRepository
{


    /**
     * @param $name
     * @return mixed
     */
    public function findByName($name);

    /**
     * Get the current coupon for the given billable entity.
     *
     * @param  mixed $billable
     * @return mixed
     */
    public function get();
}
