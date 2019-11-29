<?php

namespace App\Helpers\EcommerceIntegration;

use App\Helpers\EcommerceIntegration\Exceptions\NoApiClientDefined;
use App\Repositories\Contracts\PointRepository;
use App\Models\MerchantReward;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

abstract class BaseEcommerceIntegrationService
{
    protected $apiClient;

    protected $eCommerceIntegration;

    protected $pointsTransactionId;

    protected $merchantReward;

    protected $points;

    public function __construct(PointRepository $points)
    {
        $this->points = $points;
    }

    abstract function apiClient();

    abstract protected function sendUninstallIntegrationRequest();

    abstract protected function sendGenerateDiscountRequest($discountData);

    abstract protected function sendGetCustomerRequest($customerId);

    abstract protected function sendGetProductsRequest($requestParams);

    protected function applyDiscountRestrictions(array $discountData, array $restrictions)
    {
        return $discountData;
    }

    protected function resolveApiClient()
    {
        if (! method_exists($this, 'apiClient')) {
            throw new NoApiClientDefined();
        }

        $this->apiClient = $this->apiClient();
    }

    public function uninstallIntegration($eCommerceIntegration)
    {
        $this->eCommerceIntegration = $eCommerceIntegration;

        $this->resolveApiClient();
        return $this->sendUninstallIntegrationRequest();
    }

    public function generateDiscount(
        $eCommerceIntegration,
        $discountReward,
        $discountSettings = [],
        $pointsTransactionId = null,
        array $restrictions = [],
        ?MerchantReward $merchantReward = null
    ) {

        $this->eCommerceIntegration = $eCommerceIntegration;
        $this->pointsTransactionId = $pointsTransactionId;
        $this->merchantReward = $merchantReward;

        $this->resolveApiClient();

        $rewardType = $discountReward->reward->slug;

        switch ($rewardType) {
            case 'fixed-amount':

                // Get reward value
                $couponValue = intval($discountReward->reward_value);
                if (! $couponValue) {
                    throw new \Exception('Invalid discount request data');
                }

                $discountData = $this->getFixedAmountDiscountData($discountReward, $discountSettings, $couponValue, $restrictions);

                return $this->sendGenerateDiscountRequest($discountData);

                break;
            case 'percentage-off':

                // Get reward value
                $couponValue = intval($discountReward->reward_value);
                if (! $couponValue) {
                    throw new \Exception('Invalid discount request data');
                }

                $discountData = $this->getPercentageOffDiscountData($discountReward, $discountSettings, $couponValue, $restrictions);

                return $this->sendGenerateDiscountRequest($discountData);

                break;
            case 'free-shipping':

                $discountData = $this->getFreeShippingDiscountData($discountReward, $discountSettings, $restrictions);

                return $this->sendGenerateDiscountRequest($discountData);

                break;
            case 'free-product':

                // Get reward value
                $couponValue = 100;
                if (! $couponValue) {
                    throw new \Exception('Invalid discount request data');
                }

                $product_ids = [];
                if (isset($discountReward->product) && trim($discountReward->product)) {
                    $product_ids[] = trim($discountReward->product);
                }

                if (! count($product_ids)) {
                    throw new \Exception('Free product not specified in the discount request');
                }

                $discountData = $this->getFreeProductDiscountData($discountReward, $discountSettings, $couponValue, $product_ids, $restrictions);

                return $this->sendGenerateDiscountRequest($discountData);

                break;
            case 'variable-amount':

                if (! $pointsTransactionId) {
                    throw new \Exception('Invalid discount request data');
                }
                try {
                    $pointsTransaction = $this->points->find($pointsTransactionId);
                } catch (\Exception $exception) {
                    throw new \Exception('Invalid discount request data');
                }

                if (isset($pointsTransaction) && $pointsTransaction) {

                    $spent_points = $pointsTransaction->point_value;
                    if ($spent_points >= 0) {
                        throw new \Exception('Illegal operation');
                    }

                    $spent_points = $spent_points * -1;

                    $points_required = $discountReward->points_required;

                    $koef = floor($spent_points / $points_required);

                    // Get reward value
                    $couponValue = intval($discountReward->reward_value * $koef);
                    if (! $couponValue) {
                        throw new \Exception('Invalid discount request data');
                    }

                    $discountData = $this->getVariableAmountDiscountData($discountReward, $discountSettings, $couponValue, $restrictions);

                    return $this->sendGenerateDiscountRequest($discountData);
                } else {
                    throw new \Exception('Invalid discount request data');
                }

                break;
            default: {
                throw new \Exception('Invalid discount request data');
                break;
            }
        }
    }

