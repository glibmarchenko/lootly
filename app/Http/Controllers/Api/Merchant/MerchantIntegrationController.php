<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Jobs\ShopifyAssetInstaller;
use App\Jobs\ShopifyMetafieldInstaller;
use App\Jobs\ShopifyScripttagInstaller;
use App\Jobs\ShopifyWebhookInstaller;
use App\Helpers\EcommerceIntegration\BigcommerceEcommerceIntegrationService;
use App\Merchant;
use App\Models\Integration;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\HasIntegrationWhere;
use App\Repositories\MerchantRepository;
use App\Transformers\MerchantIntegrationTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MerchantIntegrationController extends Controller
{
    protected $merchantModel;

    protected $merchants;

    protected $customers;

    protected  $bcService;

    public function __construct(
        MerchantRepository $merchantRepository,
        \App\Repositories\Contracts\MerchantRepository $merchants,
        CustomerRepository $customers,
        BigcommerceEcommerceIntegrationService $bcService
    ) {
        $this->merchantModel = $merchantRepository;
        $this->merchants = $merchants;
        $this->customers = $customers;
        $this->bcService = $bcService;
    }

    public function get(Request $request, Merchant $merchant)
    {
        $integrations = $this->merchantModel->getIntegrations($merchant);

        return fractal($integrations)->transformWith(new MerchantIntegrationTransformer)->toArray();
    }

    public function find(Request $request, Merchant $merchant, Integration $integration)
    {
        $integration = $this->merchantModel->findIntegration($merchant, $integration);

        return fractal($integration)->transformWith(new MerchantIntegrationTransformer)->toArray();
    }

    public function update(Request $request, Merchant $merchant, Integration $integration)
    {
        $integrationValidatorRules = config("integrations.$integration->slug.validator_rules");
        if ($integrationValidatorRules && count($integrationValidatorRules)) {
            $request->validate($integrationValidatorRules);
        }

        $settingsFields = config("integrations.$integration->slug.settings_fields");

        $settings = [];

        if ($settingsFields && count($settingsFields)) {
            foreach ($settingsFields as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $fieldName => $fieldType) {
                        try {
                            $settings[$key][$fieldName] = call_user_func($fieldType.'val', $request->get($key)[$fieldName] ?? null);
                        } catch (\Exception $e) {
                            $settings[$key][$fieldName] = null;
                        }
                    }
                } else {
                    try {
                        $settings[$key] = call_user_func($value.'val', $request->get($key) ?? null);
                    } catch (\Exception $e) {
                        $settings[$key] = null;
                    }
                }
            }
        }

        $data = [
            'settings' => json_encode($settings),
            'status'   => boolval($request->get('status')),
        ];

        $integration = $this->merchantModel->updateIntegration($merchant, $integration, $data);

        return null;
    }

    public function impersonationConfig(Request $request, Merchant $merchant, $customerId)
    {

        $merchantDetails = $this->merchants->withCriteria([
            new EagerLoad(['detail']),
        ])->find($merchant->id);

        $api_key = '';
        $api_secret = '';
        $shop_domain = '';

        if ($merchantDetails && isset($merchantDetails->detail)) {
            $api_key = $merchantDetails->detail->api_key;
            $api_secret = $merchantDetails->detail->api_secret;
            $shop_domain = $merchantDetails->detail->ecommerce_shop_domain;
        }

        $customer = $this->customers->withCriteria([
            new ByMerchant($merchant->id),
        ])->find($customerId);

        return response()->json([
            'data' => [
                'provider'           => config('app.url'),
                'api_key'            => $api_key ?? '',
                'shop_domain'        => $shop_domain ?? '',
                'shop_id'            => md5($shop_domain.$api_secret),
                'customer_id'        => $customer->ecommerce_id ?? '',
                'customer_signature' => md5($customer->ecommerce_id.$api_secret),
            ],
        ], 200);
    }

    public function reinstallWidgetCode(Request $request, Merchant $merchant, Integration $integration)
    {
        try {

            $merchantDetails = $this->merchants->withCriteria([
                new EagerLoad(['detail']),
            ])->find($merchant->id);

            /*$merchantDetails = $this->merchants->withCriteria([
                new HasIntegrationWhere([
                    'integration_id' => $integration->id,
                ]),
            ])->find($merchant->id);*/
        } catch (\Exception $e) {
            return response()->json(['message' => 'Request data is not valid'], 403);
        }

        // get customer info from store by ecommerce id
        switch ($integration->slug) {
            case 'shopify':
                $webhooks = config('integrations.shopify.webhooks');
                if (count($webhooks) > 0) {
                    dispatch(new ShopifyWebhookInstaller($merchant, $webhooks));
                }

                $scripttags = config('integrations.shopify.scripttags');
                if (count($scripttags) > 0) {
                    dispatch(new ShopifyScripttagInstaller($merchant, $scripttags));
                }

                $metafields = config('integrations.shopify.metafields');
                dispatch(new ShopifyMetafieldInstaller($merchant, $metafields));

                $assets = config('integrations.shopify.assets');
                if (count($assets) > 0) {
                    dispatch(new ShopifyAssetInstaller($merchant, $assets));
                }
                break;
            case 'bigcommerce':

                Log::info('reinstalling widget code');

                //Getting token
                $bigcommerceIntegration = $this->bcService->getBigcommerceIntegration();
                $checkIntegration = $this->merchantModel->findIntegrationWithToken( $merchant, $bigcommerceIntegration );
                $token = trim($checkIntegration->pivot->token);

                //Getting hash
                $hash = $checkIntegration->pivot->external_id;

                $this->bcService->removeLootlyScripts( $token, $hash, $merchantDetails->detail );

                //from BigCommerce integration controller
                $this->bcService->makeScriptApiRequests( 'v3/content/scripts', $token, $hash, $merchantDetails->detail );
                Log::info( 'Lootly scripts were installed' );

                break;
            case 'magento':
                // ...
                break;
            case 'woocommerce':
                // ...
                break;
            case 'volusion':
                // ...
                break;
            // ...
        }
    }

    public function getActiveEcommerceIntegration(Merchant $merchant)
    {
        try {
            $integration = app('merchant_service')->getStoreIntegration($merchant->id);

        } catch (\Exception $exception) {
            return response()->json([], 404);
        }

        if ($integration) {
            return response()->json(['data' => $integration->toArray()], 200);
        }

        return response()->json([], 200);
    }
}
