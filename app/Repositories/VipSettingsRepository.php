<?php

namespace App\Repositories;

use App\Models\RewardSetting;
use App\Models\VipSetting;
use App\Models\MerchantReward;
use App\Repositories\Contracts\DisplaySettingsRepository;
use App\Repositories\RewardSettingsRepository;


class VipSettingsRepository
{
    private $baseQuery;

    public function __construct()
    {
        $this->baseQuery = VipSetting::query();
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
            return $merchantObj->reward_settings->vip;
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
     * create new or update VipSetting
     * @return App\Models\VipSetting|False
     */
    public function create($data, $RewardSetting = null, $merchant = null){
        if(!isset($RewardSetting)){
            $currentRewardSetting = $this->rewardSettingsRepo->getCurrent($merchant);
        } else {
            $currentRewardSetting = $RewardSetting;
        }
        $vipModel = $this->getCurrent($merchant);
        if(empty($vipModel)){
            $vipModel = new VipSetting();
            $vipModel->reward_settings_id = $currentRewardSetting->id;
        }

        $design = $data['design'];
        $vipModel->title = $data['title'];
        $vipModel->title_color = $design['titleColor'];
        $vipModel->tier_name_color = $design['tierNameColor'];
        $vipModel->multiplier_color = $design['multiplierColor'];
        $vipModel->requirements_color = $design['requirementsColor'];
        $vipModel->title_font_size = intval($design['titleFontSize']);
        $vipModel->tier_name_font_size = intval($design['tierNameFontSize']);
        $vipModel->multiplier_font_size = intval($design['multiplierFontSize']);
        $vipModel->requirements_font_size = intval($design['requirementsFontSize']);

        try {
            $vipModel->save();
            return $vipModel;
        } catch(\Exception $e){
            dd($e);
            return False;
        }
    }
}
