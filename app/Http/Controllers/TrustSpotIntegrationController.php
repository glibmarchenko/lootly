<?php

namespace App\Http\Controllers;

use App\Facades\ShopifyApi;
use App\Jobs\ShopifyAssetInstaller;
use App\Jobs\ShopifyMetafieldInstaller;
use App\Jobs\ShopifyScripttagInstaller;
use App\Jobs\ShopifyWebhookInstaller;
use App\Repositories\IntegrationRepository;
use App\Repositories\MerchantRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Laravel\Spark\Contracts\Interactions\Settings\Teams\CreateTeam;
use Laravel\Spark\Contracts\Repositories\UserRepository;
use Laravel\Spark\Spark;
use OhMyBrew\BasicShopifyAPI;

class TrustSpotIntegrationController extends Controller
{
    protected $api_key;

    protected $shared_secret;

    public function __construct()
    {
        $this->api_key = env('TRUSTSPOT_APP_KEY');
        $this->shared_secret = env('TRUSTSPOT_APP_SECRET');
    }

    public function install(Request $request)
    {
        $redirectQueue = [];
        $redirectQueue[] = url()->full();

        if (! Auth::user() && (! $request->get('new-user') || $request->get('new-user') != 1)) {
            // Show prompt "Do you already have lootly account"
            session(['redirect_queue' => $redirectQueue]);

            return view('tmp.do-you-already-have-account');
        }

        if (Auth::user() && ! $request->get('merchant_id')) {
            session(['redirect_queue' => $redirectQueue]);

            return redirect('/select-account');
        }

        $trustSpotMerchantID = intval($request->get('trustspot_id'));

        if (! $trustSpotMerchantID) {
            abort(422, 'TrustSpot Merchant ID is not valid.');
        }

        $scopes = '';
        if (is_array(config('integrations.trustspot.api_scopes'))) {
            $scopes = implode(',', config('integrations.trustspot.api_scopes'));
        }

        $redirectUri = secure_url(config('integrations.trustspot.api_redirect'));


        $url = "https://trustspot.io/oauth/authorize?client_id={$this->api_key}&scope={$scopes}&redirect_uri={$redirectUri}";

        if (! $request->get('new-user') && Auth::user()) {
            session(['trustspot_integration_merchant' => $request->get('merchant_id')]);
        }

        return redirect($url);
    }

    // Callback after installation - creating integration here
    public function callback(Request $request)
    {
        $trustSpotMerchantID = intval($request->get('trustspot_id'));

        if (! $trustSpotMerchantID) {
            abort(422, 'TrustSpot Merchant ID is not valid.');
        }

        //if (! $api->verifyRequest($request->except('_url'))) {
            // Not valid, redirect to login and show the errors
            return abort(403, 'Invalid signature');
        //}

        $code = $request->get('code') ?: null;
        $accessToken = ''; //$api->requestAccessToken($code);

        $merchantForIntegration = null;

        if (session('trustspot_integration_merchant')) {
            if (! Auth::user()) {
                abort(403, 'You must be logged in to perform this action.');
            }
            $user = Auth::user();
            $merchant_id = intval(session('trustspot_integration_merchant'));
            if (! $merchant_id) {
                abort(422, 'Lootly merchant ID is not valid.');
            }
            $merchantModel = new MerchantRepository();
            $merchant = $merchantModel->find($merchant_id);
            if (! $merchant) {
                abort(422, 'Lootly merchant ID is not valid.');
            }
            if ($user->roleOn($merchant) != 'owner' && ! $user->ownsTeam($merchant)) {
                abort(403, 'You are not allowed to perform this action.');
            }
            $merchantForIntegration = $merchant;
        }

        return $this->createIntegration($trustSpotMerchantID, $accessToken, $merchantForIntegration);
    }

    private function createIntegration($externalId, $accessToken, $merchant = null)
    {
        $integrationModel = new IntegrationRepository();
        $integration = $integrationModel->findBySlug('trustspot');
        if (! $integration) {
            abort(500, 'An error has occurred while attempting to connect integration.');
        }

        try {
            // Get TrustSpot data
        } catch (\Exception $e) {
            abort(500, 'An error has occurred while attempting to get trustspot data.');
        }

        if (! $merchant) {

            abort(500, 'An error has occurred while attempting to get merchant data.');

            /*$email = ''; // $trustspot->email

            $user = User::where(['email' => $email])->first();
            if (! $user) {
                $owner_name = $this->split_name($shop->shop_owner) ?: [];

                // Create New User
                $new_user_data = [];
                $new_user_data['email'] = $email;
                $new_user_data['first_name'] = isset($owner_name['first_name']) ? $owner_name['first_name'] : '';
                $new_user_data['last_name'] = isset($owner_name['last_name']) ? $owner_name['last_name'] : '';
                $new_user_data['password'] = str_random(8);

                $user = Spark::interact(UserRepository::class.'@create', [$new_user_data]);
                if (! $user) {
                    abort(500, 'An error has occurred while attempting to create new user record.');
                }
                // @todo: Send email with password
            } else {
                // Create New Merchant
                $merchant = Spark::interact(CreateTeam::class, [
                    $user,
                    [
                        'name' => ($shop->name ?: 'New Store'),
                        'slug' => '',
                    ],
                ]);
                if (! $merchant) {
                    abort(500, 'An error has occurred while attempting to create new merchant record.');
                }
            }*/
        }

        // Create/Update Shopify integration
        $merchantModel = new MerchantRepository();
        try {

            $defaultSettings = [

            ];

            $merchantModel->updateIntegrations($merchant, $integration->id, [
                'status'      => 1,
                'external_id' => $externalId,
                'token'       => $accessToken,
                'settings'    => json_encode($defaultSettings),
            ]);
        } catch (\Exception $exception) {
            abort(500, 'An unexpected error has occurred while attempting to save merchant integration.');
        }

        $this->installWebhooks($merchant);

        return redirect('/integrations/manage');
    }

    protected function installWebhooks($merchant)
    {
        $webhooks = config('integrations.trustspot.webhooks');
        if (count($webhooks) > 0) {
            //dispatch(new ShopifyWebhookInstaller($merchant, $webhooks));
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
