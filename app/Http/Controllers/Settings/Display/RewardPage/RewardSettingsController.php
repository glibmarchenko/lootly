<?php

namespace App\Http\Controllers\Settings\Display\RewardPage;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
use Auth;
use App\Http\Controllers\Controller;
use App\Models\RewardSetting;
use App\Repositories\MerchantRepository;
use App\Repositories\MerchantDetailRepository;
use App\Repositories\RewardSettingsRepository;
use App\Repositories\EarningSettingsRepository;
use App\Repositories\HeaderSettingsRepository;
use App\Repositories\HowItWorksSettingsRepository;
use App\Repositories\FaqSettingsRepository;
use App\Repositories\ReferralDisplaySettingsRepository;
use App\Repositories\VipSettingsRepository;
use App\Repositories\SpendingSettingsRepository;
use App\Repositories\TierSettingsRepository;
use App\Repositories\ReferralSettingsRepository;

class RewardSettingsController extends Controller
{
    public function __construct(
        MerchantRepository $merchantRepository,
        MerchantDetailRepository $merchantDetailRepository,
        RewardSettingsRepository $rewardSettingsRepository,
        EarningSettingsRepository $earningSettingsRepository,
        HeaderSettingsRepository $headerSettingsRepository,
        HowItWorksSettingsRepository $howItWorksSettingsRepository,
        FaqSettingsRepository $faqSettingsRepository,
        ReferralDisplaySettingsRepository $referralDisplaySettingsRepository,
        VipSettingsRepository $vipSettingsRepository,
        SpendingSettingsRepository $spendingSettingsRepository,
        TierSettingsRepository $tierSettingsRepository,
        ReferralSettingsRepository $referralSettingsRepository
    ) {
        $this->merchantRepo = $merchantRepository;
        $this->merchantDetailRepo = $merchantDetailRepository;
        $this->rewardSettingsRepo = $rewardSettingsRepository;
        $this->earningSettingsRepository = $earningSettingsRepository;
        $this->headerSettingsRepository = $headerSettingsRepository;
        $this->howItWorksSettingsRepository = $howItWorksSettingsRepository;
        $this->faqSettingsRepository = $faqSettingsRepository;
        $this->referralDisplaySettingsRepository = $referralDisplaySettingsRepository;
        $this->vipSettingsRepository = $vipSettingsRepository;
        $this->spendingSettingsRepository = $spendingSettingsRepository;
        $this->tierSettingsRepository = $tierSettingsRepository;
        $this->referralSettingsRepository = $referralSettingsRepository;

        $this->middleware('auth', ['except' => ['get_widgets_view']]);

        $this->middleware(function ($request, $next) {
            $this->currentMerchant = $this->merchantRepo->getCurrent();
            if (! $this->currentMerchant) {
                abort(401, 'Please add account (merchant)');
            }
            return $next($request);
        }, ['except' => ['get_widgets_view']]);

    }

    public function store(Request $request){
        //TODO: validation
        \Debugbar::startMeasure('store','Saving data');
        $merchant = $this->merchantRepo->getCurrent();
        $data = $request->all();
        $rewardSetting = $this->rewardSettingsRepo->create([
                'html' => $this->rewardSettingsRepo->setImageUrlTag($data['widgetsHTML']),
                'html_mode' => $data['htmlMode']], $merchant->id);
        $this->earningSettingsRepository->create($data['earning'], $rewardSetting, $merchant);
        $header = $this->headerSettingsRepository->create($data['header'], $rewardSetting, $merchant);
        $this->howItWorksSettingsRepository->create($data['howItWorks'], $rewardSetting, $merchant);
        $this->faqSettingsRepository->create($data['faq'], $rewardSetting, $merchant);
        $this->referralDisplaySettingsRepository->create($data['referral'], $rewardSetting, $merchant);
        $this->vipSettingsRepository->create($data['vip'], $rewardSetting, $merchant);
        $this->spendingSettingsRepository->create($data['spending'], $rewardSetting, $merchant);
        $rewardSetting->html = $this->rewardSettingsRepo->replaceImageUrlTag($rewardSetting->html, $header->background_url);
        $rewardSetting->save();
        \Debugbar::stopMeasure('store');
        return response()->json([
            'message' => 'Reward Page settings saved!',
            'image_url' => $header->background_url,
            'html' => $rewardSetting->html
        ], 200);
    }

    public function get_widgets_view($merchantApiKey = null) {

        if($merchantApiKey) {
            $merchantDetail = $this->merchantDetailRepo->findBy('api_key' , $merchantApiKey);
            if($merchantDetail) $merchant = $this->merchantRepo->find($merchantDetail->merchant_id);
        } else if(Auth::check()) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $data = [];

        if(!isset($merchant) || !$merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.RewardsPage'))){
            return '';
        }

        $data = $this->rewardSettingsRepo->get_data($merchant);
        $data['branding_font'] = $merchant->rewards_branding_page ? $merchant->rewards_branding_page->font : 'lato';

        $vipSettings = $this->tierSettingsRepository->get($merchant->id);
        $referralSettings = $this->referralSettingsRepository->getReferral($merchant);

        $data['referral']['program_status'] = $referralSettings ? $referralSettings->program_status : 0;
        $data['vip']['program_status'] = $vipSettings ? $vipSettings->program_status : 0;

        /** pulling selected actions */
        $selected_actions = []; 
        if(isset($data['earning'])){
            foreach($data['earning']->merchant_actions as $merchant_action){
                array_push($selected_actions, [
                    'id' => $merchant_action->id,
                    'icon' => $merchant_action->action_icon_name,
                    'title' => $merchant_action->action_name,
                    'points' => $merchant_action->reward_text
                ]);
            }
        }
        $data['selected_actions'] = $selected_actions;

        /** pulling vip tiers */        
        $vips = [];
        foreach($data['vips'] as $vip){
            array_push($vips, [
                'id' =>$vip->i,
                'name' => $vip->name,
                'icon_url' => $vip->image_url,
                'icon_name' => $vip->image_name,
                'icon_color' => $vip->default_icon_color,
                'multiplier' =>$vip->multiplier,
                'text' => $vip->requirement_text
            ]);
        }
        $data['vips'] = $vips;

        /** pulling selected rewards */
        $selected_rewards = [];
        if(isset($data['spending'])){
            foreach($data['spending']->merchant_rewards as $merchant_reward){
                array_push($selected_rewards, [
                    'id' => $merchant_reward->id,
                    'icon' => $merchant_reward->reward_icon_name,
                    'title' => $merchant_reward->reward_text,
                    'points' => $merchant_reward->reward_name
                ]);
            }
        }
        $data['selected_rewards'] = $selected_rewards;
        if($data['faq']){
            $questions = $data['faq']->questions;
        }
        if(isset($questions)) {
            $data['questions'] = $questions;
        }
    
        return view('_widgets.rewards-page', ['data' => $data]);
    }

    public function get_settings(Request $request){
        $data = $this->rewardSettingsRepo->get_data();
        $data['merchant_details'] = $this->merchantRepo->getCurrent()->detail;
        return view('display.reward-page.settings', $data);
    }

    public function upgrade(){
        return view('display.reward-page.upgrade');
    }
}