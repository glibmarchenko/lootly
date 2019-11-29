<?php

namespace App\Http\Controllers\Settings\referrals\rewards\receiver;


use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Referral\CreateRequest;
use App\Repositories\MerchantRepository;
use App\Repositories\MerchantRewardRepository;
use App\Repositories\RewardRepository;
use App\Models\MerchantReward;
use Symfony\Component\HttpFoundation\Request;


class RewardController extends Controller
{
    private $rewardTypeId;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(RewardRepository $rewardRepository, MerchantRewardRepository $merchantRewardRepository,
                                MerchantRepository $merchantRepository)
    {
        $this->middleware('auth');
        $this->rewardRepository = $rewardRepository;
        $this->merchantRepository = $merchantRepository;
        $this->merchantRewardRepository = $merchantRewardRepository;
        $this->rewardTypeId = MerchantReward::REWARD_TYPE_REFERRAL_RECEIVER;
    }

    public function get(Request $request)

    {
        $merchantObj = $this->merchantRepository->getCurrent();
        if (!$merchantObj) {
            return response()->json([
                'status' => 404,
                'message' => 'Please add account(merchant)'
            ]);
        }
        $name=$request->name;
        $rewards = $this->rewardRepository->findByRewardTypeId ($merchantObj,$name,$this->rewardTypeId);
        return response()->json([
            'rewards' => $rewards
        ]);
    }

    public function store(CreateRequest $request)
    {
        $data = $request->all();
        $merchantObj = $this->merchantRepository->getCurrent();
        if (!$merchantObj) {
            return response()->json([
                'status' => 404,
                'message' => 'Please add account(merchant)'
            ]);
        }

        $rewardObj = $this->rewardRepository->findByName($data['defaultRewardName']);
        $merchant_reward = $this->merchantRewardRepository->create($merchantObj, $rewardObj, $data,  $this->rewardTypeId );
        return response()->json([
            'merchant_reward' => $merchant_reward,
            'message' => 'Reward create successfully',
        ]);
    }

    public function deleteIcon($id)
    {
        return $this->merchantRewardRepository->deleteCustomIcon($id);

    }


    public function deleteMerchantReward($id){

        return $this->merchantRewardRepository->deleteMerchantReward($id);

    }

}
