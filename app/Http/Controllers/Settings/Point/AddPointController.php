<?php

namespace App\Http\Controllers\Settings\Point;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\PointRepository;


class AddPointController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PointRepository $pointRepositoryContract)
    {
        $this->pointRepository = $pointRepositoryContract;
        $this->middleware('auth');
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function visitLink($merchant_id, $customer_id)
    {
        $this->pointRepository->add($merchant_id, $customer_id);

        return redirect('http://visit');
    }

    /**
     * @param $merchant_id
     * @param $customer_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function shareSocial($merchant_id, $customer_id)
    {
        $this->pointRepository->add($merchant_id, $customer_id);

        return response()->json([
            'messages' => 'Success add point for share/link'
        ]);
    }

    /**
     * @param $merchant_id
     * @param $customer_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function review($merchant_id, $customer_id)
    {

        $this->pointRepository->add($merchant_id, $customer_id);

        return response()->json([
            'messages' => 'Success add point for Review'
        ]);
    }
}
