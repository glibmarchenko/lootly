<?php

namespace App\Repositories;

use App\Models\RewardSetting;
use App\Models\MerchantReward;
use App\Models\PaidPermission;

use App\Repositories\Contracts\DisplaySettingsRepository;
use App\Repositories\EarningSettingsRepository;
use App\Repositories\HeaderSettingsRepository;
use App\Repositories\HowItWorksSettingsRepository;
use App\Repositories\FaqSettingsRepository;
use App\Repositories\ReferralDisplaySettingsRepository;
use App\Repositories\VipSettingsRepository;
use App\Repositories\SpendingSettingsRepository;
use App\Repositories\MerchantRewardRepository;
use App\Repositories\MerchantRepository;

class RewardSettingsRepository
{
    private $baseQuery;

    public function __construct(){
        $this->merchant = new MerchantRepository();
        $this->baseQuery = RewardSetting::query();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */

    public function getCurrent($merchantObj = null)
    {
        if(!isset($merchantObj)) {
            $merchant = new MerchantRepository();
            $merchantObj = $this->merchant->getCurrent();
        }
        try{
            return $merchantObj->reward_settings;
        }catch (\ErrorException $e){
            return new RewardSetting();
        }catch (\Exception $e){
            return $e->message;
        }
    }

    /**
     * @return App\Models\RewardSetting|False
     */
    public function create($data, $id = Null, $merchant = null){
        $rewardSettings = $this->getCurrent();
        if(!isset($rewardSettings)){
            $rewardSettings = new RewardSetting;
        }
        if(!$id){
            $rewardSettings->merchant_id = $merchant->id;
        } else {
            $rewardSettings->merchant_id = $id;
        }
        $rewardSettings->html = $data['html'];
        $rewardSettings->html_mode = $data['html_mode'];
        try {
            $rewardSettings->save();
            return $rewardSettings;
        } catch(\Exception $e){
            dd($e);
            return False;
        }
    }

    public function formatHtml($html = null, $merchant = null) {
        if(!isset($html)){
            $rewardSettings = $this->getCurrent($merchant);
            if(isset($rewardSettings)){
                $html = $rewardSettings->html;
            } else {
                return null;
            }
        }
        $patterns = ["/\n/", "/\r/", "/\t/"];
        $formatedHtml = preg_replace($patterns, "", $html);
        $formatedHtml = preg_replace("/\'/", "\\'", $formatedHtml);
        return $formatedHtml;
    }

    /**
     * Replace base64 code with @imageUrl@ tag
     * @param string
     * @return string
     */
    public function setImageUrlTag($html){
        return \preg_replace('/background:\s{0,5}url\((&quot;).{1745,}(&quot;)\)/',
                                'background: url($1@imageUrl@$2)', $html);
    }

    /**
     * Replace @imageUrl@ tag with image url
     * @param string
     * @return string
     */
    public function replaceImageUrlTag($html, $imageUrl){
        return \preg_replace('/\@imageUrl\@/', $imageUrl, $html);
    }

    public function get_data($merchant = null){ 

        if(!isset($merchant)) {
            $merchant = $this->merchant->getCurrent();
        }

        $earningSettingsRepository = new EarningSettingsRepository();
        $headerSettingsRepository = new HeaderSettingsRepository();
        $howItWorksSettingsRepository = new HowItWorksSettingsRepository();
        $faqSettingsRepository = new FaqSettingsRepository();
        $referralDisplaySettingsRepository = new ReferralDisplaySettingsRepository();
        $vipSettingsRepository = new VipSettingsRepository();
        $spendingSettingsRepository = new SpendingSettingsRepository();
        $merchantRewardRepository = new MerchantRewardRepository();

        $rewardSettings = $this->getCurrent($merchant);
        if(!isset($rewardSettings)){
            $rewardSettings = new RewardSetting();
        }

        $rewardSettings->html = $this->formatHtml($rewardSettings->html, $merchant);

        $has_editor_permissions = $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.HTML_Editor'));
        $editor_upsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.HTML_Editor'));

        return [
            'earning' => $earningSettingsRepository->getCurrent($merchant),
            'header' => $headerSettingsRepository->getCurrent($merchant),
            'how_it_works' => $howItWorksSettingsRepository->getCurrent($merchant),
            'faq' => $faqSettingsRepository->getCurrent($merchant),
            'referral' => $referralDisplaySettingsRepository->getCurrent($merchant),
            'vip' => $vipSettingsRepository->getCurrent($merchant),
            'spending' => $spendingSettingsRepository->getCurrent($merchant),
            'points' => $merchant->points_settings,
            'merchant_actions' => $merchant->merchant_actions,
            'merchant_rewards' => $merchant->rewards,
            'vips' => $merchant->tiers->where('status', 1),
            'receiver' => $merchantRewardRepository->getByTypeId(MerchantReward::REWARD_TYPE_REFERRAL_RECEIVER, $merchant->id),
            'sender' => $merchantRewardRepository->getByTypeId(MerchantReward::REWARD_TYPE_REFERRAL_SENDER, $merchant->id),
            'merchant_domain' => $merchant->website ? $merchant->website : '{domain-name}',
            'reward_settings' => $rewardSettings,
            'has_editor_permissions' => $has_editor_permissions,
            'editor_upsell' => $editor_upsell,
        ];
    }
}
