<?php

namespace App\Repositories;

use App\Models\RewardSetting;
use App\Models\EarnSetting;
use App\Models\MerchantReward;
use App\Models\MerchantAction;
use App\Repositories\Contracts\DisplaySettingsRepository;
use App\Repositories\RewardSettingsRepository;


class EarningSettingsRepository
{
    private $baseQuery;

    public function __construct()
    {
        $this->baseQuery = EarnSetting::query();
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
            return $merchantObj->reward_settings->earn;
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
     * create new or update EarnSetting
     * @return App\Models\EarnSetting|False
     */
    public function create($data, $RewardSetting = null, $merchant = null){
        if(!isset($RewardSetting)){
            $currentRewardSetting = $this->rewardSettingsRepo->getCurrent($merchant);
        } else {
            $currentRewardSetting = $RewardSetting;
        }
        $earnModel = $this->getCurrent($merchant);
        if(empty($earnModel)){
            $earnModel = new EarnSetting();
            $earnModel->reward_settings_id = $currentRewardSetting->id;
        }

        if(isset($data['selectedActions'])){
            $this->selectActions($earnModel, $data['selectedActions']);
        }

        $design = $data['design'];
        $earnModel->title = $data['title'];
        $earnModel->action_font_size = intval($design['actionFontSize']);
        $earnModel->action_text_color = $design['actionTextColor'];
        $earnModel->box_color = $design['boxColor'];
        $earnModel->point_color = $design['pointColor'];
        $earnModel->point_font_size = intval($design['pointFontSize']);
        $earnModel->ribbon_color = $design['ribbonColor'];
        $earnModel->title_color = $design['titleColor'];
        $earnModel->title_font_size = intval($design['titleFontSize']);
        try {
            $earnModel->save();
            return $earnModel;
        } catch(\Exception $e){
            return $e;
        }
    }

    public function selectActions($earnModel, array $actions_ids){
        MerchantAction::query()->where('earn_settings_id', '=', $earnModel->id) //remove relations to unselected actions
            ->whereNotIn('id', $actions_ids)
            ->update(['earn_settings_id' => null]);
        MerchantAction::query()->whereIn('id', $actions_ids)->update(['earn_settings_id' => $earnModel->id]);
    }
}
