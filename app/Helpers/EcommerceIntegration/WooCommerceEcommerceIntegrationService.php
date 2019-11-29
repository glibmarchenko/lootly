<?php

namespace App\Helpers\EcommerceIntegration;

use App\Helpers\EcommerceIntegration\ApiClient\CommonApiClient;
use App\Helpers\EcommerceIntegration\Exceptions\ApiClientSetupError;
use App\Repositories\Contracts\MerchantDetailsRepository;
use App\Repositories\Contracts\PointRepository;

class WooCommerceEcommerceIntegrationService extends BaseEcommerceIntegrationService
{
    protected $merchantDetails;

    /**
     * WooCommerceEcommerceIntegrationService constructor.
     *
     * @param \App\Repositories\Contracts\PointRepository           $points
     * @param \App\Repositories\Contracts\MerchantDetailsRepository $merchantDetails
     */
    public function __construct(PointRepository $points, MerchantDetailsRepository $merchantDetails)
    {
        parent::__construct($points);
        $this->merchantDetails = $merchantDetails;
    }

    public function apiClient()
    {
        try {
            // Get merchant's api/secret
            $merchantDetails = $this->merchantDetails->findWhereFirst([
                'merchant_id' => $this->eCommerceIntegration->pivot->merchant_id,
            ]);

            if (! $merchantDetails) {
                throw new ApiClientSetupError('An error has occurred while attempting to get merchant information for WooCommerce API client setup.');
            }

            $api = new CommonApiClient();
            $api->setSession($this->eCommerceIntegration->pivot->api_endpoint, $merchantDetails->api_key, $merchantDetails->api_secret);

            return $api;
        } catch (\Exception $e) {
            throw new ApiClientSetupError('An error has occurred while attempting to setup WooCommerce API client. '.$e->getMessage());
        }
    }

    protected function sendUninstallIntegrationRequest()
    {
        //
    }

    protected function sendGenerateDiscountRequest($discountData)
    {
        /*try {
            $priceRuleResponse = $this->apiClient->rest('POST', '/price-rules', [
                'price_rule' => $discountData,
            ])->body->price_rule;
        } catch (\Exception $exception) {
            throw $exception;
        }*/

        try {
            //$discountCodeResponse = $this->apiClient->rest('POST', '/price-rules/'.$priceRuleResponse->id.'/discount-codes', [
            $discountCodeResponse = $this->apiClient->rest('POST', '/discount-codes', [
                'discount_code' => $discountData,
            ])->body->discount_code;
        } catch (\Exception $exception) {
            throw $exception;
        }

        return [
            'id'   => $discountCodeResponse->id,
            'code' => $discountCodeResponse->code,
        ];
    }

    protected function applyDiscountRestrictions(array $discountData, array $restrictions)
    {
        if (isset($restrictions['orders_count'])) {
            $ordersCountRestriction = $restrictions['orders_count'];

            // Apply orders count filter to discount data request
            $discountData['customer_selection'] = 'prerequisite';
            $discountData['prerequisite_customer_ids'] = [];

            $discountData['prerequisite_saved_search_ids'] = [];
            // Check customer saved search and add to id array
            /*try {
                $sinceId = null;
                $savedSearchesCount = intval($this->apiClient->rest('GET', '/customer-saved-searches/count')->body->count);
                for ($i = 0; $i < ceil($savedSearchesCount / 250); $i++) {
                    $savedSearches = $this->apiClient->rest('GET', '/customer-saved-searches?limit=250'.($sinceId ? '&since_id='.$sinceId : ''));
                    for ($j = 0; $j < count($savedSearches); $j++) {
                        if ($savedSearches[$j]->name == 'Lootly New Customers' && $savedSearches[$j]->query == 'orders_count:'.$ordersCountRestriction) {
                            $discountData['prerequisite_saved_search_ids'][] = $savedSearches[$j]->id;
                            break 2;
                        }
                        $sinceId = $savedSearches[$j]->id;
                    }
                }
                if (! count($discountData['prerequisite_saved_search_ids'])) {
                    // Add new saved search
                    $savedSearch = $this->apiClient->rest('POST', '/customer-saved-searches', [
                        'customer_saved_search' => [
                            'name'  => 'Lootly New Customers',
                            'query' => 'orders_count:'.$ordersCountRestriction,
                        ],
                    ])->body->customer_saved_search;
                    $discountData['prerequisite_saved_search_ids'][] = $savedSearch->id;
                }
            } catch (\Exception $e) {
                throw $e;
            }*/
        }

        if (isset($restrictions['prerequisite_customer_emails']) && is_array($restrictions['prerequisite_customer_emails'])) {
            $discountData['customer_selection'] = 'prerequisite';
            $discountData['prerequisite_customer_emails'] = $restrictions['prerequisite_customer_emails'];
        }

        return $discountData;
    }

    protected function sendGetCustomerRequest($customerId)
    {
        $t = time();

        $requestParams = [
            't' => $t.''
        ];

        try {
            $customerData = $this->apiClient->rest('GET', '/customers/'.$customerId, $requestParams)->body->customer;

            $output = [
                'first_name' => $customerData->first_name ?? null,
                'last_name'  => $customerData->last_name ?? null,
                'email'      => $customerData->email,
                'phone'      => $customerData->phone ?? null,
            ];

            if (isset($customerData->birthday)) {
                $customerStructure['birthday'] = $customerData->birthday;
            }

            if (isset($customerData->default_address)) {
                if (isset($customerData->default_address->country) && trim($customerData->default_address->country)) {
                    $customerStructure['country'] = trim($customerData->default_address->country);
                }
                if (isset($customerData->default_address->zip) && trim($customerData->default_address->zip)) {
                    $customerStructure['zipcode'] = trim($customerData->default_address->zip);
                }
            }
        } catch (\Exception $exception) {
            throw $exception;
        }

        return $output;
    }

    protected function sendGetProductsRequest($requestParams)
    {
        $t = time();
        $requestParams['t'] = $t.'';

        try {
            $productsResponse = $this->apiClient->rest('GET', '/products', $requestParams)->body->products;
        } catch (\Exception $exception) {
            throw $exception;
        }

        return array_map(function ($item) {
            $output = [
                'id'            => $item->id,
                'title'         => $item->title,
                'default_price' => 0,
            ];
            if (isset($item->variants) && count($item->variants)) {
                $output['default_price'] = floatval($item->variants[0]->price);

                if (count($item->variants) > 1) {
                    $output['variants'] = array_map(function ($variant) {
                        return [
                            'id'    => $variant->id,
                            'price' => floatval($variant->price),
                            'title' => $variant->title,
                        ];
                    }, $item->variants);
                }
            }

            return $output;
        }, $productsResponse);
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
        $discountData['value'] = '-100';

        $discountData['allocation_limit'] = 1;

        $discountData['entitled_product_ids'] = $product_ids;

        if (count($restrictions)) {
            $discountData = $this->applyDiscountRestrictions($discountData, $restrictions);
        }

        return $discountData;
    }
}