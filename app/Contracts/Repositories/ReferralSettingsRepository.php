<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/20/18
 * Time: 2:02 PM
 */

namespace App\Contracts\Repositories;


interface ReferralSettingsRepository
{

    /**
     * @param $merchantObj
     * @return mixed
     */
    public function getReferral($merchantObj);
    /**
     * @param array $data
     * @param $merchantObj
     * @return mixed
     */
    public function updateReferral(array $data, $merchantObj);
}