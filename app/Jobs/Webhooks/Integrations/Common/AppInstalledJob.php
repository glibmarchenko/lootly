<?php

namespace App\Jobs\Webhooks\Integrations\Common;

use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\IntegrationRepository;
use App\Repositories\Contracts\MerchantActionRepository;
use App\Repositories\Contracts\MerchantDetailsRepository;
use App\Repositories\Contracts\MerchantRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AppInstalledJob implements ShouldQueue
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

    protected $merchants;

    protected $integrations;

    protected $merchantDetails;

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
     * @param \App\Repositories\Contracts\MerchantRepository        $merchants
     * @param \App\Repositories\Contracts\IntegrationRepository     $integrations
     * @param \App\Repositories\Contracts\MerchantDetailsRepository $merchantDetails
     *
     * @return void
     */
    public function handle(
        MerchantRepository $merchants,
        IntegrationRepository $integrations,
        MerchantDetailsRepository $merchantDetails
    ) {
        $this->merchants = $merchants;
        $this->integrations = $integrations;
        $this->merchantDetails = $merchantDetails;

        if (! isset($this->data->lootly_merchant_id)) {
            Log::error("Invalid request data");

            return;
        }

        $merchant = $this->merchants->find($this->data->lootly_merchant_id);

        if ($merchant) {
            $validator = Validator::make((array) $this->data, [
                'shop_url'     => 'required|max:255',
                'api_endpoint' => 'string|max:255',
            ]);

            if (! $validator->fails()) {
                Log::info('Creating/Updating e-commerce integration for merchant #'.$merchant->id);
                try {
                    $shop_domain = $this->data->shop_url;

                    // Get integration info
                    try {
                        $ecommerceIntegration = $this->integrations->findWhereFirst([
                            'slug'   => $this->integrationType,
                            'status' => 1,
                        ]);
                    } catch (\Exception $e) {
                        // No e-commerce integration with such slug
                    }

                    // Check integration status
                    if (! isset($ecommerceIntegration) || ! $ecommerceIntegration) {
                        Log::error('An error has occurred while attempting to connect integration "'.$this->integrationType.'" for merchant #'.$merchant->id.'.');

                        return;
                    }

                    if (! trim($merchant->website)) {
                        $this->merchants->clearEntity();
                        try {
                            $this->merchants->update($merchant->id, [
                                'website' => $shop_domain,
                            ]);
                        } catch (\Exception $exception) {

                        }
                    }

                    // Create/Update integration
                    try {
                        // Default integration settings
                        $defaultSettings = [
                            'order_settings' => [
                                'reward_status'     => 'paid',
                                'subtract_status'   => 'refunded',
                                'include_taxes'     => 0,
                                'include_shipping'  => 0,
                                'exclude_discounts' => 1,
                                'include_previous_orders' => 1,
                            ],
                        ];

                        $configSettings = config("integrations.$this->integrationType.default_settings");

                        if (isset($configSettings) && $configSettings){
                            $defaultSettings = array_merge($defaultSettings, $configSettings);
                        }

                        // Update merchant integrations data
                        $merchantIntegrationData = [
                            'status'      => 1,
                            'external_id' => $shop_domain,
                            'settings'    => json_encode($defaultSettings),
                        ];

                        if (isset($this->data->api_endpoint) && trim($this->data->api_endpoint)) {
                            $merchantIntegrationData['api_endpoint'] = trim($this->data->api_endpoint);
                        }

                        $this->merchants->clearEntity();

                        try {
                            app('merchant_service')->deactivateEcommerceIntegrations($merchant, [$ecommerceIntegration->id]);
                        } catch (\Exception $e) {
                            Log::error($ecommerceIntegration->slug.' integration (#'.$ecommerceIntegration->id.') installing: Error on attempting to deactivate e-commerce integration (merchant #'.$merchant->id.').');
                            Log::error($e->getMessage());
                        }

                        $this->merchants->updateIntegrations($merchant, $ecommerceIntegration->id, $merchantIntegrationData);

                        // Update merchant details
                        $this->merchantDetails->updateOrCreate([
                            'merchant_id' => $merchant->id,
                        ], [
                            'ecommerce_shop_domain' => $shop_domain,
                        ]);
                    } catch (\Exception $exception) {
                        Log::error('Create/Update integration error: '.$exception->getMessage());

                        return;
                    }
                } catch (\Exception $e) {
                    Log::info('Error during processing app installation webhook for merchant #'.$merchant->id.': '.$e->getMessage());
                }
            } else {
                Log::error('Invalid request data: '.$validator->errors()->first());
            }
        } else {
            Log::error('Merchant with id '.$this->data->lootly_merchant_id.' not found.');
        }
    }
}