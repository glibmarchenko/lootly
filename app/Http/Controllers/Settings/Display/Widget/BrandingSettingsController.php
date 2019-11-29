<?php

namespace App\Http\Controllers\Settings\Display\Widget;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Display\Widget\BrandingCreateRequest;
use App\Repositories\MerchantRepository;
use App\Repositories\WidgetSettingsRepository;
use App\Models\PaidPermission;

class BrandingSettingsController extends Controller
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

    public function store(BrandingCreateRequest $request)
    {
        $data = $request->all();

        $merchantObj = $this->merchantRepository->getCurrent();

        if (!$merchantObj) {

            return response()->json([
                'status' => 404,
                'message' => 'Please add account(merchant)'
            ], 404);
        }

        $widgetSettingsObj = $this->widgetSettingsRepository->createOrUpdateBrandingSettings($merchantObj, $data);

        return response()->json([
            'widget_settings' => $widgetSettingsObj,
            'message' => 'Settings saved successfully!',
        ]);

    }

    public function view(){
        $merchant = $this->merchantRepository->getCurrent();
        $has_remove_branding_permissions = $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.RemoveLootlyBranding'));
        $branding_upsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.RemoveLootlyBranding'));

        return view('display.widget.branding', compact(
            'merchant',
            'has_remove_branding_permissions',
            'branding_upsell'
        ));        
    }

}