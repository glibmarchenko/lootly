<?php

namespace App\Repositories;

use App\Models\ReferralSetting;
use App\Contracts\Repositories\ReferralSettingsRepository as ReferralSettingsRepositoryContract;

class ReferralSettingsRepository implements ReferralSettingsRepositoryContract
{
    public $baseQuery;

    public function __construct()
    {
        $this->baseQuery = ReferralSetting::query();
    }


    /**
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateReferral(array $data, $merchantObj)
    {

        $referralSettings = $this->baseQuery->updateOrCreate(
            ['merchant_id' => $merchantObj->id],
            [
                'referral_domain' => $data['customDomain'],
                'referral_link' => $data['url'],
                'program_status' => $data['programStatus'],
                'referral_domain_status' => $data['customDomainStatus'],

            ]);
        return $referralSettings;
    }

    public function getReferral($merchantObj)
    {
        return $this->baseQuery->where(['merchant_id' => $merchantObj->id])->first();
    }


}