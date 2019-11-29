<?php

namespace App\Helpers;

use App\Events\CustomerEarnedVipTier;
use App\Events\ReferralSenderEarnedPoints;
use App\Merchant;
use App\Models\Customer;
use App\Models\MerchantReward;
use App\Models\TierHistory;
use App\Models\TierSettings;
use App\Repositories\Contracts\CustomerReferralClickRepository;
use App\Repositories\Contracts\CustomerReferralShareRepository;
use App\Repositories\Contracts\MerchantActionRepository;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\MerchantRewardRepository;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\PointRepository;
use App\Repositories\Contracts\TierBenefitRepository;
use App\Repositories\Contracts\TierHistoryRepository;
use App\Repositories\Contracts\TierRepository;
use App\Repositories\Contracts\TierSettingsRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\Eloquent\Criteria\ByCustomer;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\CreatedBetween;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\EarnedPoints;
use App\Repositories\Eloquent\Criteria\HasActionWhere;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Repositories\Eloquent\Criteria\LowestSpendValueFirst;
use App\Repositories\Eloquent\Criteria\ValidOrders;
use App\Repositories\Eloquent\Criteria\AdminAdjustPoints;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CustomerService
{
    protected $merchantRewards;

    protected $tiers;

    protected $customers;

    protected $points;

    protected $tierHistory;

    protected $merchants;

    protected $tierSettings;

    protected $orders;

    protected $tierBenefits;

    protected $customerReferralShares;

    protected $customerReferralClicks;

    protected $merchantActions;

    public function __construct(
        MerchantRewardRepository $merchantRewards,
        TierRepository $tiers,
        \App\Repositories\Contracts\CustomerRepository $customers,
        PointRepository $points,
        TierHistoryRepository $tierHistory,
        MerchantRepository $merchants,
        TierSettingsRepository $tierSettings,
        OrderRepository $orders,
        TierBenefitRepository $tierBenefits,
        CustomerReferralShareRepository $customerReferralShares,
        CustomerReferralClickRepository $customerReferralClicks,
        MerchantActionRepository $merchantActions
    ) {
        $this->merchantRewards = $merchantRewards;
        $this->tiers = $tiers;
        $this->customers = $customers;
        $this->points = $points;
        $this->tierHistory = $tierHistory;
        $this->merchants = $merchants;
        $this->tierSettings = $tierSettings;
        $this->orders = $orders;
        $this->tierBenefits = $tierBenefits;
        $this->customerReferralShares = $customerReferralShares;
        $this->customerReferralClicks = $customerReferralClicks;
        $this->merchantActions = $merchantActions;
    }

    public function updateOrCreate(Merchant $merchant, array $customerStructure = [])
    {
        $customer = $this->customers->updateOrCreate([
            'merchant_id' => $merchant->id,
            'email'       => $customerStructure['email'],
        ], $customerStructure);

        if ($customer) {
            if ($customer->wasRecentlyCreated) {
                // Check reward for account creation
                // If active earn reward points
                $createAccountAction = $this->merchantActions->withCriteria([
                    new ByMerchant($merchant->id),
                    new EagerLoad(['action']),
                    new HasActionWhere([
                        'type' => 'Account',
                        'url'  => 'create-account',
                    ]),
                ])->findWhere([
                    'active_flag' => 1,
                ]);
                $this->merchantActions->clearEntity();

                Log::info('Checking create account action. Found: '.count($createAccountAction));
                if (count($createAccountAction)) {
                    try {
                        app('action_service')->validateAccountCreateActionAndCreditPoints($createAccountAction[0], $customer);
                    } catch (\Exception $exception) {
                        Log::error('Cannot credit points to customer #'.$customer->id.' for create-account action #'.$createAccountAction[0]->id.'. '.$exception->getMessage());
                    }
                }
            }
        }

        return $customer;
    }

    public function generateReferralSlug()
    {
        return uniqid("loot");
    }

    public function addRewardPoints(MerchantReward $merchantReward, Customer $customer)
    {
        if (! boolval($merchantReward->active_flag)) {
            return null;
        }

        $rewardType = $merchantReward->reward_type ?? null;

        $rewardName = $merchantReward->reward_text ?? '';

        $pointStructure = [
            'merchant_id' => $merchantReward->merchant_id,
            'customer_id' => $customer->id,
            'title'       => $rewardName,
            'type'        => $rewardType,
            'point_value' => intval($merchantReward->reward_value),
        ];

        $point = $this->merchantRewards->createPoint($merchantReward->id, $pointStructure);

        if ($point) {
            return $point;
        } else {
            return null;
        }
    }

    public function givePoints($merchantId, $customerId, $pointValue, $pointsData = [])
    {

        $pointStructure = array_merge($pointsData, [
            'merchant_id' => $merchantId,
            'customer_id' => $customerId,
            'point_value' => intval($pointValue),
        ]);

        $point = $this->points->create($pointStructure);

        if ($point) {
            return $point;
        } else {
            return null;
        }
    }

    public function updateTier($merchantId, $customerId, $adminAction = false)
    {
        try {
            $tierSettings = $this->tierSettings->findWhereFirst([
                'merchant_id' => $merchantId,
            ]);

        } catch (\Exception $exception) {
            return;
        }

        // Check is VIP program enabled
        if (! isset($tierSettings) || ! $tierSettings || ! $tierSettings->isProgramStatus()) {
            return;
        }

        try {
            $customer = $this->customers->find($customerId);

        } catch (\Exception $exception) {
            Log::error('CustomerService Error: Customer with id ' . $customerId . ' not found on updateTier()');

            return;
        }

        $this->customers->clearEntity();

        if (isset($customer) && $customer) {
            $previousTierInHistory = null;
            $historyActivities = [
                TierHistory::ACTIVITY_NEW_MEMBER,
                TierHistory::ACTIVITY_UPGRADED,
                TierHistory::ACTIVITY_DOWNGRADED,
                TierHistory::ACTIVITY_ADMIN_UPGRADED,
                TierHistory::ACTIVITY_ADMIN_DOWNGRADED,
            ];
            $historyActivityIndex = $adminAction ? 3 : 0;

            try {
                $historyRecord = $this->tierHistory->withCriteria([
                    new LatestFirst(),

                ])->findWhereFirst([
                    'customer_id' => $customerId,
                ]);

                if ($historyRecord) {
                    $previousTierInHistory = $historyRecord->new_tier_id;
                    $historyActivityIndex = $adminAction ? 3 : 1;
                }

            } catch (\Exception $exception) {
                // No tier history
                $historyRecord = null;
            }

            $this->tierHistory->clearEntity();

            // Get available tiers by merchant ID
            $tiers = $this->tiers->withCriteria([
                new ByMerchant($merchantId),
                new LowestSpendValueFirst(),
            ])->findWhere([
                'status' => 1,
            ]);

            $assignTier = null;

            $rollingPeriodType = null;
            $rollingPeriodNum = null;

            if (count($tiers)) {
                $requirementType = $tierSettings->requirement_type;

                $dataRangeCriteria = null;

                $rollingPeriodType = $tierSettings->getRollingPeriodType();
                $rollingPeriodNum = $tierSettings->getRollingPeriodNumber();

                if ($rollingPeriodType && $rollingPeriodNum) {
                    switch ($rollingPeriodType) {
                        case 'year':
                            $dataRangeCriteria = new CreatedBetween(Carbon::now()
                                ->subYears($rollingPeriodNum), Carbon::now());
                            break;

                        case 'month':
                            $dataRangeCriteria = new CreatedBetween(Carbon::now()
                                ->subMonths($rollingPeriodNum), Carbon::now());
                            break;
                    }
                }

                $spentAmount = 0;
                switch ($requirementType) {
                    case 'points-earned':
                        // Get earned points sum by customer ID
                        $points = $this->points->withCriteria([
                            new ByCustomer($customerId),
                            new EarnedPoints(),
                            $dataRangeCriteria,
                        ])->all();

                        $this->points->clearEntity();

                        $adminPoints = $this->points->withCriteria([
                            new ByCustomer($customerId),
                            new AdminAdjustPoints(),
                            $dataRangeCriteria,
                        ])->all();
                        $spentAmount = $points->sum('point_value') + $adminPoints->sum('point_value');
                        // dd($spentAmount, $adminPoints);
                        break;
                    case 'amount-spent':
                        // Get spent amount sum by customer ID

                        $orders = $this->orders->withCriteria([
                            new ByCustomer($customerId),
                            new ValidOrders(),
                            $dataRangeCriteria,
                        ])->all();

                        $spentAmount = $orders->sum('total_price');
                        break;
                }

                $maxTierSpendValue = 0;
                $assignTierObj = null;
                $previousTierObj = null;
                for ($i = 0; $i < count($tiers); $i++) {
                    $spend_value = intval($tiers[$i]->spend_value);
                    if ($spentAmount >= $spend_value && $spend_value >= $maxTierSpendValue) {
                        $assignTier = $tiers[$i]->id;
                        $assignTierObj = $tiers[$i];
                        $maxTierSpendValue = $spend_value;
                    }
                }
                if (! empty($previousTierInHistory)) {
                    $previousTierObj = $tiers->filter(function ($tier) use ($previousTierInHistory) {
                        return $tier->id == $previousTierInHistory;
                    })->first();
                }
            }

            if ($customer->tier_id != $assignTier) {
                if ($rollingPeriodType && $rollingPeriodNum) {
                    $isActivityUserChanges = $this->isActivityUserChanges($customer, $rollingPeriodType, $rollingPeriodNum);

                    if ($isActivityUserChanges) {
                        return;
                    }
                }

                // Set customer tier_id
                $this->customers->update($customerId, [
                    'tier_id' => $assignTier,
                ]);

                if (! empty($previousTierObj)) { // check if prev tier was higher
                    if (empty($assignTierObj)) {
                        $historyActivityIndex = $adminAction ? 4 : 2;
                    } elseif ($assignTierObj->spend_value < $previousTierObj->spend_value) {
                        $historyActivityIndex = $adminAction ? 4 : 2;
                    }
                }

                // Add record to customer's tier history
                $this->tierHistory->create([
                    'customer_id' => $customerId,
                    'new_tier_id' => $assignTier,
                    'old_tier_id' => $customer->tier_id,
                    'activity' => $historyActivities[$historyActivityIndex],
                ]);

                $this->tierHistory->clearEntity();

                // Check is this tier was achieved earlier
                $sameTiers = $this->tierHistory->withCriteria([
                    new ByCustomer($customerId),
                ])->findWhere([
                    'new_tier_id' => $assignTier,
                ]);
                if ($sameTiers->count() > 1 || $previousTierInHistory == $assignTier) {
                    // Rewards where given previously

                    if ($assignTier && isset($assignTierObj) && $assignTierObj) {
                        event(new CustomerEarnedVipTier($customer, $assignTierObj));
                    }

                    return;
                }

                // Give customer rewards for tier achieving
                $coupons = $this->giveTierBenefitRewards($assignTier, $merchantId, $customerId);

                if ($assignTier && isset($assignTierObj) && $assignTierObj) {
                    event(new CustomerEarnedVipTier($customer, $assignTierObj, $coupons));
                }
            }
        }
    }

    public function isActivityUserChanges(Customer $customer, string $rollingPeriodType, int $rollingPeriodNum): bool
    {
        $historyRecord = TierHistory::where(['customer_id' => $customer->id])
            ->orderBy('id', 'desc')
            ->first();

        if (! $historyRecord) {
            return false;
        }

        $dateNow = Carbon::now();
        $createdAt = $historyRecord->created_at;
        $rollingPeriod = null;

        if ($historyRecord->isActivityUserChanges()) {
            switch (strtolower($rollingPeriodType)) {
                case 'year':
                    $rollingPeriod = $createdAt->addYear($rollingPeriodNum);
                    break;
            }

            Log::info("VIP Rolling Check: dateNow=".$dateNow->getTimestamp().", rollingPeriod=".$rollingPeriod->getTimestamp());
            if ($rollingPeriod && $dateNow->getTimestamp() > $rollingPeriod->getTimestamp()) {
                return true;
            }
        }

        return false;
    }

    public function giveTierBenefitRewards($tierId, $merchantId, $customerId)
    {
        // Get tier benefits
        $benefits = $this->tierBenefits->withCriteria([
            new EagerLoad([
                'reward.reward',
                'tier',
            ]),
        ])->findWhere([
            'tier_id' => $tierId,
        ]);

        $generatedCoupons = [];

        for ($i = 0; $i < count($benefits); $i++) {
            if ($benefits[$i]->benefits_type == 'custom') {
                continue;
            }
            if ($benefits[$i]->benefits_type == 'lifetime') {
                if (! isset($benefits[$i]->merchant_reward_id) || ! isset($benefits[$i]->reward->reward) || ! in_array($benefits[$i]->reward->reward->slug, [
                        'fixed-amount',
                        'variable-amount',
                        'percentage-off',
                        'free-shipping',
                    ])) {
                    continue;
                }
            }
            if (isset($benefits[$i]->merchant_reward_id)) {
                try {
                    $coupon = app('coupon_service')->generateTierBenefitCoupon($benefits[$i]->merchant_reward_id, $customerId, $benefits[$i]->benefits_type);
                } catch (\Exception $exception) {
                    Log::error('CustomerService: An error occurred while attempting to generate tier benefit reward coupon (Benefit #'.$benefits[$i]->id.'): '.$exception->getMessage().'.');
                }
                if (isset($coupon)) {
                    $generatedCoupons[] = $coupon;
                }
            } else {
                if ($benefits[$i]->benefits_reward == 'points') {
                    $pointTitle = (isset($benefits[$i]->tier->name) ? trim($benefits[$i]->tier->name).' ' : '').'Tier achieved';

                    // Reward points to customer
                    $this->givePoints($merchantId, $customerId, $benefits[$i]->benefits_discount, [
                        'title' => $pointTitle,
                    ]);
                }
            }
        }

        Log::info('CustomerService: Tier benefit reward coupons generated: '.print_r(array_map(function ($item) {
                return $item->id;
            }, $generatedCoupons), true));

        return $generatedCoupons;
    }

    public function registerCustomerByEcommerceId($merchant_id, $ecommerce_id)
    {
        // Get merchant's store integration
        $storeIntegration = app('merchant_service')->getStoreIntegration($merchant_id);

        if (! $storeIntegration) {
            throw new \Exception('No store integration found.');
        }

        // get customer info from store by ecommerce id
        switch ($storeIntegration->slug) {
            case 'shopify':
                $eCommerceCustomerData = app('shopify_ecommerce_integration')->getCustomerData($storeIntegration, $ecommerce_id);
                break;
            case 'magento':
                $eCommerceCustomerData = app('magento_ecommerce_integration')->getCustomerData($storeIntegration, $ecommerce_id);
                break;
            case 'woocommerce':
                $eCommerceCustomerData = app('woocommerce_ecommerce_integration')->getCustomerData($storeIntegration, $ecommerce_id);
                break;
            case 'volusion':
                $eCommerceCustomerData = app('volusion_ecommerce_integration')->getCustomerData($storeIntegration, $ecommerce_id);
                break;
            case 'bigcommerce':
                $eCommerceCustomerData = app('bigcommerce_ecommerce_integration')->getCustomerData($storeIntegration, $ecommerce_id);
            // ...
        }

        // register customer
        if (! isset($eCommerceCustomerData) || ! $eCommerceCustomerData || ! isset($eCommerceCustomerData['email'])) {
            throw new \Exception('No customer found.');
        }

        $customerStructure = [
            'name'         => ($eCommerceCustomerData['first_name'] ?? '').' '.($eCommerceCustomerData['last_name'] ?? ''),
            'email'        => $eCommerceCustomerData['email'],
            'ecommerce_id' => $ecommerce_id,
        ];

        if (isset($eCommerceCustomerData['country']) && trim($eCommerceCustomerData['country'])) {
            $customerStructure['country'] = trim($eCommerceCustomerData['country']);
        }
        if (isset($eCommerceCustomerData['zipcode']) && trim($eCommerceCustomerData['zipcode'])) {
            $customerStructure['zipcode'] = trim($eCommerceCustomerData['zipcode']);
        }

        if (isset($eCommerceCustomerData['birthday'])) {
            $customerStructure['birthday'] = Carbon::createFromTimestamp(strtotime($eCommerceCustomerData['birthday']))
                ->format('Y-m-d');
        }

        $this->merchants->clearEntity();
        $merchant = $this->merchants->find($merchant_id);

        // Create/Update customer
        $customer = $this->updateOrCreate($merchant, $customerStructure);

        return $customer;
    }

    public function incrementReferralShareCounter($customerId, array $data = [])
    {
        // Create referral link share record for customer with id $customerId
        $storeData = [
            'customer_id' => $customerId,
        ];

        if (isset($data['shared_to']) && trim($data['shared_to'])) {
            $storeData['shared_to'] = trim($data['shared_to']);
        }

        return $this->customerReferralShares->create($storeData);
    }

    public function incrementReferralClickCounter($customerId, array $data = [])
    {
        // Create referral link click record for customer with id $customerId
        $storeData = [
            'customer_id' => $customerId,
        ];

        if (isset($data['fpl']) && trim($data['fpl'])) {
            $fpl = trim($data['fpl']);

            $sharePlatformsMap = [
                'fb' => 'facebook',
                'tw' => 'twitter',
                'gp' => 'google',
                'em' => 'email',
            ];
            $storeData['clicked_from'] = isset($sharePlatformsMap[$fpl]) ? $sharePlatformsMap[$fpl] : $fpl;
        }

        return $this->customerReferralClicks->create($storeData);
    }
}
