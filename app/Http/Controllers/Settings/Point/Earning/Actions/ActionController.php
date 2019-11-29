<?php

namespace App\Http\Controllers\Settings\Point\Earning\Actions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Point\Earning\CreateRequest;
use App\Repositories\ActionRepository;
use App\Repositories\MerchantActionRepository;
use App\Repositories\MerchantRepository;


class ActionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MerchantActionRepository $merchantActionRepository, MerchantRepository $merchantRepository,
                                ActionRepository $actionRepository)
    {
        $this->merchantActionRepository = $merchantActionRepository;
        $this->merchantRepository = $merchantRepository;
        $this->actionRepository = $actionRepository;

        $this->middleware('auth');
    }

    public function get($name)
    {
        $action = $this->actionRepository->findByName($name);
        return response()->json([
            'action' => $action
        ]);
    }

    public function store(CreateRequest $request)
    {
        $data = $request->all();

        $merchantObj = $this->merchantRepository->getCurrent();
        $actionObj = $this->actionRepository->findByName($data['defaultActionName']);

        if (!$merchantObj) {

            return response()->json([
                'status' => 404,
                'message' => 'Please add account(merchant)'
            ]);
        }
        $merchant_action = $this->merchantActionRepository->create($merchantObj, $actionObj, $data);

        return response()->json([
            'merchant_action' => $merchant_action,
            'message' => 'Action create successfully',
        ]);
    }

    public function deleteIcon($id)
    {

        return $this->merchantActionRepository->deleteCustomIcon($id);

    }
}
