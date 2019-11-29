<?php

namespace App\Jobs\Webhooks\Integrations\Shopify;

use App\Repositories\Contracts\CouponRepository;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\IntegrationRepository;
use App\Repositories\Contracts\MerchantActionRepository;
use App\Repositories\Contracts\ReferralRepository;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Jobs\Webhooks\Integrations\Traits\RestrictionsTrait;

class OrdersFulfilledJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RestrictionsTrait;

    /**
     * The webhook data.
     *
     * @var object
     */
    protected $data;

    /**
     * Request headers.
     *
     * @var string
     */
    protected $headers;

    protected $integrationType;

    protected $integrations;

    protected $coupons;

    protected $referrals;

    protected $merchantActions;

    protected $customers;

    /**
     * Create a new job instance.
     *
     * @param object $data    The webhook data (JSON decoded)
     * @param array  $headers Request headers
     * @param string $integrationType
     */
    public function __construct($data, array $headers, $integrationType = null)
    {
        $this->data = $data;
        $this->headers = $headers;
        $this->integrationType = $integrationType;
    }

    /**
     * Execute the job.
     *
     * @param \App\Repositories\Contracts\IntegrationRepository    $integrations
     * @param \App\Repositories\Contracts\CouponRepository         $coupons
     * @param \App\Repositories\Contracts\ReferralRepository       $referrals
     * @param \App\Repositories\Contracts\MerchantActionRepository $merchantActions
     * @param \App\Repositories\Contracts\CustomerRepository       $customers
     *
     * @return void
     */
    public function handle(
        IntegrationRepository $integrations,
        CouponRepository $coupons,
        ReferralRepository $referrals,
        MerchantActionRepository $merchantActions,
        CustomerRepository $customers
    ) {
        $this->integrations = $integrations;
        $this->referrals = $referrals;
        $this->coupons = $coupons;
        $this->merchantActions = $merchantActions;
        $this->customers = $customers;

        $shopDomain = isset($this->headers['x-shopify-shop-domain']) ? ($this->headers['x-shopify-shop-domain'][0] ?? '') : '';

        Log::info('Fulfilled order at '.$shopDomain.'.');

        try {
            $shopifyIntegration = $this->integrations->findWhereFirst([
                'slug'   => 'shopify',
                'status' => 1,
            ]);
        } catch (\Exception $exception) {
            Log::warning('No integration with slug "shopify"');

            return;
        }

        if (isset($shopifyIntegration) && $shopifyIntegration) {
            $this->integrations->clearEntity();
            $targetMerchants = $this->integrations->withCriteria([
                new LatestFirst(),
            ])->findMerchantWhere($shopifyIntegration->id, [
                'status'      => 1,
                'external_id' => $shopDomain,
            ]);

            if ($targetMerchants && count($targetMerchants)) {
                foreach ($targetMerchants as $merchant) {
                    $integrationSettings = [];
                    try {
                        $settings = $merchant->pivot->settings ?? null;
                        $integrationSettings = json_decode($settings, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            $integrationSettings = [];
                        }
                    } catch (\Exception $e) {
                        Log::error('Error while trying to convert shopify integration settings from JSON to array (Merchant: #'.$merchant->id.'):'.$e->getMessage());
                    }

                    if (! isset($integrationSettings['order_settings']['reward_status']) || strtolower($integrationSettings['order_settings']['reward_status']) != 'fulfilled') {
                        Log::info('Order status for webhook proceeding not matching to selected (Shopify integration #'.$shopifyIntegration->id.', merchant #'.$merchant->id.').');
                        continue;
                    }

                    try {

                        if (isset($this->data->customer)) {

                            if (! isset($this->data->customer->state) || strtolower($this->data->customer->state) != 'enabled') {
                                if (! isset($integrationSettings['order_settings']['include_previous_orders']) || ! boolval($integrationSettings['order_settings']['include_previous_orders'])) {
                                    Log::info('Skip order: Customer accounts - Disabled. Include previous purchases as a guest - Disabled (Shopify integration #'.$shopifyIntegration->id.', merchant #'.$merchant->id.').');
                                    continue;
                                }
                            }

                            // Checking if order already exists
                            $orderCheck = app('order_service')->findWhere($merchant->id, [
                                'customer' => [
                                    'email' => $this->data->customer['email'],
                                    //'ecommerce_id' => $this->data->customer['id'],
                                ],
                                'order'    => [
                                    'order_id' => $this->data->id,
                                ],
                            ]);

                            if ($orderCheck) {
                                Log::info('Order #'.$orderCheck->order_id.' already exists.');

                                return;
                            }

                            $customerStructure = [
                                'name'         => ($this->data->customer['first_name'] ?? '').' '.($this->data->customer['last_name'] ?? ''),
                                'email'        => $this->data->customer['email'],
                                'ecommerce_id' => $this->data->customer['id'] ?? null,
                            ];

                            if (isset($this->data->customer['default_address'])) {
                                if (isset($this->data->customer['default_address']['country']) && trim($this->data->customer['default_address']['country'])) {
                                    $customerStructure['country'] = trim($this->data->customer['default_address']['country']);
                                }
                                if (isset($this->data->customer['default_address']['zip']) && trim($this->data->customer['default_address']['zip'])) {
                                    $customerStructure['zipcode'] = trim($this->data->customer['default_address']['zip']);
                                }
                            }

                            // Create/Update Customer
                            $customer = app('customer_service')->updateOrCreate($merchant, $customerStructure);

                            if ($customer) {

                                $this->checkAndReward( $merchant, $this->data, $this->coupons, $this->referrals, $customer, $this->merchantActions, $integrationSettings, 'shopify' );
                            }
                        }
                    } catch (\Exception $e) {
                        Log::info('Error during customer processing for merchant #'.$merchant->id.': '.$e->getMessage());
                    }
                }
            } else {
                Log::warning('No merchants with active Shopify integration');
            }
        } else {
            Log::warning('No integration with slug "shopify"');
        }
    }
}
