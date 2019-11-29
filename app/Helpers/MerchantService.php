<?php

namespace App\Helpers;

use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Eloquent\Criteria\WithActiveIntegrations;
use Illuminate\Support\Facades\Log;

class MerchantService
{
    protected $merchants;

    public function __construct(MerchantRepository $merchants)
    {
        $this->merchants = $merchants;
    }

    public function getStoreIntegration($merchantId)
    {
        // Get Merchant Data
        try {
            $merchant = $this->merchants->withCriteria([
                new WithActiveIntegrations(),
            ])->findWhereFirst([
                'id' => $merchantId,
            ]);
        } catch (\Exception $exception) {
            throw $exception;
        }

        $integrations = $merchant->integrationsWithToken;

        if($integrations->where('slug', 'custom-api')->count() > 0)
            return $integrations->where('slug', 'custom-api')->first();

        if (! isset($integrations) || ! count($integrations)) {
            throw new \Exception('There is no active integrations found.');
        }

        // Find Store integration
        // $available_integrations = config('integrations.store_integrations', []);

        // $integration = null;

        // for ($i = 0; $i < count($integrations); $i++) {
        //     if (in_array($integrations[$i]->slug, $available_integrations)) {
        //         $integration = $integrations[$i];
        //         break;
        //     }
        // }

        return $integrations->first();
    }

    public function deactivateIntegration($merchant, $integrationId)
    {
        $this->merchants->updateIntegrations($merchant, $integrationId, [
            'status' => 0,
        ]);
    }

    public function deactivateEcommerceIntegrations($merchant, $except = [])
    {
        $activeEcommerceIntegrations = [];

        try {
            $merchantWithActiveIntegrations = $this->merchants->withCriteria([
                new WithActiveIntegrations(),
            ])->findWhereFirst([
                'id' => $merchant->id,
            ]);

            $activeIntegrations = $merchantWithActiveIntegrations->integrationsWithToken;
        } catch (\Exception $exception) {
            //
        }

        if (isset($activeIntegrations) && count($activeIntegrations)) {
            // Filter only Store integrations
            $available_integrations = config('integrations.store_integrations', []);

            for ($i = 0; $i < count($activeIntegrations); $i++) {
                if (in_array($activeIntegrations[$i]->slug, $available_integrations)) {
                    $activeEcommerceIntegrations[] = $activeIntegrations[$i];
                }
            }
        }

        for ($i = 0; $i < count($activeEcommerceIntegrations); $i++) {
            if (! in_array($activeEcommerceIntegrations[$i]->id, $except)) {
                app('merchant_service')->deactivateIntegration($merchant, $activeEcommerceIntegrations[$i]->id);

                switch ($activeEcommerceIntegrations[$i]->slug) {
                    case 'shopify':
                        app('shopify_ecommerce_integration')->uninstallIntegration($activeEcommerceIntegrations[$i]);
                        break;
                    case 'magento':
                        app('magento_ecommerce_integration')->uninstallIntegration($activeEcommerceIntegrations[$i]);
                        break;
                    case 'woocommerce':
                        app('woocommerce_ecommerce_integration')->uninstallIntegration($activeEcommerceIntegrations[$i]);
                        break;
                    case 'volusion':
                        app('volusion_ecommerce_integration')->uninstallIntegration($activeEcommerceIntegrations[$i]);
                        break;
                    // ...
                }
            }
        }
    }
}