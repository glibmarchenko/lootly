<?php

namespace App\Http\Controllers\Settings\Point\Spending\Rewards;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Point\Spending\CreateRequest;
use App\Repositories\MerchantRepository;
use App\Repositories\MerchantRewardRepository;
use App\Repositories\RewardRepository;
use App\Repositories\Contracts\MerchantRewardRestrictionRepository;
use App\Repositories\Contracts\TagRepository;
use App\Repositories\Contracts\TierRepository;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Models\MerchantReward;
use App\Helpers\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RewardController extends Controller
{
    private $rewardTypeId;

    private $couponService;

    protected $merchantRewardRestrictions;

    protected $tags;

    protected $tiers;

    public function __construct(
        RewardRepository $rewardRepository,
        MerchantRewardRepository $merchantRewardRepository,
        MerchantRepository $merchantRepository,
        CouponService $couponService,
        MerchantRewardRestrictionRepository $merchantRewardRestrictions,
        TagRepository $tags,
        TierRepository $tiers
    ) {
        $this->middleware('auth');
        $this->rewardRepository = $rewardRepository;
        $this->merchantRepository = $merchantRepository;
        $this->merchantRewardRepository = $merchantRewardRepository;
        $this->merchantRewardRestrictions = $merchantRewardRestrictions;
        $this->rewardTypeId = MerchantReward::REWARD_TYPE_POINT;
        $this->couponService = $couponService;
        $this->tags = $tags;
        $this->tiers = $tiers;
    }

    public function get($name)
    {
        $merchantObj = $this->merchantRepository->getCurrent();
        if (!$merchantObj) {
            return response()->json([
                'status' => 404,
                'message' => 'Please add account(merchant)'
            ]);
        }

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
        $rewardTypeId = MerchantReward::REWARD_TYPE_POINT;
        $rewardObj = $this->rewardRepository->findByName($data['program']['defaultRewardName']);
        $merchant_reward = $this->merchantRewardRepository->create($merchantObj, $rewardObj, $data, $rewardTypeId );
        $this->storeRestrictions( $merchantObj, $merchant_reward, $data );

        return response()->json([
            'merchant_reward' => $merchant_reward,
            'message' => 'Reward create successfully',
        ]);
    }

    private function storeRestrictions( $merchant, $merchant_reward, $data ) {

        $customerRestrictions = [
            'merchant_id'        => $merchant->id,
            'merchant_reward_id' => $merchant_reward->id,
            'type'               => 'customer',
            'restrictions'       => [],
        ];
        $productRestrictions = [
            'merchant_id'        => $merchant->id,
            'merchant_reward_id' => $merchant_reward->id,
            'type'               => 'product',
            'restrictions'       => [],
        ];

        // Validating customer restrictions
        if (isset($data['restrictions']['customer']) && count($data['restrictions']['customer'])) {
            $customer_restrictions = [];

            // Get Merchant Tags list
            $merchantTags = $this->tags->withCriteria([
                new ByMerchant($merchant->id),
            ])->all();
            $this->tags->clearEntity();
            // Get Merchant Tiers list
            $merchantTiers = $this->tiers->withCriteria([
                new ByMerchant($merchant->id),
            ])->all();
            foreach ($data['restrictions']['customer'] as $restriction) {
                if (! in_array(strtolower($restriction['conditional']), [
                    'has',
                    'has-none-of',
                    'equals',
                ])) {
                    continue;
                }
                $count_values = count( $restriction['values'] );
                if( strtolower($restriction['conditional']) == 'equals' ) {
                    $count_values = 1;
                }
                if ($restriction['type'] == 'customer-tags') {
                    $values = [];
                    // Check for new tags and create
                    for ($j = 0; $j < $count_values; $j++) {
                        $exists = false;
                        for ($i = 0; $i < count($merchantTags); $i++) {
                            if (strtolower($merchantTags[$i]->name) == strtolower($restriction['values'][$j])) {
                                $values[] = $merchantTags[$i]->id;
                                $exists = true;
                                break;
                            }
                        }
                        if (! $exists) {
                            // create new tag
                            $newMerchantTag = $this->tags->create([
                                'merchant_id' => $merchant->id,
                                'name'        => $restriction['values'][$j],
                            ]);
                            if ($newMerchantTag) {
                                $values[] = $newMerchantTag->id;
                            }
                            $this->tags->clearEntity();
                        }
                    }
                    $customer_restrictions[] = [
                        'type'      => 'customer-tags',
                        'condition' => strtolower($restriction['conditional']),
                        'value'     => $values,
                    ];
                } else {
                    if ($restriction['type'] == 'vip-tier') {
                        $values = [];
                        // Check VIPs on existence and cleanup
                        for ($j = 0; $j < $count_values; $j++) {
                            for ($i = 0; $i < count($merchantTiers); $i++) {
                                if (strtolower($merchantTiers[$i]->name) == strtolower($restriction['values'][$j])) {
                                    $values[] = $merchantTiers[$i]->id;
                                    break;
                                }
                            }
                        }
                        $customer_restrictions[] = [
                            'type'      => 'vip-tier',
                            'condition' => strtolower($restriction['conditional']),
                            'value'     => $values,
                        ];
                    }
                }
            }
            $customerRestrictions['restrictions'] = $customer_restrictions;
        }

        // Validating product restrictions
        if (isset($data['restrictions']['product']) && count($data['restrictions']['product'])) {
            $product_restrictions = [];

            foreach ($data['restrictions']['product'] as $restriction) {
                if (! in_array(strtolower($restriction['conditional']), [
                    'has',
                    'has-none-of',
                    'equals',
                ])) {
                    continue;
                }

                if (! in_array(strtolower($restriction['type']), [
                    'product-id',
                    'collection',
                ])) {
                    continue;
                }

                $values = $restriction['values'];

                if( $restriction['conditional'] == 'equals' ) {
                    $new_values = [];
                    $new_values[] = $values[0];
                    $values = $new_values;
                }

                $product_restrictions[] = [
                    'type'      => strtolower($restriction['type']),
                    'condition' => strtolower($restriction['conditional']),
                    'value'     => $values,
                ];
            }
            $productRestrictions['restrictions'] = $product_restrictions;
        }

        $this->merchantRewardRestrictions->updateOrCreate([
            'merchant_id'        => $merchant->id,
            'merchant_reward_id' => $merchant_reward->id,
            'type'               => 'customer',
        ], $customerRestrictions);
        $this->merchantRewardRestrictions->clearEntity();
        $this->merchantRewardRestrictions->updateOrCreate([
            'merchant_id'        => $merchant->id,
            'merchant_reward_id' => $merchant_reward->id,
            'type'               => 'product',
        ], $productRestrictions);
        $this->merchantRewardRestrictions->clearEntity();
    }

    public function getCoupons($rewardId)
    {
        // @todo move to repo
        $coupons = $this->merchantRewardRepository->getCoupons($rewardId);

        // @todo create transformer
        return $coupons;
    }

    public function importCoupons(Request $request, $rewardId)
    {
        $request->validate([
            'importFile' => 'base64size:5120',
        ]);

        try {
            $coupons = $this->couponService->uploadCoupons($request);

            $this->merchantRewardRepository->storeCoupons($rewardId, $coupons);

        } catch (\Exception $exception) {
            return response()->json([
                'errors' => [
                    'importFile' => 'Invalid file content: ' . $exception->getMessage()
                ]
            ], 422);
        }
    }

    public function deleteIcon($id)
    {
        return $this->merchantRewardRepository->deleteCustomIcon($id);
    }
}
