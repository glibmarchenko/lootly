<?php

namespace App\Helpers;

use App\Repositories\Contracts\CouponRepository;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\MerchantRewardRepository;
use App\Repositories\Contracts\PointRepository;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CouponService
{
    protected $merchantRewards;

    protected $coupons;

    protected $storeIntegration;

    protected $discountReward;

    protected $discountCode;

    protected $points;

    protected $customers;

    protected $pointsTransactionId;

    public function __construct(
        MerchantRewardRepository $merchantRewards,
        CouponRepository $coupons,
        PointRepository $points,
        CustomerRepository $customers
    ) {
        $this->merchantRewards = $merchantRewards;
        $this->coupons = $coupons;
        $this->points = $points;
        $this->customers = $customers;
        $this->storeIntegration = null;
        $this->discountReward = null;
        $this->discountCode = null;
        $this->pointsTransactionId = null;
    }

    public function generateRewardCoupon(
        $merchantRewardId,
        $customerId,
        $pointId = null,
        $creatorId = null,
        $additionalRestrictions = []
    ) {
        if ($pointId) {
            $this->pointsTransactionId = $pointId;
        }

        $merchantReward = $this->merchantRewards->find($merchantRewardId);

        // Get Store Integration
        $this->storeIntegration = app('merchant_service')->getStoreIntegration($merchantReward->merchant_id);

        if (!isset($this->storeIntegration)) {
            throw new \Exception('Cannot get e-commerce integration data');
        }

        $couponSettings = [];

        if (isset($additionalRestrictions['available_for_owner_only']) && $additionalRestrictions['available_for_owner_only']) {
            try {
                $customer = $this->customers->find($customerId);
                if ((!isset($customer->ecommerce_id) || !trim($customer->ecommerce_id)) && $this->storeIntegration->slug != 'custom-api') {
                    throw new \Exception('Not valid e-commerce customer');
                }
            } catch (\Exception $e) {
                throw $e;
            }

            $couponSettings = [
                'customer_ids' => $customer->ecommerce_id,
            ];
        }

        // Get Reward Data
        try {
            $this->discountReward = $this->merchantRewards->withCriteria([
                new EagerLoad(['reward']),
            ])->findWhereFirst([
                'id' => $merchantRewardId,
            ]);
        } catch (\Exception $exception) {
            throw $exception;
        }

        if (! isset($this->discountReward->reward)) {
            throw new \Exception('Cannot get reward data');
        }

        switch ($this->storeIntegration->slug) {
            case 'shopify':
                $this->discountCode = app('shopify_ecommerce_integration')->generateDiscount($this->storeIntegration, $this->discountReward, $couponSettings, $this->pointsTransactionId, $additionalRestrictions);
                break;
            case 'magento':
                $this->discountCode = app('magento_ecommerce_integration')->generateDiscount($this->storeIntegration, $this->discountReward, $couponSettings, $this->pointsTransactionId, $additionalRestrictions);
                break;
            case 'woocommerce':
                $this->discountCode = app('woocommerce_ecommerce_integration')->generateDiscount($this->storeIntegration, $this->discountReward, $couponSettings, $this->pointsTransactionId, $additionalRestrictions);
                break;
            case 'volusion':
                $this->discountCode = app('volusion_ecommerce_integration')->generateDiscount($this->storeIntegration, $this->discountReward, $couponSettings, $this->pointsTransactionId, $additionalRestrictions, $merchantReward);
                break;
            case 'custom-api':
                $this->discountCode = app('custom_api_ecommerce_integration')->generateDiscount($this->storeIntegration, $this->discountReward, $couponSettings, $this->pointsTransactionId, $additionalRestrictions, $merchantReward);
                break;                
            case 'bigcommerce':
                $this->discountCode = app('bigcommerce_ecommerce_integration')->generateDiscount($this->storeIntegration, $this->discountReward, $couponSettings, $this->pointsTransactionId, $additionalRestrictions);
                break;
            // ...
        }

        if (! $this->discountCode) {
            throw new \Exception('Discount code has not been generated');
        }

        $couponData = [
            'merchant_id'        => $this->discountReward->merchant_id,
            'customer_id'        => $customerId,
            'merchant_reward_id' => $merchantRewardId,
            'shop_coupon_id'     => $this->discountCode['id'],
            'coupon_code'        => $this->discountCode['code'],
            'is_used'            => 0,
        ];

        if ($creatorId) {
            $couponData['created_by_customer_id'] = $creatorId;
        }

        $coupon = $this->coupons->create($couponData);

        if (! $coupon) {
            throw new \Exception('Discount code has not been saved');
        }

        return $coupon;
    }

    public function generateTierBenefitCoupon($merchantRewardId, $customerId, $couponType)
    {
        $merchantReward = $this->merchantRewards->find($merchantRewardId);

        $allowedCouponTypes = [
            'entry',
            'lifetime',
        ];

        try {
            $customer = $this->customers->find($customerId);
            if (! isset($customer->ecommerce_id) || ! trim($customer->ecommerce_id)) {
                throw new \Exception('Not valid e-commerce customer');
            }
        } catch (\Exception $e) {
            throw $e;
        }

        $couponSettings = [
            'customer_ids' => $customer->ecommerce_id,
        ];

        if (in_array($couponType, $allowedCouponTypes)) {
            if ($couponType == 'entry') {
                $couponSettings['usage_limit'] = 1;
            } elseif ($couponType == 'lifetime') {
                $couponSettings['usage_limit'] = 9999;
            }
        }

        // Get Reward Data
        try {
            $this->discountReward = $this->merchantRewards->withCriteria([
                new EagerLoad(['reward']),
            ])->findWhereFirst([
                'id' => $merchantRewardId,
            ]);
        } catch (\Exception $exception) {
            throw $exception;
        }

        if (! isset($this->discountReward->reward)) {
            throw new \Exception('Cannot get reward data');
        }

        // Get Store Integration
        $this->storeIntegration = app('merchant_service')->getStoreIntegration($this->discountReward->merchant_id);

        if (! isset($this->storeIntegration)) {
            throw new \Exception('Cannot get e-commerce integration data');
        }

        switch ($this->storeIntegration->slug) {
            case 'shopify':
                $this->discountCode = app('shopify_ecommerce_integration')->generateDiscount($this->storeIntegration, $this->discountReward, $couponSettings, $this->pointsTransactionId);
                break;
            case 'magento':
                $this->discountCode = app('magento_ecommerce_integration')->generateDiscount($this->storeIntegration, $this->discountReward, $couponSettings, $this->pointsTransactionId);
                break;
            case 'woocommerce':
                $this->discountCode = app('woocommerce_ecommerce_integration')->generateDiscount($this->storeIntegration, $this->discountReward, $couponSettings, $this->pointsTransactionId);
                break;
            case 'volusion':
                $this->discountCode = app('volusion_ecommerce_integration')->generateDiscount($this->storeIntegration, $this->discountReward, $couponSettings, $this->pointsTransactionId, [], $merchantReward);
                break;
            case 'bigcommerce':
                $this->discountCode = app('bigcommerce_ecommerce_integration')->generateDiscount($this->storeIntegration, $this->discountReward, $couponSettings, $this->pointsTransactionId);
                break;
            // ...
        }

        if (! $this->discountCode) {
            throw new \Exception('Discount code has not been generated');
        }

        $coupon = $this->coupons->create([
            'merchant_id'        => $this->discountReward->merchant_id,
            'customer_id'        => $customerId,
            'merchant_reward_id' => $merchantRewardId,
            'shop_coupon_id'     => $this->discountCode['id'],
            'coupon_code'        => $this->discountCode['code'],
            'is_used'            => 0,
        ]);

        if (! $coupon) {
            throw new \Exception('Discount code has not been saved');
        }

        return $coupon;
    }

    public function uploadCoupons(Request $request): array
    {
        $tempFile = tmpfile();
        $pathFile = stream_get_meta_data($tempFile)['uri'];
        fwrite($tempFile, base64_decode(substr($request->importFile, strpos($request->importFile, ',') + 1)));

        $fileContents = file_get_contents($pathFile);
        fclose($tempFile);

        $items = explode(PHP_EOL, $fileContents);

        $coupons = [];
        foreach ($items as $item) {
            if (! empty($item)) {
                $code = trim($item);
                $code = trim($code, ',;');

                $coupons[]['code'] = $code;
            }
        }

        return $coupons;
    }
}
