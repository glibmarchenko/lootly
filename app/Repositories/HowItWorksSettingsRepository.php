<?php

namespace App\Repositories;

use App\Models\RewardSetting;
use App\Models\HowItWorksSetting;
use App\Models\MerchantReward;
use App\Repositories\Contracts\DisplaySettingsRepository;
use App\Repositories\RewardSettingsRepository;


class HowItWorksSettingsRepository
{
    private $baseQuery;

    public function __construct()
    {
        $this->baseQuery = HowItWorksSetting::query();
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
            return $merchantObj->reward_settings->how_it_works;
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
     * create new or update HowItWorksSetting
     * @return App\Models\HowItWorksSetting|False
     */
    public function create($data, $RewardSetting = null, $merchant = null){
        if(!isset($RewardSetting)){
            $currentRewardSetting = $this->rewardSettingsRepo->getCurrent($merchant);
        } else {
            $currentRewardSetting = $RewardSetting;
        }
        $howItWorksModel = $this->getCurrent($merchant);
        if(empty($howItWorksModel)){
            $howItWorksModel = new HowItWorksSetting();
            $howItWorksModel->reward_settings_id = $currentRewardSetting->id;
        }

        $design = $data['design'];
        $howItWorksModel->title = $data['title'];
        $howItWorksModel->steep1_text = $data['step1'];
        $howItWorksModel->steep2_text = $data['step2'];
        $howItWorksModel->steep3_text = $data['step3'];
        $howItWorksModel->title_color = $design['titleColor'];
        $howItWorksModel->steps_color = $design['stepsColor'];
        $howItWorksModel->title_font_size = intval($design['titleFontSize']);
        $howItWorksModel->steps_front_size = intval($design['stepsFontSize']);
        $howItWorksModel->circle_full_color = $design['circleFullColor'];
        $howItWorksModel->circle_empty_color = $design['circleEmptyColor'];
        $howItWorksModel->arrows_color = $design['arrowsColor'];
        try {
            $howItWorksModel->save();
            return $howItWorksModel;
        } catch(\Exception $e){
            dd($e);
            return False;
        }
    }
}
