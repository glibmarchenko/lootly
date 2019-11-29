<?php

namespace App\Jobs\Webhooks\Integrations\Shopify;

use App\Repositories\Contracts\IntegrationRepository;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\SubscriptionRepository;
use App\Repositories\Eloquent\Criteria\HasIntegrationWhere;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class AppUninstalledJob implements ShouldQueue
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
     * @var array
     */
    protected $headers;

    protected $integrationType;

    protected $integrations;

    protected $merchants;

    protected $subscriptions;

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
     * @param \App\Repositories\Contracts\IntegrationRepository  $integrations
     * @param \App\Repositories\Contracts\MerchantRepository     $merchants
     *
     * @param \App\Repositories\Contracts\SubscriptionRepository $subscriptions
     *
     * @return void
     */
    public function handle(
        IntegrationRepository $integrations,
        MerchantRepository $merchants,
        SubscriptionRepository $subscriptions
    ) {
        $this->integrations = $integrations;
        $this->merchants = $merchants;
        $this->subscriptions = $subscriptions;

        $shopDomain = isset($this->headers['x-shopify-shop-domain']) ? ($this->headers['x-shopify-shop-domain'][0] ?? '') : '';

        Log::info('Shopify App for '.$shopDomain.' was uninstalled.');

        //$merchantModel = new MerchantRepository();
        //$integrationModel = new IntegrationRepository();

        try {
            $shopifyIntegration = $this->integrations->findWhereFirst([
                'slug'   => 'shopify',
                'status' => 1,
            ]);
        } catch (\Exception $exception) {
            //
        }

        if (! isset($shopifyIntegration) || ! $shopifyIntegration) {
            Log::warning('No integration with slug "shopify"');

            return;
        }

        //$targetMerchants = $integrationModel->getMerchantsWithActiveIntegration($shopifyIntegration, $shopDomain);
        $targetMerchants = $this->merchants->withCriteria([
            new HasIntegrationWhere([
                'integrations.id'                   => $shopifyIntegration->id,
                'merchant_integrations.external_id' => $shopDomain,
            ]),
        ])->all();

        if ($targetMerchants && count($targetMerchants)) {
            foreach ($targetMerchants as $merchant) {
                Log::info('Deactivating Shopify integration for merchant #'.$merchant->id);
                $this->merchants->updateIntegrations($merchant, $shopifyIntegration->id, [
                    'status' => 0,
                ]);
                /*$merchantModel->updateIntegrations($merchant, $shopifyIntegration->id, [
                    'status' => 0,
                ]);*/

//                // NOTE: Cancel subscription and downgrade plan to Free
//                $this->subscriptions->clearEntity();
//                $this->subscriptions->updateOrCreate([
//                    'merchant_id' => $merchant->id,
//                ], [
//                    'status' => 'cancelled',
//                ]);
//                try {
//                    $this->merchants->clearEntity();
//                    $this->merchants->update($merchant->id, [
//                        'plan_id' => null,
//                    ]);
//                } catch (\Exception $exception) {
//                    Log::error('Cannot downgrade plan for merchant #'.$merchant->id.' on app/uninstalled webhook');
//                }

            }
        } else {
            Log::warning('No merchants with active Shopify integration');
        }
    }
}