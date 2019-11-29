<?php

namespace App\Jobs\Integrations\Shopify;

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

class LootlyLauncherAssetIncludeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The shop.
     *
     * @var \App\Merchant
     */
    protected $merchant;

    /**
     * Shopify theme ID.
     *
     */
    protected $shopify_theme_id;

    /**
     * Asset data.
     *
     */
    protected $asset;

    /**
     * Create a new job instance.
     *
     * @param \App\Merchant $merchant             The merchant object
     * @param string        $shopifyThemeId       Shopify theme ID
     * @param               $asset                The asset object
     *
     * @return void
     */
    public function __construct(Merchant $merchant, $shopifyThemeId, $asset)
    {
        $this->merchant = $merchant;
        $this->shopify_theme_id = $shopifyThemeId;
        $this->asset = $asset;
    }

    /**
     * Execute the job.
     *
     * @return array
     */
    public function handle()
    {
        Log::info('Job[LootlyLauncherAssetInclude]: Status => Started');

        if (! isset($this->asset->key) || ! trim($this->asset->key)) {
            Log::error('Job[LootlyLauncherAssetInclude]: Error => No asset found');

            return;
        }

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

            $layoutThemeAsset = $api->rest('GET', '/admin/themes/'.$this->shopify_theme_id.'/assets.json?asset[key]=layout/theme.liquid&theme_id='.$this->shopify_theme_id)->body->asset;
        } catch (\Exception $e) {
            Log::error('Job[LootlyLauncherAssetInclude]: Error => ('.$this->asset->key.') '.$e->getMessage());

            return;
        }

        $themeBodyOrig = $layoutThemeAsset->value;

        $themeBody = $layoutThemeAsset->value;
        $themeBody = preg_replace('/{% include \'?lootly-launcher\'? %}/m', '', $themeBody);
        $themeBody = preg_replace('/<\/body>/m', "{% include 'lootly-launcher' %}\n</body>", $themeBody);

        try {
            $api->rest('PUT', '/admin/themes/'.$this->shopify_theme_id.'/assets.json', [
                    'asset' => [
                        'key'   => 'layout/theme.liquid',
                        'value' => $themeBody,
                    ],
                ]);
        } catch (\Exception $e) {
            Log::error('Job[LootlyLauncherAssetInclude]: Error => (layout/theme.liquid) '.$e->getMessage());

            return;
        }

        Log::info('Job[LootlyLauncherAssetInclude]: Status => Completed');

        return;
    }
}
