<?php

namespace App\Http\Controllers\Settings\Point;

use App\Http\Controllers\Controller;
use App\Repositories\CustomerRepository;
use App\Repositories\MerchantActionRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\MerchantRewardRepository;
use App\Repositories\PointRepository;


class OverviewPointsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PointRepository $pointRepositoryContract, MerchantRepository $merchantRepository,
                                CustomerRepository $customerRepository, MerchantActionRepository $merchantActionRepository,
                                MerchantRewardRepository $merchantRewardRepository)
    {

        $this->pointRepository = $pointRepositoryContract;
        $this->merchantActionRepository = $merchantActionRepository;
        $this->merchantRewardRepository = $merchantRewardRepository;
        $this->merchantRepository = $merchantRepository;
        $this->customerRepository = $customerRepository;
        $this->middleware('auth');
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPoints()
    {

        $currentMerchantObj = $this->merchantRepository->getCurrent();
        $points = $this->merchantActionRepository->getTopEarningAction($currentMerchantObj);
        $rewards = $this->merchantRewardRepository->getTopSpendingReward($currentMerchantObj);
        // dd($rewards);
        $activity = $this->pointRepository->getLatestActivity($currentMerchantObj);
        return view('points.overview', compact('points', 'activity', 'rewards'));
    }


}
