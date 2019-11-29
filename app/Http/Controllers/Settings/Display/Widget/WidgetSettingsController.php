<?php

namespace App\Http\Controllers\Settings\Display\Widget;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Display\Widget\WidgetCreateRequest;
use App\Repositories\MerchantRepository;
use App\Repositories\WidgetSettingsRepository;
use App\Models\PaidPermission;

class WidgetSettingsController extends Controller
{

    private $widgetSettingsRepository;
    private $merchantRepository;

    public function __construct(WidgetSettingsRepository $widgetSettingsRepository, MerchantRepository $merchantRepository)
    {
        $this->widgetSettingsRepository = $widgetSettingsRepository;
        $this->merchantRepository = $merchantRepository;

        $this->middleware('auth');
    }

    public function get()
    {
        $merchantObj = $this->merchantRepository->getCurrent();

        if (!$merchantObj) {

            return response()->json([
                'status' => 404,
                'message' => 'Please add account(merchant)'
            ], 404);
        }

        $widgetSettingsObj = $this->widgetSettingsRepository->first($merchantObj);

        return response()->json([
            'widget_settings' => $widgetSettingsObj
        ]);
    }

    public function store(WidgetCreateRequest $request)
    {
        $data = $request->all();

        $merchantObj = $this->merchantRepository->getCurrent();

        if (!$merchantObj) {

            return response()->json([
                'status' => 404,
                'message' => 'Please add account(merchant)'
            ], 404);
        }

        $widgetSettingsObj = $this->widgetSettingsRepository->createOrUpdateWidgetSettings($merchantObj, $data);

        return response()->json([
            'widget_settings' => $widgetSettingsObj,
            'message' => 'Settings saved successfully!',
        ]);

    }

    public function editView()
    {
        $merchant = $this->merchantRepository->getCurrent();

        $default_store_links = ['/account/register', '/account/login'];

        if (count($merchant->integrations->where('status', 1)) > 0) {
            switch ($merchant->integrations->where('status', 1)[0]->slug) {
                case 'magento':
                    $default_store_links = ['/customer/account/create/', '/customer/account/login/'];
                    break;

                case 'woocommerce':
                    $default_store_links = ['/my-account/', '/my-account/'];
                    break;

                case 'bigcommerce':
                    $default_store_links = ['/login.php?action=create_account', '/login.php'];
                    break;

                default:
                    $default_store_links = ['/account/register', '/account/login'];
            }
        }

        $points_settings = $merchant->points_settings;
        $has_remove_branding_permissions = $merchant
            ->checkPermitionByTypeCode(\Config::get('permissions.typecode.RemoveLootlyBranding'));
        $branding_upsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.RemoveLootlyBranding'));

        $have_customization_permissions = $merchant
        ->checkPermitionByTypeCode(\Config::get('permissions.typecode.AdvancedCustomization'));
        $customizations_upsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.AdvancedCustomization'));
        
        $have_referral_customization_permissions = $merchant
        ->checkPermitionByTypeCode(\Config::get('permissions.typecode.AdvancedReferralCustomization'));
        $referral_customizations_upsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.AdvancedReferralCustomization'));

        $remove_branding = $this->widgetSettingsRepository->brandingStatus($merchant);

        return view('display.widget.edit.not-logged-in' , compact(
            'points_settings',
            'has_remove_branding_permissions',
            'branding_upsell',
            'have_customization_permissions',
            'customizations_upsell',
            'remove_branding',
            'have_referral_customization_permissions',
            'referral_customizations_upsell',
            'default_store_links'
        ));
    }

    public function tabView(){
        $merchant = $this->merchantRepository->getCurrent();
		$have_customization_permissions =  $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.AdvancedTabCustomization'));
        $customizations_upsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.AdvancedTabCustomization'));
        $plan = $merchant->plan();

        return view('display.widget.tab', compact(
            'plan',
            'have_customization_permissions',
            'customizations_upsell'
        ));
    }
}