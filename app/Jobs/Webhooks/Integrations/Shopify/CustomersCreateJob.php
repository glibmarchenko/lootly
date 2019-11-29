<?php

namespace App\Jobs\Webhooks\Integrations\Shopify;

use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\IntegrationRepository;
use App\Repositories\Contracts\MerchantActionRepository;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class CustomersCreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

    protected $customers;

    protected $merchantActions;

    protected $integrations;

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
     * @param \App\Repositories\Contracts\CustomerRepository       $customers
     *
     * @param \App\Repositories\Contracts\MerchantActionRepository $merchantActions
     *
     * @param \App\Repositories\Contracts\IntegrationRepository    $integrations
     *
     * @return void
     */
    public function handle(
        CustomerRepository $customers,
        MerchantActionRepository $merchantActions,
        IntegrationRepository $integrations
    ) {
        $this->customers = $customers;
        $this->merchantActions = $merchantActions;
        $this->integrations = $integrations;

        $shopDomain = isset($this->headers['x-shopify-shop-domain']) ? ($this->headers['x-shopify-shop-domain'][0] ?? '') : '';

        Log::info('New customer at '.$shopDomain.'.');

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

                    Log::info('Creating/Updating customer #'.$this->data->id.' ('.$this->data->email.') in merchant #'.$merchant->id);
                    try {
                        if (! isset($this->data->state) || strtolower($this->data->state) != 'enabled') {
                            if (! isset($integrationSettings['order_settings']['include_previous_orders']) || ! boolval($integrationSettings['order_settings']['include_previous_orders'])) {
                                Log::info('Skip customer creation: Customer accounts - Disabled. Include previous purchases as a guest - Disabled (Shopify integration #'.$shopifyIntegration->id.', merchant #'.$merchant->id.').');
                                continue;
                            }
                        }

                        $customerStructure = [
                            'name'         => $this->data->first_name.' '.$this->data->last_name,
                            'email'        => $this->data->email,
                            'ecommerce_id' => $this->data->id,
                            'birthday'     => '0000-00-00'
                        ];

                        if (isset($this->data->default_address)) {
                            if (isset($this->data->default_address->country) && trim($this->data->default_address->country)) {
                                $customerStructure['country'] = trim($this->data->default_address->country);
                            }
                            if (isset($this->data->default_address->zip) && trim($this->data->default_address->zip)) {
                                $customerStructure['zipcode'] = trim($this->data->default_address->zip);
                            }
                        }

                        // Create/Update customer
                        $customer = app('customer_service')->updateOrCreate($merchant, $customerStructure);
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
