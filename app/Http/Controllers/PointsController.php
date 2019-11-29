<?php

namespace App\Http\Controllers;

use App\Repositories\MerchantRepository;
use App\Models\PointSetting;
use App\Models\PaidPermission;
use App\Models\MerchantEmailNotificationSettings;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Repositories\ReportsRepository;
use App\Transformers\PointsActivityViewTransformer;

class PointsController extends Controller
{

    public function __construct()
    {
		$this->reportsRepository = new ReportsRepository;
        $this->middleware('auth');
    }
    public function index()
	{
		return redirect()->route('points.overview');
	}
	
    public function overview()
	{
		return view('points.overview');	
	}

	/*
		@Earning Functions
		@Index, Actions .. etc
	*/

	public function earning()
	{
		return view('points.earning.index', $this->GetData('earning'));	
	}
	
	public function earningActions()
	{
		return view('points.earning.actions.index', $this->GetData('earning'));	
	}
	
	public function makePurchase()
	{
		return view('points.earning.actions.orders.make-purchase', $this->GetData('earning'));	
	}

	public function createAccount()
	{
		return view('points.earning.actions.account.create-account', $this->GetData('earning'));	
	}

	public function celebrateBirthday()
	{
		return view('points.earning.actions.account.celebrate-birthday', $this->GetData('earning'));
	}

	public function facebookLike()
	{
		return view('points.earning.actions.social.facebook-like', $this->GetData('earning'));	
	}

	public function facebookShare()
	{
		return view('points.earning.actions.social.facebook-share', $this->GetData('earning'));	
	}

	public function twitterFollow()
	{
		return view('points.earning.actions.social.twitter-follow', $this->GetData('earning'));
	}

	public function twitterShare()
	{
		return view('points.earning.actions.social.twitter-share', $this->GetData('earning'));
	}

    public function instagramFollow()
    {
        return view('points.earning.actions.social.instagram-follow', $this->GetData('earning'));
    }

	public function readContent()
	{
		return view('points.earning.actions.store.read-content', $this->GetData('earning'));	
	}

	public function trustspotReview()
	{
		return view('points.earning.actions.store.trustspot-review', $this->GetData('earning'));	
	}

    public function customEarning()
    {
        return view('points.earning.actions.custom.custom-earning', $this->GetData('earning'));
    }

	public function goalSpend()
	{
		return view('points.earning.actions.orders.goal-spend', $this->GetData('earning'));	
	}

	public function goalOrders()
	{
		return view('points.earning.actions.orders.goal-orders', $this->GetData('earning'));	
	}


	/*
		@Spending Functions
	*/
	
    public function spending()
	{	
		return view('points.spending.index');	
	}
	
    public function spendingRewards()
	{
		return view('points.spending.rewards.index');	
	}


    public function fixedDiscount()
	{
		return view('points.spending.rewards.fixed-discount', $this->GetData('spending'));
	}
    public function variableDiscount()
	{
		return view('points.spending.rewards.variable-discount', $this->GetData('spending'));
	}
    public function percentageDiscount()
	{
		return view('points.spending.rewards.percentage-discount', $this->GetData('spending'));
	}
    public function freeShipping()
	{
		return view('points.spending.rewards.free-shipping', $this->GetData('spending'));
	}
    public function freeProduct()
	{
		return view('points.spending.rewards.free-product', $this->GetData('spending'));
	}
	
	public function points()
	{
		return null;
	}

	/* ------- */
    public function activity()
	{
		$merchantRepository = new MerchantRepository;
		$merchant = $merchantRepository->getCurrent();

		$merchantActions = $merchant->merchant_actions;
		$merchantRewards = $merchant->rewards->filter(function($item){
			return $item->type_id == 1;
		});
		return view('points.activity', compact('merchantActions', 'merchantRewards'));
	}
	
    public function settings()
	{
		$merchantRepository = new MerchantRepository;
		$data['have_expiration_permissions'] = $merchantRepository
			->getCurrent()
			->checkPermitionByTypeCode(\Config::get('permissions.typecode.PointsExpiration'));
		$data['expiration_upsell'] = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.PointsExpiration'));
		
		return view('points.settings', $data);
	}

	private function GetData($action_type)
	{
		$merchantRepository = new MerchantRepository;
		$merchant = $merchantRepository->getCurrent();
		$company_logo = env('DefaultCompanyLogo');
		if (isset($merchant->email_notification_settings)){
			$company_logo = $merchant->email_notification_settings->company_logo;
		}
		$data['company'] = $merchant->name;
		$data['points_settings'] = $merchant->points_settings;
		$data['company_logo'] = $company_logo;
        $data['woocommerce'] = $merchant->integrations->filter(function ($integration, $key) {
            return $integration->slug === 'woocommerce';
        }) ? true : false;
        $data['zapier'] = $merchant->integrations->filter(function ($integration, $key) {
            return $integration->slug === 'zapier';
        })->count() ? true : false;

		$data['have_rest_permissions'] = $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.CustomerSegmentation'));
		$data['restrictions_upsell'] = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.CustomerSegmentation'));

		$data['has_editor_permissions'] = $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.HTML_Editor'));
		$data['editor_upsell'] = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.HTML_Editor'));

		if($action_type == 'spending'){ // data for spendings

			$data['have_email_permissions'] = $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.EmailSpendingCustomization'));
			$data['email_upsell'] = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.EmailSpendingCustomization'));

			$data['have_customization_permissions'] = $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.AdvancedSpendingCustomization'));
			$data['customizations_upsell'] = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.AdvancedSpendingCustomization'));

            $data['have_limits_permissions'] = $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.SpendingLimits'));
            $data['limits_upsell'] = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.SpendingLimits'));

			$data['id'] = $this->getRouteParam('id');
			if(empty($data['id'])){
				$data['id'] = -1;
			}
		} else {  // data for earnings

			$data['have_email_permissions'] = $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.EmailEarningCustomization'));
			$data['email_upsell'] = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.EmailEarningCustomization'));

			$data['have_customization_permissions'] = $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.AdvancedEarningCustomization'));
			$data['customizations_upsell'] = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.AdvancedEarningCustomization'));

			$data['have_limits_permissions'] = $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.EarningLimits'));
			$data['limits_upsell'] = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.EarningLimits'));
		}
		return $data;
	}
		
}
