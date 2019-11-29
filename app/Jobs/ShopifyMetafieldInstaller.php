<?php

namespace App\Jobs;

use App\Facades\ShopifyApi;
use App\Merchant;
use App\Repositories\IntegrationRepository;
use App\Repositories\MerchantRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ShopifyMetafieldInstaller implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The shop.
     *
     * @var \App\Merchant
     */
    protected $merchant;

    /**
     * Metafields list.
     *
     * @var array
     */
    protected $metafields;

    /**
     * Create a new job instance.
     *
     * @param \App\Merchant $merchant   The merchant object
     * @param array         $metafields The metafields list
     *
     * @return void
     */
    public function __construct(Merchant $merchant, array $metafields)
    {
        $this->merchant = $merchant;
        $this->metafields = $metafields;
    }

    /**
     * Execute the job.
     *
     * @return array
     */
    public function handle()
    {
        Log::info('Job[ShopifyMetafieldInstall]: Status => Started');

        $api = ShopifyApi::setup();

        $integrationModel = new IntegrationRepository();
        $shopifyIntegration = $integrationModel->findBySlug('shopify');

        if (! $shopifyIntegration) {
            Log::error('Job[ShopifyMetafieldInstall]: Error => No Shopify integration found!');

            return [];
        }

        $merchantModel = new MerchantRepository();
        $checkIntegration = $merchantModel->findIntegrationWithToken($this->merchant, $shopifyIntegration);
        if (! $checkIntegration || ! isset($checkIntegration->pivot) || ! $checkIntegration->pivot->status || ! trim($checkIntegration->pivot->token)) {
            Log::error('Job[ShopifyMetafieldInstall]: Error => No Shopify integration found for merchant with ID #'.$this->merchant->id);

            return [];
        }
        $token = trim($checkIntegration->pivot->token);
        $shopDomain = trim($checkIntegration->pivot->external_id);

        try {
            $api->setShop($shopDomain);
            $api->setAccessToken($token);

            $shopMetafields = $api->rest('GET', '/admin/metafields.json', [
                'limit'  => 250,
                'fields' => 'id,namespace,key',
            ])->body->metafields;
        } catch (\Exception $e) {
            Log::error('Job[ShopifyMetafieldInstall]: Error => '.$e->getMessage());

            return [];
        }

        // Keep track of whats created
        $created = [];

        $merchant_api_key = '';
        $merchant_api_secret = '';

        $getMerchantDetails = $merchantModel->getDetails($this->merchant);
        if(!$getMerchantDetails){
            // create details record
            $merchant_api_key = str_random(60);
            $merchant_api_secret = str_random(60);
            $data = [
                'api_key' => $merchant_api_key,
                'api_secret' => $merchant_api_secret
            ];
            $merchantModel->saveDetails($this->merchant, $data);
        }else {
            if (! isset($getMerchantDetails->api_key) || ! trim($getMerchantDetails->api_key) || ! isset($getMerchantDetails->api_secret) || ! trim($getMerchantDetails->api_secret)) {
                // update api key & secret
                $merchant_api_key = str_random(60);
                $merchant_api_secret = str_random(60);
                $data = [
                    'api_key' => $merchant_api_key,
                    'api_secret' => $merchant_api_secret
                ];
                $merchantModel->saveDetails($this->merchant, $data);
            }else{
                $merchant_api_key = trim($getMerchantDetails->api_key);
                $merchant_api_secret = trim($getMerchantDetails->api_secret);
            }
        }

        $dynamicMetafields = [];
        $dynamicMetafields[] = [
            'namespace' => 'lootly',
            'key' => 'api_key',
            'value' => $merchant_api_key,
            'value_type' => 'string'
        ];
        $dynamicMetafields[] = [
            'namespace' => 'lootly',
            'key' => 'api_secret',
            'value' => $merchant_api_secret,
            'value_type' => 'string'
        ];

        foreach ($dynamicMetafields as $metafield) {
            // Check if the required metafield exists on the shop
            Log::info('Job[ShopifyMetafieldInstall]: Creating\Updating dynamic metafield ['.$metafield['key'].']');
            try {
                $existingMetafield = $this->metafieldExists($shopMetafields, $metafield);
                if (! $existingMetafield) {
                    Log::info('Job[ShopifyMetafieldInstall]: No existing metafield ['.$metafield['key'].']. Creating...');
                    // It does not... create the metafield
                    $new_metafield = $api->rest('POST', '/admin/metafields.json', [
                        'metafield' => $metafield,
                    ]);

                    $shopMetafields[] = $new_metafield->body->metafield;

                    $created[] = $metafield;
                }else{
                    Log::info('Job[ShopifyMetafieldInstall]: Metafield exists ['.$metafield['key'].']. Updating...');
                    $update_metafield = $api->rest('PUT', '/admin/metafields/'.$existingMetafield->id.'.json', [
                        'metafield' => [
                            'id' => $existingMetafield->id,
                            'value' => $metafield['value'] ?? '',
                            'value_type' => $metafield['value_type'] ?? ''
                        ]
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Job[ShopifyMetafieldInstall]: Error => ('.$metafield['namespace'].'/'.$metafield['key'].') '.$e->getMessage());
            }
        }

        foreach ($this->metafields as $metafield) {
            // Check if the required metafield exists on the shop
            Log::info('Job[ShopifyMetafieldInstall]: Creating\Updating static metafield ['.$metafield['key'].']');
            try {
                if (! $this->metafieldExists($shopMetafields, $metafield)) {
                    // It does not... create the metafield
                    Log::info('Job[ShopifyMetafieldInstall]: No existing metafield ['.$metafield['key'].']. Creating...');
                    $api->rest('POST', '/admin/metafields.json', [
                        'metafield' => $metafield,
                    ]);

                    $created[] = $metafield;
                }
            } catch (\Exception $e) {
                Log::error('Job[ShopifyMetafieldInstall]: Error => ('.$metafield['namespace'].'/'.$metafield['key'].') '.$e->getMessage());
            }
        }

        Log::info('Job[ShopifyMetafieldInstall]: Status => Completed');

        return $created;
    }

    /**
     * Check if metafield is in the list.
     *
     * @param array $shopMetafields The metafields installed on the shop
     * @param array $metafield      The metafield
     *
     * @return bool
     */
    protected function metafieldExists(array $shopMetafields, array $metafield)
    {
        foreach ($shopMetafields as $shopMetafield) {
            if ($shopMetafield->namespace === $metafield['namespace'] && $shopMetafield->key === $metafield['key']) {
                // Found the metafield in our list
                return $shopMetafield;
            }
        }

        return false;
    }
}
