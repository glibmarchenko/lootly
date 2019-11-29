<?php

namespace App\Http\Controllers\Settings\Vip;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\MerchantRepository;
use App\Repositories\TierSettingsRepository;


class  SettingsController extends Controller
{

    public function __construct(TierSettingsRepository $tierSettingsRepository, MerchantRepository $merchantRepository)
    {
        $this->tierSettingsRepository = $tierSettingsRepository;
        $this->merchantRepository = $merchantRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        return view('vip.settings');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get()
    {
        $vipSetting = $this->tierSettingsRepository->get();

        return response()->json([
            'vipSetting' => $vipSetting
        ]);
    }

    /**
     * @param Request $request
     */
    public function edit(Request $request)
    {
        $request_data = $request->all();
        $tier_setting = $this->tierSettingsRepository->edit($request_data);
        return $tier_setting;
    }
}