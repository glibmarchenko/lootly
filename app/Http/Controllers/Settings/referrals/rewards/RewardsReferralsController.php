<?php

namespace App\Http\Controllers\Settings\referrals\rewards;

use App\Http\Controllers\Controller;
use App\Repositories\MerchantRepository;
use App\Repositories\MerchantRewardRepository;
use App\Repositories\RewardRepository;
use App\Models\MerchantReward;
use Illuminate\Support\Facades\Auth;

class RewardsReferralsController extends Controller
{
    private $rewardSenderTypeId;
    private $rewardReceiverTypeId;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MerchantRepository $merchantRepository, MerchantRewardRepository $merchantRewardRepository,
                                RewardRepository $rewardRepository)
    {
        $this->middleware('auth');
        $this->merchantRepository = $merchantRepository;
        $this->rewardRepository = $rewardRepository;
        $this->merchantRewardRepository = $merchantRewardRepository;
        $this->rewardSenderTypeId = MerchantReward::REWARD_TYPE_REFERRAL_SENDER;
        $this->rewardReceiverTypeId = MerchantReward::REWARD_TYPE_REFERRAL_RECEIVER;
    }


    public function getMerchantReward()
    {
        $receiverRewardUrl=null;
        $senderRewardUrl=null;

        $senderReward=null;
        $receiverReward=null;


        $merchantObj = Auth::user()->currentTeam;
        $referralRewards = $this->merchantRewardRepository->get($merchantObj,[$this->rewardReceiverTypeId , $this->rewardSenderTypeId]);
        foreach ($referralRewards as $referralReward)
        {
            if( $this->rewardSenderTypeId==$referralReward->type_id){
                $senderReward= $referralReward;
                $senderRewardUrl=$referralReward->reward->url;

                switch ($referralReward->reward->url) {
                    case "free-product.get":
                            $senderRewardUrl="/referrals/rewards/sender/free-product-discount";
                        break;
                    case "free-shipping.get":
                        $senderRewardUrl="/referrals/rewards/sender/free-shipping-discount";
                        break;
                    case "percentage-discount.get":
                        $senderRewardUrl="/referrals/rewards/sender/percentage-discount";
                        break;
                    case "fixed-discount.get":
                        $senderRewardUrl="/referrals/rewards/sender/fixed-amount-discount";
                        break;
                    case "points.get":
                        $senderRewardUrl="/referrals/rewards/sender/points";
                        break;

                }


            }else{
                $receiverReward= $referralReward;
                switch ($referralReward->reward->url) {
                    case "free-product.get":
                        $receiverRewardUrl="/referrals/rewards/receiver/free-product-discount";
                        break;
                    case "free-shipping.get":
                        $receiverRewardUrl="/referrals/rewards/receiver/free-shipping-discount";
                        break;
                    case "percentage-discount.get":
                        $receiverRewardUrl="/referrals/rewards/receiver/percentage-discount";
                        break;
                    case "fixed-discount.get":
                        $receiverRewardUrl="/referrals/rewards/receiver/fixed-amount-discount";
                        break;
                }
            }
        }
        return \response()->json([
            'receiverRewardUrl'=>$receiverRewardUrl,
            'receiverReward'=> $receiverReward,

            'senderRewardUrl'=>$senderRewardUrl,
            'senderReward'=>$senderReward
        ]);





    }
    public function getDefaultRewardReceiver()
    {
        $rewards = $this->rewardRepository->get();
        return view('referrals.rewards.receiver.index', compact('rewards'));
    }
    public function getDefaultRewardSender()
    {
        $rewards = $this->rewardRepository->get();
        return view('referrals.rewards.sender.index', compact('rewards'));
    }
}
