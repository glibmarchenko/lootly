<?php

namespace App\Http\Controllers\Settings\Display\Widget;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Display\Widget\TabCreateRequest;
use App\Repositories\MerchantRepository;
use App\Repositories\WidgetSettingsRepository;

class TabSettingsController extends Controller
{

    public $widgetSettingsRepository;
    public $merchantRepository;

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

    public function store(TabCreateRequest $request)
    {
        $data = $request->all();

        $merchantObj = $this->merchantRepository->getCurrent();

        if (!$merchantObj) {

            return response()->json([
                'status' => 404,
                'message' => 'Please add account(merchant)'
            ], 404);
        }

        $widgetSettingsObj = $this->widgetSettingsRepository->createOrUpdateTabSettings($merchantObj, $data);

        return response()->json([
            'widget_settings' => $widgetSettingsObj,
            'message' => 'Settings saved successfully!',
        ]);

    }

}