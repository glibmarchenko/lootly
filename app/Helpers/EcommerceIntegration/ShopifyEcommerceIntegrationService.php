<?php

namespace App\Helpers\EcommerceIntegration;

use App\Helpers\EcommerceIntegration\Exceptions\ApiClientSetupError;
use Illuminate\Support\Facades\Log;

class ShopifyEcommerceIntegrationService extends BaseEcommerceIntegrationService
{
    public function apiClient()
    {
        try {
            $api = app('shopify_api')->setup();
            $api->setShop($this->eCommerceIntegration->pivot->external_id);
            $api->setAccessToken($this->eCommerceIntegration->pivot->token);

            return $api;
        } catch (\Exception $e) {
            throw new ApiClientSetupError('An error has occurred while attempting to setup Shopify API client. '.$e->getMessage());
        }
    }

    protected function sendUninstallIntegrationRequest()
    {
        try{
            $this->apiClient->rest('DELETE', '/admin/api_permissions/current.json', []);
        }catch(\Exception $exception){
            throw $exception;
        }
    }

    protected function sendGenerateDiscountRequest($discountData)
    {
        try {
            $priceRuleResponse = $this->apiClient->rest('POST', '/admin/price_rules.json', [
                'price_rule' => $discountData,
            ])->body->price_rule;
        } catch (\Exception $exception) {
            throw $exception;
        }

        try {
            $discountCodeResponse = $this->apiClient->rest('POST', '/admin/price_rules/'.$priceRuleResponse->id.'/discount_codes.json', [
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
                $discountData['customer_selection'] = 'prerequisite';
                $discountData['prerequisite_customer_ids'] = [];
                $sinceId = null;
                $savedSearchesCount = intval($this->apiClient->rest('GET', '/admin/customer_saved_searches/count.json')->body->count);
                for ($i = 0; $i < ceil($savedSearchesCount / 250); $i++) {
                    $savedSearches = $this->apiClient->rest('GET', '/admin/customer_saved_searches.json?limit=250'.($sinceId ? '&since_id='.$sinceId : ''))->body->customer_saved_searches;
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
                    $savedSearch = $this->apiClient->rest('POST', '/admin/customer_saved_searches.json', [
                        'customer_saved_search' => [
                            'name'  => 'Lootly New Customers',
                            'query' => 'orders_count:'.$ordersCountRestriction,
                        ],
                    ])->body->customer_saved_search;
                    $discountData['prerequisite_saved_search_ids'][] = $savedSearch->id;
                }
            } catch (\Exception $e) {
                Log::error('An error on referral receiver reward discount create: '.$e->getMessage());
                $discountData['customer_selection'] = 'all';
                $discountData['prerequisite_customer_ids'] = [];
            }
        }

        return $discountData;
    }

    protected function sendGetCustomerRequest($customerId)
    {
        $output = [];

        try {
            $customerData = $this->apiClient->rest('GET', '/admin/customers/'.$customerId.'.json')->body->customer;

            $output = [
                'first_name' => $customerData->first_name ?? null,
                'last_name'  => $customerData->last_name ?? null,
                'email'      => $customerData->email,
                'phone'      => $customerData->phone ?? null,
            ];

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
            $productsResponse = $this->apiClient->rest('GET', '/admin/products.json', $requestParams)->body->products;
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

    public function getProductsCollections( $product_IDs )
    {
        try {
            $all_products_collections = [];
            foreach ( $product_IDs as $product_ID ) {
                $requestParams = ['product_id' => $product_ID];

                $smart_collections = $this->apiClient->rest('GET', '/admin/smart_collections.json', $requestParams)->body->smart_collections;
                foreach ( $smart_collections as $collection ) {
                    $all_products_collections[] = $collection->title;
                }
            }
            return $all_products_collections;

        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
