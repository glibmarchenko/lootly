<?php

namespace App\Repositories;

use App\Models\RewardSetting;
use App\Models\SpendingSetting;
use App\Models\MerchantReward;
use App\Repositories\Contracts\DisplaySettingsRepository;
use App\Repositories\RewardSettingsRepository;


class SpendingSettingsRepository
{
    private $baseQuery;

    public function __construct()
    {
        $this->baseQuery = SpendingSetting::query();
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
            return $merchantObj->reward_settings->spending;
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
     * create new or update SpendingSetting
     * @return App\Models\SpendingSetting|False
     */
    public function create($data, $RewardSetting = null, $merchant = null){
        if(!isset($RewardSetting)){
            $currentRewardSetting = $this->rewardSettingsRepo->getCurrent($merchant);
        } else {
            $currentRewardSetting = $RewardSetting;
        }
        $spendingModel = $this->getCurrent($merchant);
        if(empty($spendingModel)){
            $spendingModel = new SpendingSetting();
            $spendingModel->reward_settings_id = $currentRewardSetting->id;
        }

        if(isset($data['selectedActions'])){
            $this->selectRewards($spendingModel, $data['selectedActions']);
        }

        $design = $data['design'];
        $spendingModel->title = $data['title'];
        $spendingModel->title_color = $design['titleColor'];
        $spendingModel->box_text_color = $design['boxTextColor'];
        $spendingModel->box_color = $design['boxColor'];
        $spendingModel->title_font_size = intval($design['titleFontSize']);
        $spendingModel->box_font_size = intval($design['boxFontSize']);

        try {
            $spendingModel->save();
            return $spendingModel;
        } catch(\Exception $e){
            dd($e);
            return False;
        }
    }

    public function selectRewards($spendingModel, array $rewards_ids){
        MerchantReward::query()->where('spending_settings_id', '=', $spendingModel->id) //remove relations to unselected actions
            ->whereNotIn('id', $rewards_ids)
            ->update(['spending_settings_id' => null]);
        MerchantReward::query()->whereIn('id', $rewards_ids)->update(['spending_settings_id' => $spendingModel->id]);
    }
}
