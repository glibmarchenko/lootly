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

class ShopifyScripttagInstaller implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The shop.
     *
     * @var \App\Merchant
     */
    protected $merchant;

    /**
     * Scripttag list.
     *
     * @var array
     */
    protected $scripttags;

    /**
     * Create a new job instance.
     *
     * @param \App\Merchant $merchant   The merchant object
     * @param array         $scripttags The scripttag list
     *
     * @return void
     */
    public function __construct(Merchant $merchant, array $scripttags)
    {
        $this->merchant = $merchant;
        $this->scripttags = $scripttags;
    }

    /**
     * Execute the job.
     *
     * @return array
     */
    public function handle()
    {
        Log::info('Job[ShopifyScripttagInstall]: Status => Started');

        $api = ShopifyApi::setup();

        $integrationModel = new IntegrationRepository();
        $shopifyIntegration = $integrationModel->findBySlug('shopify');

        if (! $shopifyIntegration) {
            Log::error('Job[ShopifyScripttagInstall]: Error => No Shopify integration found!');

            return [];
        }

        $merchantModel = new MerchantRepository();
        $checkIntegration = $merchantModel->findIntegrationWithToken($this->merchant, $shopifyIntegration);
        if (! $checkIntegration || ! isset($checkIntegration->pivot) || ! $checkIntegration->pivot->status || ! trim($checkIntegration->pivot->token)) {
            Log::error('Job[ShopifyScripttagInstall]: Error => No Shopify integration found for merchant with ID #'.$this->merchant->id);

            return [];
        }
        $token = trim($checkIntegration->pivot->token);
        $shopDomain = trim($checkIntegration->pivot->external_id);

        try {
            $api->setShop($shopDomain);
            $api->setAccessToken($token);

            $shopScripttags = $api->rest('GET', '/admin/script_tags.json', [
                'limit'  => 250,
                'fields' => 'id,src',
            ])->body->script_tags;
        } catch (\Exception $e) {
            Log::error('Job[ShopifyScripttagInstall]: Error => '.$e->getMessage());

            return [];
        }

        // Keep track of whats created
        $created = [];

        foreach ($this->scripttags as $scripttag) {
            // Check if the required scripttag exists on the shop
            try {
                if (! $this->scripttagExists($shopScripttags, $scripttag)) {
                    // It does not... create the scripttag
                    $api->rest('POST', '/admin/script_tags.json', [
                        'script_tag' => $scripttag,
                    ]);

                    $created[] = $scripttag;
                }
            } catch (\Exception $e) {
                Log::error('Job[ShopifyScripttagInstall]: Error => ('.$scripttag['src'].') '.$e->getMessage());
            }
        }

        Log::info('Job[ShopifyScripttagInstall]: Status => Completed');

        return $created;
    }

    /**
     * Check if scripttag is in the list.
     *
     * @param array $shopScripttags The scripttags installed on the shop
     * @param array $scripttag      The scripttag
     *
     * @return bool
     */
    protected function scripttagExists(array $shopScripttags, array $scripttag)
    {
        foreach ($shopScripttags as $shopScripttag) {
            if ($shopScripttag->src === $scripttag['src']) {
                // Found the scripttag in our list
                return true;
            }
        }

        return false;
    }
}
