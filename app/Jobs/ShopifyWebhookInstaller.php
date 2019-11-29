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

class ShopifyWebhookInstaller implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The shop.
     *
     * @var \App\Merchant
     */
    protected $merchant;

    /**
     * Webhooks list.
     *
     * @var array
     */
    protected $webhooks;

    /**
     * Create a new job instance.
     *
     * @param \App\Merchant $merchant The merchant object
     * @param array         $webhooks The webhook list
     *
     * @return void
     */
    public function __construct(Merchant $merchant, array $webhooks)
    {
        $this->merchant = $merchant;
        $this->webhooks = $webhooks;
    }

    /**
     * Execute the job.
     *
     * @return array
     */
    public function handle()
    {
        Log::info('Job[ShopifyWebhookInstall]: Status => Started');

        $api = ShopifyApi::setup();

        $integrationModel = new IntegrationRepository();
        $shopifyIntegration = $integrationModel->findBySlug('shopify');

        if (! $shopifyIntegration) {
            Log::error('Job[ShopifyWebhookInstall]: Error => No Shopify integration found!');

            return [];
        }

        $merchantModel = new MerchantRepository();
        $checkIntegration = $merchantModel->findIntegrationWithToken($this->merchant, $shopifyIntegration);
        if (! $checkIntegration || ! isset($checkIntegration->pivot) || ! $checkIntegration->pivot->status || ! trim($checkIntegration->pivot->token)) {
            Log::error('Job[ShopifyWebhookInstall]: Error => No Shopify integration found for merchant with ID #'.$this->merchant->id);

            return [];
        }
        $token = trim($checkIntegration->pivot->token);
        $shopDomain = trim($checkIntegration->pivot->external_id);

        try {
            $api->setShop($shopDomain);
            $api->setAccessToken($token);

            $shopWebhooks = $api->rest('GET', '/admin/webhooks.json', [
                    'limit'  => 250,
                    'fields' => 'id,address',
                ])->body->webhooks;
        } catch (\Exception $e) {
            Log::error('Job[ShopifyWebhookInstall]: Error => '.$e->getMessage());

            return [];
        }

        // Keep track of whats created
        $created = [];

        foreach ($this->webhooks as $webhook) {
            // Check if the required webhook exists on the shop
            try {
                if (! $this->webhookExists($shopWebhooks, $webhook)) {
                    // It does not... create the webhook
                    $api->rest('POST', '/admin/webhooks.json', [
                            'webhook' => $webhook,
                        ]);

                    $created[] = $webhook;
                }
            } catch (\Exception $e) {
                Log::error('Job[ShopifyWebhookInstall]: Error => ('.$webhook['topic'].') '.$e->getMessage());
            }
        }

        Log::info('Job[ShopifyWebhookInstall]: Status => Completed');

        return $created;
    }

    /**
     * Check if webhook is in the list.
     *
     * @param array $shopWebhooks The webhooks installed on the shop
     * @param array $webhook      The webhook
     *
     * @return bool
     */
    protected function webhookExists(array $shopWebhooks, array $webhook)
    {
        foreach ($shopWebhooks as $shopWebhook) {
            if ($shopWebhook->address === $webhook['address']) {
                // Found the webhook in our list
                return true;
            }
        }

        return false;
    }
}
