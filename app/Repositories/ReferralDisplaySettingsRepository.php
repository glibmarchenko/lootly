<?php

namespace App\Repositories;

use App\Models\RewardSetting;
use App\Models\ReferralDisplaySetting;
use App\Models\MerchantReward;
use App\Repositories\Contracts\DisplaySettingsRepository;
use App\Repositories\RewardSettingsRepository;


class ReferralDisplaySettingsRepository
{
    private $baseQuery;

    public function __construct()
    {
        $this->baseQuery = ReferralDisplaySetting::query();
        $this->merchant = new MerchantRepository();
        $this->rewardSettingsRepo = new RewardSettingsRepository();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function getCurrent($merchantObj = null)
    {
        if(!isset($merchantObj)) {
            $merchantObj = $this->merchant->getCurrent();
        }
        try{
            return $merchantObj->reward_settings->referral;
        }catch (\ErrorException $e){
            return NULL;
        }catch (\Exception $e){
            return $e->message;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]|NULL
     */
    public function getByRewardSettingsId($rewardId){
        if(empty($rewardId)){
            return NULL;
        }
        return $this->baseQuery->where('reward_settings_id', '=', $rewardId)->get();
    }

    /**
     * create new or update ReferralDisplaySetting
     * @return App\Models\ReferralDisplaySetting|False
     */
    public function create($data, $RewardSetting = null, $merchant = null){
        if(!isset($RewardSetting)){
            $currentRewardSetting = $this->rewardSettingsRepo->getCurrent($merchant);
        } else {
            $currentRewardSetting = $RewardSetting;
        }
        $referralDisplayModel = $this->getCurrent($merchant);
        if(empty($referralDisplayModel)){
            $referralDisplayModel = new ReferralDisplaySetting();
            $referralDisplayModel->reward_settings_id = $currentRewardSetting->id;
        }

        $design = $data['design'];
        $referralDisplayModel->title = $data['title'];
        $referralDisplayModel->subtitle = $data['subtitle'];
        $referralDisplayModel->title_color = $design['titleColor'];
        $referralDisplayModel->subtitle_color = $design['subtitleColor'];
        $referralDisplayModel->title_font_size = $design['titleFontSize'];
        $referralDisplayModel->subtitle_font_size = $design['subtitleFontSize'];

        try {
            $referralDisplayModel->save();
            return $referralDisplayModel;
        } catch(\Exception $e){
            dd($e);
            return False;
        }
    }
}
