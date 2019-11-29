<?php

namespace App\Http\Controllers\Settings\Point;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Point\EditRequest;
use App\Repositories\MerchantRepository;
use App\Repositories\PointSettingsRepository;


class SettingsPointsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PointSettingsRepository $pointSettingsRepository, MerchantRepository $merchantRepository)
    {
        $this->pointSettingsRepository = $pointSettingsRepository;
        $this->merchantRepository = $merchantRepository;
    }

    /**
     * @param EditRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(EditRequest $request)
    {
        $merchantObj = $this->merchantRepository->getCurrent();
        $data = $request->all();
        $response = $this->pointSettingsRepository->update($data, $merchantObj);
        return \response()->json([
            'response' => $response,
            'message' => 'Success point settings'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editReminde(Request $request)
    {

        $merchantObj = $this->merchantRepository->getCurrent();
        $data = $request->all();
        $response = $this->pointSettingsRepository->updateReminder($data, $merchantObj);
        return \response()->json([
            'response' => $response,
            'message' => 'Success point settings'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateFinalReminder(Request $request)
    {

        $merchantObj = $this->merchantRepository->getCurrent();
        $data = $request->all();
        $response = $this->pointSettingsRepository->updateFinalReminder($data, $merchantObj);
        return \response()->json([
            'response' => $response,
            'message' => 'Success point settings'
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get()
    {
        $merchantObj = $this->merchantRepository->getCurrent();
        $points = $this->pointSettingsRepository->get($merchantObj)->toArray();
        return response()->json([
            'point' => $points,
        ]);
    }

    public function getName()
    {
        $merchantObj = $this->merchantRepository->getCurrent();
        $point_name = $this->pointSettingsRepository->get($merchantObj)->toArray();
        if ($point_name) {
            return response()->json([
                'point' => $point_name[0]
            ]);
        }
    }
}
