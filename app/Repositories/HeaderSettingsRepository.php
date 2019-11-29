<?php

namespace App\Repositories;

use App\Models\RewardSetting;
use App\Models\HeaderSetting;
use App\Models\MerchantReward;
use App\Repositories\Contracts\DisplaySettingsRepository;
use App\Repositories\RewardSettingsRepository;

use App\Services\Amazon\UploadFile;


class HeaderSettingsRepository
{
    private $baseQuery;

    public function __construct()
    {
        $this->baseQuery = HeaderSetting::query();
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
            return $merchantObj->reward_settings->header;
        }catch (\ErrorException $e){
            return new HeaderSetting();
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
     * create new or update HeaderSetting
     * @return App\Models\HeaderSetting|False
     */
    public function create($data, $RewardSetting = null, $merchant = null){
        $currentMerchant = $this->merchant->getCurrent();
        if(!isset($RewardSetting)){
            $currentRewardSetting = $this->rewardSettingsRepo->getCurrent($merchant);
        } else {
            $currentRewardSetting = $RewardSetting;
        }
        $headerModel = $this->getCurrent($merchant);
        if(empty($headerModel)){
            $headerModel = new HeaderSetting();
            $headerModel->reward_settings_id = $currentRewardSetting->id;
        }

        if($data['background_name'] != $headerModel->background_name) {
            $amazon = new UploadFile();
            try{
                $amazon->delete($this->getIconName());
            }catch(\Exception $e){
                dd($e);
            }
            $file = $data['background'];
            if ($file) {
                $icone_url = $amazon->upload($currentMerchant, $file, $headerModel->id);
            } else {
                $icone_url = '';
            }
            $headerModel->background_url = $icone_url;
        }

        $design = $data['design'];

        $headerModel->title = $data['title'];
        $headerModel->subtitle = $data['subtitle'];
        $headerModel->button1_text = $data['button1'];
        $headerModel->button2_text = $data['button2'];
        $headerModel->button1_link = $data['button1Link'];
        $headerModel->button2_link = $data['button2Link'];
        $headerModel->background_opacity = $data['background_opacity'];
        $headerModel->background_name = $data['background_name'];
        $headerModel->header_color = $design['color'];
        $headerModel->title_color = $design['titleColor'];
        $headerModel->subtitle_color = $design['subtitleColor'];
        $headerModel->button_color = $design['buttonColor'];
        $headerModel->button_text_color = $design['buttonTextColor'];
        $headerModel->title_font_size = intval($design['titleFontSize']);
        $headerModel->subtitle_font_size = intval($design['subtitleFontSize']);
        $headerModel->button_font_size = intval($design['buttonFontSize']);

        try {
            $headerModel->save();
            return $headerModel;
        } catch(\Exception $e){
            dd($e);
            return False;
        }
    }

    public function getIconName()
    {
        $header = $this->getCurrent();
        if(!isset($header)) {
            return '';
        }
        $split_path = explode('/', $header->background_url);
        $index = count($split_path);
        $icon_name = $split_path[$index - 1];

        return $icon_name;
    }
}
