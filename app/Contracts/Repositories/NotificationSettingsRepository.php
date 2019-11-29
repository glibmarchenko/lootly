<?php

namespace App\Contracts\Repositories;

interface NotificationSettingsRepository
{

    /**
     * @param $merchant
     * @param $type
     * @return mixed
     */
    public function findByType($merchant, $type);

    /**
     * @param $merchant
     * @param $data
     *
     * @return mixed
     */
    public function updateOrCreate($merchant, $data);

}
