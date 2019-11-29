<?php

namespace App\Http\Controllers\Settings\Display\Widget;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Display\Widget\WidgetLoggedCreateRequest;
use App\Repositories\MerchantRepository;
use App\Repositories\WidgetSettingsRepository;
use App\Models\PaidPermission;

class WidgetLoggedSettingsController extends Controller
{

    private $widgetSettingsRepository;
    private $merchantRepository;

    public function __construct(WidgetSettingsRepository $widgetSettingsRepository, MerchantRepository $merchantRepository)
    {
        $this->widgetSettingsRepository = $widgetSettingsRepository;
        $this->merchantRepository = $merchantRepository;

        $this->middleware('auth');
    }

    public function editView() {

        $merchant = $this->merchantRepository->getCurrent();
        $points_settings = $merchant->points_settings;

        $have_customization_permissions = $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.AdvancedCustomization'));
        $customizations_upsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.AdvancedCustomization'));

        $have_referral_customization_permissions = $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.AdvancedReferralCustomization'));
        $referral_customizations_upsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.AdvancedReferralCustomization'));

        return view('display.widget.edit.logged-in' , compact(
            'points_settings',
            'have_customization_permissions',
            'customizations_upsell',
            'remove_branding',
            'have_referral_customization_permissions',
            'referral_customizations_upsell'
        ));
        
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

    public function store(WidgetLoggedCreateRequest $request)
    {
        $data = $request->all();

        $merchantObj = $this->merchantRepository->getCurrent();

        if (!$merchantObj) {

            return response()->json([
                'status' => 404,
                'message' => 'Please add account(merchant)'
            ], 404);
        }

        $widgetSettingsObj = $this->widgetSettingsRepository->createOrUpdateWidgetLoggedSettings($merchantObj, $data);

        return response()->json([
            'widget_settings' => $widgetSettingsObj,
            'message' => 'Settings saved successfully!',
        ]);

    }

}