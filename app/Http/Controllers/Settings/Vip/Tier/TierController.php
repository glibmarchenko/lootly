<?php

namespace App\Http\Controllers\Settings\Vip\Tier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Point\EditRequest;
use App\Repositories\MerchantRepository;
use App\Repositories\RewardRepository;
use App\Repositories\TierRepository;
use App\Repositories\CurrencyRepository;
use App\Repositories\TierSettingsRepository;
use App\Repositories\PointSettingsRepository;
use DB;
use App\Models\PaidPermission;

class  TierController extends Controller
{
    public $tierRepository;
    public $rewardRepository;
    public $merchantRepository;

   public $tierSettingsRepository;
   public $pointSettingsRepository;

    public function __construct( TierSettingsRepository $tierSettingsRepository,PointSettingsRepository $pointSettingsRepository, TierRepository $tierRepository, MerchantRepository $merchantRepository,
                                RewardRepository $rewardRepository)
    {
        $this->tierRepository = $tierRepository;
        $this->rewardRepository = $rewardRepository;
        $this->merchantRepository = $merchantRepository;
        $this->tierSettingsRepository=$tierSettingsRepository;
        $this->pointSettingsRepository=$pointSettingsRepository;

    }

    public function get()
    {   
        $merchant = $this->merchantRepository->getCurrent();
        $tiers = $this->tierRepository->get($merchant);

        $earingActions = $merchant->merchant_actions;
        $makePurchasePoints = $earingActions->where('action.url', 'make-a-purchase')->first()->point_value ?? 1;

        return view('vip.tiers.index', compact('tiers', 'merchant', 'makePurchasePoints'));
    }

    public function getTiers()
    {
        $merchantObj = $this->merchantRepository->getCurrent();
        $tiers = $this->tierRepository->get($merchantObj);
        
        return response()->json([
            'tiers' => $tiers,
        ]);
    }

    public function getById($id)
    {
        $merchant = $this->merchantRepository->getCurrent();
        $tier = $this->tierRepository->getById($id, $merchant->id);
        $activityResponse = $this->generateActivityResponse($tier);
        $currencies = $merchant->merchant_currency;
        $vipSetting = $this->tierSettingsRepository->get();
        $points_settings = $merchant->points_settings;
        $merchantReward = $this->rewardRepository->getMerchantRewards();
        $earingActions = $merchant->merchant_actions;
        $makePurchasePoints = $earingActions->where('action.url', 'make-a-purchase')->first()->point_value ?? 1;

        return response()->json([
            'tier' => $activityResponse,
            'currency' => $currencies,
            'vipSetting' => $vipSetting,
            'points_settings' => $points_settings,
            'merchantReward' => $merchantReward,
            'makePurchasePoints' => $makePurchasePoints
        ]);
    }

    public function getData()
    {
        $merchant = $this->merchantRepository->getCurrent();
        $currencies = $merchant->merchant_currency;
        $vipSetting = $this->tierSettingsRepository->get();
        $tiersList = $this->tierRepository->get($merchant);
        $point_name = $this->pointSettingsRepository->get($merchant)->toArray();
        $merchantReward = $this->rewardRepository->getMerchantRewards();
        $earingActions = $merchant->merchant_actions;
        $makePurchasePoints = $earingActions->where('action.url', 'make-a-purchase')->first()->point_value ?? 1;

        return response()->json([
            'currency' => $currencies,
            'tiers' => $tiersList,
            'vipSetting' => $vipSetting,
            'point' => isset($point_name) && count($point_name) ? $point_name[0] : [],
            'merchantReward' => $merchantReward,
            'makePurchasePoints' => $makePurchasePoints            
        ]);
    }

    public function generateActivityResponse($tier)
    {

        $tierItem = [
            'tier_id' => $tier['id'],
            'program' => [
                'status' => $tier['status'],
                'name' => $tier['name'],
                'emailText' => $tier['text_email'],
                'emailDefaultText' => $tier['text_email_default'],
                'reward_icon' => $tier['image_url'],
                'icon_name' => $tier['image_name'],
                'defaultIconColor' => $tier['default_icon_color'],
            ],
            'spend' => [
                'value' => $tier['spend_value'],
                'text' => $tier['requirement_text'],
                'defaultText' => $tier['requirement_text_default']
            ],
            'rolling_days' => '',
            'currency' => '',
            'points' => [
                'value' => $tier['multiplier'],
                'text' => $tier['multiplier_text'],
                'defaultText' => $tier['multiplier_text_default']
            ],
            'benefits' => [
                'entry' => [],
                'lifetime' => [],
                'custom' => []
            ],
            'restrictions' => [
                'status' => $tier['restrictions_enabled'],
                'customer' => [],
            ],
            'iconPreview' => '',
            'emailNotification' => true,

        ];
        foreach ($tier->tierBenefits as $benefit) {
            $tierItem['benefits'][$benefit->benefits_type][] = ['reward' => $benefit->benefits_reward, 'discount' => $benefit->benefits_discount];

        };
        $tierItem['rewards'] = $this->rewardRepository->getMerchantRewards();


        return $tierItem;
    }

    public function getEditPage($id)
    {
        $merchant = $this->merchantRepository->getCurrent();
        $tier = $this->tierRepository->getById($id, $merchant->id);
        $company_logo = env('DefaultCompanyLogo');
        $has_editor_permissions = $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.HTML_Editor'));
        $editor_upsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.HTML_Editor'));

        if (isset($merchant->email_notification_settings)){
            $company_logo = $merchant->email_notification_settings->company_logo;
        }

        return view('vip.tiers.edit-tier', compact('id', 'company_logo', 'merchant', 'has_editor_permissions', 'editor_upsell'));

    }

    public function edit(EditRequest $request)
    {
        $merchantObj = $this->merchantRepository->getCurrent();
        $request_data = $request->all();
        $newTier = $this->tierRepository->edit($merchantObj, $request_data);
        return $newTier;
    }

    public function add()
    {       
        $merchant = $this->merchantRepository->getCurrent();
        $points_settings = $merchant->points_settings;
        $company_logo = env('DefaultCompanyLogo');
        if (isset($merchant->email_notification_settings)){
            $company_logo = $merchant->email_notification_settings->company_logo ? $merchant->email_notification_settings->company_logo : env('DefaultCompanyLogo');
        }
        $has_editor_permissions = $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.HTML_Editor'));
        $editor_upsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.HTML_Editor'));
        return view('vip.tiers.add-tier', compact(
            'points_settings', 
            'merchant', 
            'company_logo',
            'has_editor_permissions',
            'editor_upsell'));
    }

    public function store(\App\Http\Requests\Settings\Vip\Tier\EditRequest $request)
    {
        $merchantObj = $this->merchantRepository->getCurrent();
        $request_data = $request->all();

        DB::beginTransaction();
        try {
            $newTier = $this->tierRepository->add($merchantObj, $request_data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false
            ]);
        }

        return response()->json([
            'success' => true
        ]);
    }

    public function update(\App\Http\Requests\Settings\Vip\Tier\EditRequest $request)
    {

        $merchantObj = $this->merchantRepository->getCurrent();
        $request_data = $request->all();

        DB::beginTransaction();
        try {
            $this->tierRepository->update($merchantObj, $request_data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true
        ]);
    }

    public function getReward()
    {
        $merchantReward = $this->rewardRepository->getMerchantRewards();
        return response()->json([
            'merchantReward' => $merchantReward
        ]);
    }

    public function deleteIcon($id)
    {
        return $this->tierRepository->deleteCustomIcon($id);

    }
}