    protected function getDiscountCommonData($discountReward, array $discountData)
    {
        // Check coupon prefix settings
        $couponPrefix = '';
        if (isset($discountReward->coupon_prefix) && trim($discountReward->coupon_prefix)) {
            $couponPrefix = trim($discountReward->coupon_prefix);
        }
        // Generate coupon code
        $couponCode = $couponPrefix.strtoupper(str_random(15));

        // Set title
        $discountData['title'] = $couponCode;

        // Set start_at limit
        $discountData['starts_at'] = Carbon::now()->toIso8601ZuluString(); //2017-01-19T17:59:10Z

        // Check coupon order minimum
        if (isset($discountReward->order_minimum) && floatval($discountReward->order_minimum) > 0) {
            $discountData['prerequisite_subtotal_range'] = [
                'greater_than_or_equal_to' => floatval($discountReward->order_minimum)
                // 50.0
            ];
        }

        // Check coupon expiration limits
        if (isset($discountReward->coupon_expiration) && $discountReward->coupon_expiration) {
            $coupon_expiration_time = trim($discountReward->coupon_expiration_time);
            $explode_expiration_string = explode(' ', $coupon_expiration_time);
            if (count($explode_expiration_string) == 2) {
                $period_value = intval($explode_expiration_string[0]);
                $period_type = strtolower(trim($explode_expiration_string[1]));
                $valid = false;
                if ($period_value) {
                    if (in_array($period_type, [
                        'days',
                        'weeks',
                        'month',
                        'years',
                    ])) {
                        $valid = true;
                    }
                }
                if ($valid) {
                    $addPeriod = 'add'.ucfirst($period_type);
                    $discountData['ends_at'] = Carbon::now()
                        ->$addPeriod($period_value)
                        ->toIso8601ZuluString(); //2017-01-19T17:59:10Z
                }
            }
        }

        return $discountData;
    }

    public function getCustomerData(
        $eCommerceIntegration,
        $customerEcommerceId
    ) {
        $this->eCommerceIntegration = $eCommerceIntegration;

        $this->resolveApiClient();

        return $this->sendGetCustomerRequest($customerEcommerceId);
    }

    public function getProducts(
        $eCommerceIntegration,
        array $additionalConditions = []
    ) {
        $this->eCommerceIntegration = $eCommerceIntegration;

        $this->resolveApiClient();

        $requestParams = [];
        if (isset($additionalConditions['q']) && trim($additionalConditions['q'])) {
            $requestParams['title'] = trim($additionalConditions['q']);
        }

        return $this->sendGetProductsRequest($requestParams);
    }

    //Get collections of bought products to check them for restrictions
    public function getСollections(
        $eCommerceIntegration,
        $product_IDs
    ) {

        $this->eCommerceIntegration = $eCommerceIntegration;

        $this->resolveApiClient();

        return $this->getProductsCollections( $product_IDs );
    }

    protected function getFixedAmountDiscountData($discountReward, $discountSettings = [], $couponValue = 0, $restrictions = []){
        $discountData = [];

        $discountData = $this->getDiscountCommonData($discountReward, $discountData);

        if (isset($discountSettings['usage_limit'])) {
            $discountData['usage_limit'] = intval($discountSettings['usage_limit']);
        } else {
            $discountData['usage_limit'] = 1;
        }

        if (isset($discountSettings['customer_ids'])) {
            $discountData['prerequisite_customer_ids'] = array_flatten([$discountSettings['customer_ids']]);
            $discountData['customer_selection'] = 'prerequisite';
        } else {
            $discountData['customer_selection'] = 'all';
        }

        $discountData['target_type'] = 'line_item';
        $discountData['target_selection'] = 'all';
        $discountData['allocation_method'] = 'across';
        $discountData['value_type'] = 'fixed_amount';
        $discountData['value'] = abs($couponValue) * -1; // -15.0

        if (count($restrictions)) {
            $discountData = $this->applyDiscountRestrictions($discountData, $restrictions);
        }

        return $discountData;
    }

