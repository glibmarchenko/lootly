<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Merchant;
use App\Repositories\Contracts\MerchantRewardRepository;
use App\Repositories\Contracts\RewardRepository;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Transformers\RewardTransformer;
use App\Repositories\Contracts\MerchantRewardRestrictionRepository;
use App\Transformers\MerchantRewardRestrictionTransformer;
use Illuminate\Http\Request;

class MerchantRewardController extends Controller
{
    protected $rewards;

    protected $merchantRewards;

    protected $merchantRewardRestrictions;

    public function __construct(
        RewardRepository $rewards,
        MerchantRewardRepository $merchantRewards,
        MerchantRewardRestrictionRepository $merchantRewardRestrictions)
    {
        $this->rewards = $rewards;
        $this->merchantRewards = $merchantRewards;
        $this->merchantRewardRestrictions = $merchantRewardRestrictions;
    }

    public function get(Request $request, Merchant $merchant)
    {
        //
    }

    public function update(Request $request, Merchant $merchant)
    {
        //
    }

    public function delete(Request $request, Merchant $merchant, $rewardId)
    {
        $this->merchantRewards->withCriteria([
            new ByMerchant($merchant->id),
        ])->delete($rewardId);

        return response()->json([], 204);
    }

    public function getReceiverReward(Request $request, Merchant $merchant, $rewardId = null)
    {
        $type = trim($request->get('type'));
        $rewardId = intval($rewardId);

        $receiver_reward_type = $this->merchantRewards->getTypeId('referral_receiver');

        if (! $type) {
            return response()->json([
                'message' => 'Unknown receiver reward type',
            ], 404);
        }

        try {
            $reward = $this->rewards->withCriteria([
                new LatestFirst(),
            ])->findWhereFirst([
                'slug' => $type,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Reward not found',
                'error'   => $e->getMessage(),
            ], 403);
        }

        if ($rewardId) {
            try {
                $merchant_reward = $this->merchantRewards->withCriteria([
                    new LatestFirst(),
                    new ByMerchant($merchant->id),
                ])->findWhereFirst([
                    'id'        => $rewardId,
                    'reward_id' => $reward->id,
                    'type_id'   => $receiver_reward_type,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Merchant reward not found',
                    'error'   => $e->getMessage(),
                ], 403);
            }
            $reward->merchant_reward = $merchant_reward;
        } else {
            try {
                $merchant_reward = $this->merchantRewards->withCriteria([
                    new LatestFirst(),
                    new ByMerchant($merchant->id),
                ])->findWhereFirst([
                    'reward_id' => $reward->id,
                    'type_id'   => $receiver_reward_type,
                ]);
                $reward->merchant_reward = $merchant_reward;
            } catch (\Exception $e) {
                $reward->merchant_reward = null;
            }
        }

        return fractal($reward)->parseIncludes(['merchant_reward'])->transformWith(new RewardTransformer());
    }

    public function getRewardRestrictions(Request $request, Merchant $merchant, $rewardId)
    {
        $restrictions = $this->merchantRewardRestrictions->withCriteria([
            new ByMerchant($merchant->id),
        ])->findWhere(['merchant_reward_id' => $rewardId]);

        return fractal($restrictions)->transformWith(new MerchantRewardRestrictionTransformer())->toArray();
    }

    public function storeReceiverReward(Request $request, Merchant $merchant)
    {
        // ToDo check it
        dd($request->all());
        /*$data = $request->all();

        $rewardObj = $this->rewardRepository->findByName($data['defaultRewardName']);
        $merchant_reward = $this->merchantRewardRepository->create($merchantObj, $rewardObj, $data,  $this->rewardTypeId );
        return response()->json([
            'merchant_reward' => $merchant_reward,
            'message' => 'Reward create successfully',
        ]);*/
    }
}
