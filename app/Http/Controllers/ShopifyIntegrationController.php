<?php

namespace App\Http\Controllers;

use App\Jobs\ShopifyAssetInstaller;
use App\Jobs\ShopifyMetafieldInstaller;
use App\Jobs\ShopifyScripttagInstaller;
use App\Jobs\ShopifyWebhookInstaller;
use App\Mail\MerchantWelcome;
use App\Repositories\Contracts\IntegrationRepository;
use App\Repositories\Contracts\MerchantDetailsRepository;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Eloquent\Criteria\WithActiveIntegrations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class ShopifyIntegrationController extends Controller
{
    protected $sh;

    protected $api_key;

    protected $shared_secret;

    protected $integrations;

    protected $users;

    protected $merchants;

    protected $merchantDetails;

    /**
     * ShopifyIntegrationController constructor.
     *
     * @param \App\Repositories\Contracts\IntegrationRepository     $integrations
     * @param \App\Repositories\Contracts\UserRepository            $users
     * @param \App\Repositories\Contracts\MerchantRepository        $merchants
     * @param \App\Repositories\Contracts\MerchantDetailsRepository $merchantDetails
     */
    public function __construct(
        IntegrationRepository $integrations,
        UserRepository $users,
        MerchantRepository $merchants,
        MerchantDetailsRepository $merchantDetails
    ) {
        $this->integrations = $integrations;
        $this->users = $users;
        $this->merchants = $merchants;
        $this->merchantDetails = $merchantDetails;
    }

    public function install(Request $request)
    {

        //dd( $request );
        // Validate request data
        if (! $request->get('shop') || ! $request->get('hmac')) {
            return abort(422, 'Invalid request.');
        }

        // Verify shop domain
        $shopDomain = app('shopify_api')->sanitizeShopDomain($request->get('shop'));
        if (! $shopDomain) {
            return abort(422, 'Invalid shop domain.');
        }

        // Set API client and verify HMAC
        $api = app('shopify_api')->setup();
        $api->setShop($shopDomain);
        if (! $api->verifyRequest($request->except('_url'))) {
            return abort(422, 'Invalid signature.');
        }

        // Get Shopify integration info
        try {
            $shopifyIntegration = $this->integrations->findWhereFirst([
                'slug'   => 'shopify',
                'status' => 1,
            ]);
        } catch (\Exception $e) {
            // No Shopify integration
        }

        // Check Shopify integration status
        if (! isset($shopifyIntegration) || ! $shopifyIntegration) {
            return abort(405, 'Shopify integraion is disabled.');
        }

        // Check merchant with same active Shopify store integration
        $this->integrations->clearEntity();
        $merchants = $this->integrations->findMerchantWhere($shopifyIntegration->id, [
            'status'      => 1,
            'external_id' => $shopDomain,
        ]);

        if (count($merchants) == 1) {
            // There is one merchant
            $targetMerchant = $merchants[0];
            // Login and redirect to Dashboard
            try {
                $user = $this->users->find($targetMerchant->owner_id);
                if (Auth::user()) {
                    Auth::logout();
                }
                Auth::loginUsingId($user->id);

                // Set current merchant to targetMerchant
                Auth::user()->switchToTeam($targetMerchant);

                return redirect()->route('dashboard');
            } catch (\Exception $e) {
                return abort('Unexpected error in the authentication system.');
            }
        } elseif (count($merchants) > 1) {
            // There are more than one merchants

            // Redirect to login page
            return redirect()->route('login');
        }

        // Generate redirect URL
        $redirectUrl = $api->getAuthUrl(config('integrations.shopify.api_scopes'), secure_url(config('integrations.shopify.api_redirect')));

        // Redirect to Shopify
        return redirect($redirectUrl);
    }

    public function callback(Request $request)
    {
        // Validate request data
        if (! $request->get('shop') || ! $request->get('hmac')) {
            return abort(422, 'Invalid request.');
        }

        // Verify shop domain
        $shopDomain = app('shopify_api')->sanitizeShopDomain($request->get('shop'));
        if (! $shopDomain) {
            abort(422, 'Store URL is not valid.');
        }

        // Set API client and verify HMAC
        $api = app('shopify_api')->setup();
        $api->setShop($shopDomain);
        if (! $api->verifyRequest($request->except('_url'))) {
            return abort(422, 'Invalid signature.');
        }

        // Get access token
        $code = $request->get('code') ?: null;
        $accessToken = $api->requestAccessToken($code);

        // Save shop domain and access token to session
        session([
            'shopify_integration' => [
                'shop_domain'  => $shopDomain,
                'access_token' => $accessToken,
            ],
        ]);

        // Redirect to merchant selection page
        return redirect('/app/shopify/finish');
    }

    public function selectMerchantAndConnectShopify(Request $request)
    {
        // Validate session data
        if (! session('shopify_integration') || ! isset(session('shopify_integration')['shop_domain']) || ! isset(session('shopify_integration')['access_token'])) {
            session()->forget([
                'redirect_queue',
            ]);

            return redirect()->route('login');
        }

        $shopDomain = trim(session('shopify_integration')['shop_domain']);
        $accessToken = trim(session('shopify_integration')['access_token']);

        // Save return url path
        $redirectQueue = [];
        // $redirectQueue[] = route('dashboard');
        $redirectQueue[] = url()->full();

        // Show prompt "Do you already have account" if not logged in
        if (! Auth::user() && (! $request->get('new-user') || $request->get('new-user') != 1)) {
            session(['redirect_queue' => $redirectQueue]);

            return view('tmp.do-you-already-have-account');
        }

        // Show prompt "Select merchant" if no merchant selected
        if (Auth::user() && ! $request->get('merchant_id')) {
            session(['redirect_queue' => $redirectQueue]);

            return redirect('/select-account');
        }

        // Merchant for Shopify integration
        $merchantForIntegration = null;

        // Existing user
        if (! $request->get('new-user') && Auth::user()) {
            $user = Auth::user();
            $merchant_id = intval($request->get('merchant_id'));
            if (! $merchant_id) {
                return abort(422, 'Lootly merchant ID is not valid.');
            }
            try {
                $merchant = $this->merchants->find($merchant_id);
            } catch (\Exception $e) {
                // No merchant with such ID
            }
            if (! isset($merchant) || ! $merchant) {
                return abort(422, 'Lootly merchant ID is not valid.');
            }
            if ($user->roleOn($merchant) != 'owner' && ! $user->ownsTeam($merchant)) {
                return abort(403, 'You are not allowed to perform this action.');
            }
            $merchantForIntegration = $merchant;
        }

        session()->forget([
            'redirect_queue',
            'shopify_integration',
        ]);

        // Create Shopify integration record
        return $this->createIntegration($shopDomain, $accessToken, $merchantForIntegration);
    }

    private function createIntegration($shop_domain, $accessToken, $merchant = null)
    {
        // Get Shopify integration info
        try {
            $shopifyIntegration = $this->integrations->findWhereFirst([
                'slug'   => 'shopify',
                'status' => 1,
            ]);
        } catch (\Exception $e) {
            // No Shopify integration
        }

        // Check Shopify integration status
        if (! isset($shopifyIntegration) || ! $shopifyIntegration) {
            return abort(500, 'An error has occurred while attempting to connect integration.');
        }

        // Get Shopify shop info
        try {
            $api = app('shopify_api')->setup();
            $api->setShop($shop_domain);
            $api->setAccessToken($accessToken);

            $shop = $api->rest('GET', '/admin/shop.json', [])->body->shop;
        } catch (\Exception $e) {
            return abort(500, 'An error has occurred while attempting to get shop data.');
        }

        $email = $shop->email;

        // New merchant
        if (! $merchant) {
            // Check if user already exists
            try {
                $user = $this->users->findWhereFirst([
                    'email' => $email,
                ]);
            } catch (\Exception $e) {
                // No user with such email
            }

            // Create new user
            if (! isset($user) || (isset($user) && ! $user)) {
                // Get shop owner name
                $owner_name = $this->split_name($shop->shop_owner) ?: [];

                // Prepare new user data
                $new_user_data = [];
                $new_user_data['email'] = $email;
                $new_user_data['first_name'] = isset($owner_name['first_name']) ? $owner_name['first_name'] : '';
                $new_user_data['last_name'] = isset($owner_name['last_name']) ? $owner_name['last_name'] : '';
                $new_user_data['password'] = str_random(8);
                $new_user_data['plan'] = 0;

                // Store user
                $user = app('user_service')->createNewUser($new_user_data);
                if (! $user) {
                    return abort(500, 'An error has occurred while attempting to create new user record.');
                }

                /*// Setup Mail API Client
                $mailClient = app('postmark_api')->setup();
                $signature = env('POSTMARK_SIGNATURE');

                // Render mail body
                $mailBody = View::make('emails.auth.you-have-been-successfully-registered', compact('new_user_data'))
                    ->render();

                // Send email to user with account data
                try {
                    $mailClient->sendEmail($signature, $user->email, __('Welcome to '.config('app.name').'!'), $mailBody);
                } catch (\Exception $e) {
                    Log::error('Mail error: '.$e->getMessage());
                }*/

                Mail::to($user->email)->queue(new MerchantWelcome($user, true, $new_user_data['password']));
            }

            // Create New Merchant
            $merchant = app('user_service')->configureMerchant($user, [
                'company' => $shop->name,
                'website' => $shop_domain,
            ]);
            if (! $merchant) {
                return abort(500, 'An error has occurred while attempting to create new merchant record.');
            }

            // Login with created user credentials
            Auth::login($user);
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

        // Create/Update Shopify integration
        try {
            // Default Shopify integration settings
            $defaultSettings = [
                'order_settings' => [
                    'reward_status'           => 'paid',
                    'subtract_status'         => 'refunded',
                    'include_taxes'           => 0,
                    'include_shipping'        => 0,
                    'exclude_discounts'       => 1,
                    'include_previous_orders' => 1,
                ],
            ];

            // Update merchant integrations data
            $this->merchants->clearEntity();

            try {
                app('merchant_service')->deactivateEcommerceIntegrations($merchant, [$shopifyIntegration->id]);
            } catch (\Exception $e) {
                Log::error('Shopify integration installing: Error on attempting to deactivate e-commerce integration (merchant #'.$merchant->id.').');
                Log::error($e->getMessage());
            }

            $this->merchants->updateIntegrations($merchant, $shopifyIntegration->id, [
                'status'      => 1,
                'external_id' => $shop_domain,
                'token'       => $accessToken,
                'settings'    => json_encode($defaultSettings),
            ]);

            // Update merchant details
            $this->merchantDetails->updateOrCreate([
                'merchant_id' => $merchant->id,
            ], [
                'ecommerce_shop_domain' => $shop_domain,
            ]);
        } catch (\Exception $exception) {
            Log::error('Create/Update Shopify integration error: '.$exception->getMessage());

            return abort(500, 'An unexpected error has occurred while attempting to save merchant integration.');
        }

        // Run jobs after successful Shopify installation
        $this->installWebhooks($merchant);
        $this->installScripttags($merchant);
        $this->installMetafields($merchant);
        $this->installAssets($merchant);

        // Set current merchant
        Auth::user()->switchToTeam($merchant);

        return redirect()->route('dashboard');
    }

    protected function installWebhooks($merchant)
    {
        $webhooks = config('integrations.shopify.webhooks');
        if (count($webhooks) > 0) {
            dispatch(new ShopifyWebhookInstaller($merchant, $webhooks));
        }
    }

    protected function installScripttags($merchant)
    {
        $scripttags = config('integrations.shopify.scripttags');
        if (count($scripttags) > 0) {
            dispatch(new ShopifyScripttagInstaller($merchant, $scripttags));
        }
    }

    protected function installMetafields($merchant)
    {
        $metafields = config('integrations.shopify.metafields');
        dispatch(new ShopifyMetafieldInstaller($merchant, $metafields));
    }

    protected function installAssets($merchant)
    {
        $assets = config('integrations.shopify.assets');
        if (count($assets) > 0) {
            dispatch(new ShopifyAssetInstaller($merchant, $assets));
        }
    }

    private function split_name($name)
    {
        $parts = explode(' ', $name);

        $name = [];
        $name['first_name'] = trim(array_shift($parts));
        $name['last_name'] = trim(array_pop($parts));
        $name['middle_name'] = trim(implode(' ', $parts));

        return $name;
    }
}
