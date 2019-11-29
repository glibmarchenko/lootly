<?php

namespace App\Helpers\EcommerceIntegration;

use App\Helpers\EcommerceIntegration\ApiClient\CommonApiClient;
use App\Helpers\EcommerceIntegration\Exceptions\ApiClientSetupError;
use App\Repositories\Contracts\MerchantDetailsRepository;
use App\Repositories\Contracts\PointRepository;

class MagentoEcommerceIntegrationService extends BaseEcommerceIntegrationService
{
    protected $merchantDetails;

    /**
     * MagentoEcommerceIntegrationService constructor.
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
                throw new ApiClientSetupError('An error has occurred while attempting to get merchant information for Magento API client setup.');
            }

            $api = new CommonApiClient();
            $api->setSession($this->eCommerceIntegration->pivot->api_endpoint, $merchantDetails->api_key, $merchantDetails->api_secret);

            return $api;
        } catch (\Exception $e) {
            throw new ApiClientSetupError('An error has occurred while attempting to setup Magento API client. '.$e->getMessage());
        }
    }

    protected function sendUninstallIntegrationRequest()
    {
        //
    }

    protected function sendGenerateDiscountRequest($discountData)
    {
        try {
            $priceRuleResponse = $this->apiClient->rest('POST', '/price-rules', [
                'price_rule' => $discountData,
            ])->body->price_rule;
        } catch (\Exception $exception) {
            throw $exception;
        }

        try {
            $discountCodeResponse = $this->apiClient->rest('POST', '/price-rules/'.$priceRuleResponse->id.'/discount-codes', [
                'discount_code' => [
                    'code' => $discountData['title'],
                ],
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
            try {
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
            }
        }

        return $discountData;
    }

    protected function sendGetCustomerRequest($customerId)
    {
        $output = [];

        try {
            $customerData = $this->apiClient->rest('GET', '/customers/'.$customerId)->body->customer;

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
}