    protected function getPercentageOffDiscountData($discountReward, $discountSettings = [], $couponValue = 0, $restrictions = []){
        $discountData = [];

        $discountData = $this->getDiscountCommonData($discountReward, $discountData);

        if (isset($discountSettings['usage_limit'])) {
            $discountData['usage_limit'] = intval($discountSettings['usage_limit']);
        } else {
            $discountData['usage_limit'] = 1;
        }

        if (isset($discountSettings['customer_ids'])) {
            $discountData['prerequisite_customer_ids'] = array_flatten([$discountSettings['customer_ids']]);
            $discountData['customer_selection'] = 'prerequisite';
        } else {
            $discountData['customer_selection'] = 'all';
        }

        $discountData['target_type'] = 'line_item';
        $discountData['target_selection'] = 'all';
        $discountData['allocation_method'] = 'across';
        $discountData['value_type'] = 'percentage';
        $discountData['value'] = abs($couponValue) * -1; // -15.0

        if (count($restrictions)) {
            $discountData = $this->applyDiscountRestrictions($discountData, $restrictions);
        }

        return $discountData;
    }

    protected function getFreeShippingDiscountData($discountReward, $discountSettings = [], $restrictions = []){
        $discountData = [];

        $discountData = $this->getDiscountCommonData($discountReward, $discountData);

        if (isset($discountSettings['usage_limit'])) {
            $discountData['usage_limit'] = intval($discountSettings['usage_limit']);
        } else {
            $discountData['usage_limit'] = 1;
        }

        if (isset($discountSettings['customer_ids'])) {
            $discountData['prerequisite_customer_ids'] = array_flatten([$discountSettings['customer_ids']]);
            $discountData['customer_selection'] = 'prerequisite';
        } else {
            $discountData['customer_selection'] = 'all';
        }

        $discountData['target_type'] = 'shipping_line';
        $discountData['target_selection'] = 'all';
        $discountData['allocation_method'] = 'each';
        $discountData['value_type'] = 'percentage';
        $discountData['value'] = '-100.0';

        // Check coupon maximum shipping
        if (isset($discountReward->max_shipping) && floatval($discountReward->max_shipping) > 0) {
            $discountData['prerequisite_shipping_price_range'] = [
                'less_than_or_equal_to' => floatval($discountReward->max_shipping)
                // 50.0
            ];
        }

        if (count($restrictions)) {
            $discountData = $this->applyDiscountRestrictions($discountData, $restrictions);
        }

        return $discountData;
    }

    protected function getFreeProductDiscountData($discountReward, $discountSettings= [], $couponValue = 0, $product_ids = [], $restrictions = []){
        $discountData = [];

        $discountData = $this->getDiscountCommonData($discountReward, $discountData);

        if (isset($discountSettings['usage_limit'])) {
            $discountData['usage_limit'] = intval($discountSettings['usage_limit']);
        } else {
            $discountData['usage_limit'] = 1;
        }

        if (isset($discountSettings['customer_ids'])) {
            $discountData['prerequisite_customer_ids'] = array_flatten([$discountSettings['customer_ids']]);
            $discountData['customer_selection'] = 'prerequisite';
        } else {
            $discountData['customer_selection'] = 'all';
        }

        $discountData['target_type'] = 'line_item';
        $discountData['target_selection'] = 'entitled';
        $discountData['allocation_method'] = 'across';
        $discountData['value_type'] = 'percentage';
        $discountData['value'] = abs($couponValue) * -1; // -100.0

        $discountData['allocation_limit'] = 1;

        $discountData['entitled_product_ids'] = $product_ids;

        if (count($restrictions)) {
            $discountData = $this->applyDiscountRestrictions($discountData, $restrictions);
        }

        return $discountData;
    }

    protected function getVariableAmountDiscountData($discountReward, $discountSettings = [], $couponValue = 0, $restrictions = []){
        $discountData = [];

        $discountData = $this->getDiscountCommonData($discountReward, $discountData);

        if (isset($discountSettings['usage_limit'])) {
            $discountData['usage_limit'] = intval($discountSettings['usage_limit']);
        } else {
            $discountData['usage_limit'] = 1;
        }

        if (isset($discountSettings['customer_ids'])) {
            $discountData['prerequisite_customer_ids'] = array_flatten([$discountSettings['customer_ids']]);
            $discountData['customer_selection'] = 'prerequisite';
        } else {
            $discountData['customer_selection'] = 'all';
        }

        $discountData['target_type'] = 'line_item';
        $discountData['target_selection'] = 'all';
        $discountData['allocation_method'] = 'across';
        $discountData['value_type'] = 'fixed_amount';
        $discountData['value'] = abs($couponValue) * -1; // -15.0

        if (count($restrictions)) {
            $discountData = $this->applyDiscountRestrictions($discountData, $restrictions);
        }

        return $discountData;
    }
}
