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

class ShopifyAssetInstaller implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The shop.
     *
     * @var \App\Merchant
     */
    protected $merchant;

    /**
     * Assets list.
     *
     * @var array
     */
    protected $assets;

    /**
     * Create a new job instance.
     *
     * @param \App\Merchant $merchant The merchant object
     * @param array         $assets   The assets list
     *
     * @return void
     */
    public function __construct(Merchant $merchant, array $assets)
    {
        $this->merchant = $merchant;
        $this->assets = $assets;
    }

    /**
     * Execute the job.
     *
     * @return array
     */
    public function handle()
    {
        Log::info('Job[ShopifyAssetInstall]: Status => Started');

        $api = ShopifyApi::setup();

        $integrationModel = new IntegrationRepository();
        $shopifyIntegration = $integrationModel->findBySlug('shopify');

        if (! $shopifyIntegration) {
            Log::error('Job[ShopifyAssetInstall]: Error => No Shopify integration found!');

            return [];
        }

        $merchantModel = new MerchantRepository();
        $checkIntegration = $merchantModel->findIntegrationWithToken($this->merchant, $shopifyIntegration);
        if (! $checkIntegration || ! isset($checkIntegration->pivot) || ! $checkIntegration->pivot->status || ! trim($checkIntegration->pivot->token)) {
            Log::error('Job[ShopifyAssetInstall]: Error => No Shopify integration found for merchant with ID #'.$this->merchant->id);

            return [];
        }
        $token = trim($checkIntegration->pivot->token);
        $shopDomain = trim($checkIntegration->pivot->external_id);

        try {
            $api->setShop($shopDomain);
            $api->setAccessToken($token);

            $shopThemes = $api->rest('GET', '/admin/themes.json', [
                'fields' => 'id,role',
            ])->body->themes;

            if (! count($shopThemes)) {
                Log::error('Job[ShopifyAssetInstall]: Error => No Shopify themes found');

                return [];
            }

            $mainThemeIndx = array_search('main', array_column($shopThemes, 'role'));

            if ($mainThemeIndx === false) {
                Log::error('Job[ShopifyAssetInstall]: Error => No main Shopify theme found');

                return [];
            }

            $shopThemeId = $shopThemes[$mainThemeIndx]->id;
        } catch (\Exception $e) {
            Log::error('Job[ShopifyAssetInstall]: Error => '.$e->getMessage());

            return [];
        }

        // Keep track of whats created
        $created = [];

        foreach ($this->assets as $asset) {
            try {
                // Create the asset
                $assetResponse = $api->rest('PUT', '/admin/themes/'.$shopThemeId.'/assets.json', [
                    'asset' => $asset['data'],
                ]);

                if (isset($asset['afterInstallJob']) && trim($asset['afterInstallJob'])){
                    $classPath = trim($asset['afterInstallJob']);

                    if (! class_exists($classPath)) {
                        Log::error('Job[ShopifyAssetInstall]: Error => No class by path "'.$classPath.'" found');
                    }
                    // Dispatch
                    dispatch(new $classPath($this->merchant, $shopThemeId, $assetResponse->body->asset));
                }

                $created[] = $asset;
            } catch (\Exception $e) {
                Log::error('Job[ShopifyAssetInstall]: Error => ('.$asset['data']['key'].') '.$e->getMessage());
            }
        }

        Log::info('Job[ShopifyAssetInstall]: Status => Completed');

        return $created;
    }
}
