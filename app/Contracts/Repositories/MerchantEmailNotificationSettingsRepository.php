<?php

namespace App\Contracts\Repositories;

interface MerchantEmailNotificationSettingsRepository
{

    /**
     * @param $merchant
     * @return mixed
     */
    public function find($merchant);

    /**
     * @param $mercahnt
     * @param $data
     *
     * @return mixed
     */
    public function updateOrCreate($mercahnt, $data);

}
