<?php

namespace App\Http\Controllers\Settings\Point\Spending;

use App\Http\Controllers\Controller;
use App\Repositories\MerchantRepository;
use App\Repositories\MerchantRewardRepository;
use App\Repositories\RewardRepository;
use App\Models\MerchantReward;
use App\Models\PaidPermission;

class SpendingPointsController extends Controller
{
    private $rewardTypeId;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MerchantRepository $merchantRepository, MerchantRewardRepository $merchantRewardRepository,
                                RewardRepository $rewardRepository)
    {
        $this->merchantRepository = $merchantRepository;
        $this->rewardRepository = $rewardRepository;
        $this->merchantRewardRepository = $merchantRewardRepository;
        $this->middleware('auth');
        $this->rewardTypeId = MerchantReward::REWARD_TYPE_POINT;
    }


    public function getMerchantReward()
    {
        $merchantObj = $this->merchantRepository->getCurrent();
        $spendingRewards = $this->merchantRewardRepository->get($merchantObj,[$this->rewardTypeId]);

        $have_rewards_permissions = $this->merchantRepository
        ->getCurrent()
        ->checkPermitionByTypeCode(\Config::get('permissions.typecode.VariableDiscountCoupons'));
        $discount_upsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.VariableDiscountCoupons'));

        return view('points.spending.index', compact('spendingRewards', 'have_rewards_permissions', 'discount_upsell'));
    }

    public function getReward()
    {
        $merchantObj = $this->merchantRepository->getCurrent();
        $spendingRewards = $this->merchantRewardRepository->get($merchantObj,[$this->rewardTypeId]);

        return response()->json([
            'spendingRewards' => $spendingRewards
        ]);
    }

    public function getDefaultReward()
    {
        $merchantObj = $this->merchantRepository->getCurrent();
        $spendingRewardArr = $this->merchantRewardRepository->getType($merchantObj)->toArray();
        $rewards = $this->rewardRepository->get();
        $has_discount_permissions = $merchantObj->checkPermitionByTypeCode(\Config::get('permissions.typecode.VariableDiscountCoupons'));
        $discount_upsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.VariableDiscountCoupons'));

        return view('points.spending.rewards.index', compact(
            'rewards', 
            'spendingRewardArr',
            'has_discount_permissions',
            'discount_upsell'
        ));
    }

}
