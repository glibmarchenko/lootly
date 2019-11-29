<?php

namespace App\Contracts\Repositories;

interface EmailNotificationRepository
{

    /**
     * @param $type
     * @return mixed
     */
    public function findByType($type);


}
