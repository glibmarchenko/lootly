<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Repositories\Contracts\IntegrationRepository;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\MerchantDetailsRepository;
use App\Repositories\Contracts\UserRepository;
use App\Jobs\BigcommerceWebhookInstaller;
use App\Helpers\EcommerceIntegration\BigcommerceEcommerceIntegrationService;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use App\Mail\MerchantWelcome;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class BigcommerceIntegrationController extends Controller
{
    protected $baseURL;

    protected $users;

    protected $integrations;

    protected $merchants;

    protected $merchantModel;

    protected $merchantDetails;

    protected $bcService;

    public function __construct(
        UserRepository $users,
        IntegrationRepository $integrations,
        MerchantRepository $merchants,
        \App\Repositories\MerchantRepository $merchantModel,
        MerchantDetailsRepository $merchantDetails,
        BigcommerceEcommerceIntegrationService $bcService
    )
    {
        $this->baseURL = env('APP_URL');
        $this->users = $users;
        $this->integrations = $integrations;
        $this->merchants = $merchants;
        $this->merchantModel = $merchantModel;
        $this->merchantDetails = $merchantDetails;
        $this->bcService = $bcService;
    }

    public function getAccessToken(Request $request) {
        if (env('APP_ENV') === 'local') {
            return env('BC_LOCAL_ACCESS_TOKEN');
        } else {
            return $request->session()->get('access_token');
        }
    }

    public function install(Request $request)
    {
        // Make sure all required query params have been passed
        if (!$request->has('code') || !$request->has('scope') || !$request->has('context')) {
            return redirect()->action('BigcommerceIntegrationController@error')->with('error_message', 'Not enough information was passed to install this app.');
        }

        echo( "<div id='installing' style='padding: 3.2rem 3.2rem;'><p>Lootly is installing. Please wait a bit</p></div>" );

        try {
            $client = new Client();

            $result = $client->request('POST', 'https://login.bigcommerce.com/oauth2/token', [
                'json' => [
                    'client_id' => env('BC_APP_CLIENT_ID'),
                    'client_secret' => env('BC_APP_SECRET'),
                    'redirect_uri' => $this->baseURL . '/app/bigcommerce/install',
                    'grant_type' => 'authorization_code',
                    'code' => $request->input('code'),
                    'scope' => $request->input('scope'),
                    'context' => $request->input('context'),
                ]
            ]);

            $statusCode = $result->getStatusCode();
            $data = json_decode($result->getBody(), true);

            if ($statusCode == 200) {

                $request->session()->put('store_hash', $data['context']);
                $request->session()->put('access_token', $data['access_token']);
                $request->session()->put('user_id', $data['user']['id']);
                $request->session()->put('user_email', $data['user']['email']);

                // If the merchant installed the app via an external link, redirect back to the
                // BC installation success page for this app
                if ($request->has('external_install')) {
                    return redirect('https://login.bigcommerce.com/app/' . env('BC_APP_CLIENT_ID') . '/install/succeeded');
                }
            }

            //Search of active merchant(s) was here
            $this->loginAndSelectMerchant( $data['access_token'], $data['user']['email'] );

        } catch (RequestException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $errorMessage = "An error occurred.";

            if ($e->hasResponse()) {
                if ($statusCode != 500) {
                    $errorMessage = Psr7\str($e->getResponse());
                }
            }

            // If the merchant installed the app via an external link, redirect back to the
            // BC installation failure page for this app
            if ($request->has('external_install')) {
                return redirect('https://login.bigcommerce.com/app/' . env('BC_APP_CLIENT_ID') . '/install/failed');
            } else {
                dd( $errorMessage );
                return redirect()->action('BigcommerceIntegrationController@error')->with('error_message', $errorMessage);
            }
        }
    }

    private function loginAndSelectMerchant( $access_token, $email )
    {
        // Save return url path
        $redirectQueue = [];
        $redirectQueue[] = str_replace( 'install', 'finish', preg_replace("/^http:/i", "https:", url()->full() . '&access_token=' . $access_token ) );
        session(['redirect_queue' => $redirectQueue]);

        try {
            $user = $this->users->findWhereFirst([
                'email' => $email,
            ]);
        } catch (\Exception $e) {
            // No user with such email
        }

        $url = session('redirect_queue')[0];
        $url = preg_replace( "/^http:/i", "https:", $url );
        $url .= strpos(session('redirect_queue')[0], '?') === false ? '?' : '&';

        // New user - autologin
        if ( !isset($user) || (isset($user) && !$user) ) {

            $url = $url . "new-user=1";

            // Redirecting to finish installation link
            echo "<script type='text/javascript'>
                window.location.href = '" . $url . "';
            </script>";
        }

        // Already Lootly user - showing list of merchants
        else {

            Auth::login( $user );

            $this->merchants->clearEntity();
            $merchants = $this->merchants->findWhere([
                'owner_id' => $user->id,
            ]);

            echo( "<style> #installing { display: none; } </style>" );
            echo( "<div style='padding: 3.2rem 3.2rem;'><h3>Select Store</h3>" );
            foreach ($merchants as $merchant) {

                // Finish installation link for every merchant
                $finish_url = $url . "merchant_id=" . $merchant->id;
                echo('<p><a href="' . $finish_url . '">' . $merchant->name . '</a></p>');
            }
            echo( "</div>" );
        }
    }

    //Call from route, finish installation
    public function connectBigcommerce( Request $request ) {

        $merchantForIntegration = null;
        if (! $request->get('new-user') && Auth::user()) {
            $user = Auth::user();
            $merchant_id = intval($request->get('merchant_id'));
            if (! $merchant_id) {
                return abort(422, 'Lootly merchant ID is not valid.');
            }
            try {
                $merchant = $this->merchants->find($merchant_id);
            } catch (\Exception $e) {

            }
            if (! isset($merchant) || ! $merchant ) {
                return abort(422, 'Lootly merchant ID is not valid.');
            }
            if ($user->roleOn($merchant) != 'owner' && ! $user->ownsTeam($merchant)) {
                return abort(403, 'You are not allowed to perform this action.');
            }
            $merchantForIntegration = $merchant;
        }

        session()->forget([
            'redirect_queue'
        ]);

        // Create BigCommerce integration record
        return $this->createIntegration( $request->get('context'), $request->get('access_token'), $this->getStoreHash($request), $merchantForIntegration );
    }

    public function getStoreHash(Request $request) {
        if (env('APP_ENV') === 'local') {
            return env('BC_LOCAL_STORE_HASH');
        } else {
            return $request->session()->get('store_hash');
        }
    }

    private function createIntegration( $shop_domain_code, $access_token, $hash, $merchant = null )
    {
        $bigcommerceIntegration = $this->bcService->getBigcommerceIntegration();

        // Get BC shop info
        try {
            $shop = $this->bcService->makeApiCall( 'v2/store', null, $hash, $access_token );
        } catch (\Exception $e) {
            return abort(500, 'An error has occurred while attempting to get shop data.');
        }

        $email = $shop->admin_email;

        // New merchant
        if ( !$merchant ) {

            try {
                $user = $this->users->findWhereFirst([
                    'email' => $email,
                ]);
            } catch (\Exception $e) {
                // No user with such email
            }

            // Create new user
            if (! isset($user) || (isset($user) && ! $user)) {

                // Prepare new user data
                $new_user_data = [];
                $new_user_data['email'] = $email;
                $new_user_data['first_name'] = isset( $shop->first_name ) ? $shop->first_name : '';
                $new_user_data['last_name'] = isset( $shop->last_name ) ? $shop->last_name : '';
                $new_user_data['password'] = str_random(8);
                $new_user_data['plan'] = 0;

                // Store user
                $user = app('user_service')->createNewUser( $new_user_data );
                if (! $user) {
                    return abort(500, 'An error has occurred while attempting to create new user record.');
                }

                Mail::to($user->email)->queue(new MerchantWelcome($user, true, $new_user_data['password']));
            }


            // Create New Merchant
            $merchant = app('user_service')->configureMerchant($user, [
                'company' => $shop->name,
                'website' => $shop->domain,
            ]);
            if (! $merchant) {
                return abort(500, 'An error has occurred while attempting to create new merchant record.');
            }

            // Login with created user credentials
            Auth::login($user);
        }

        $this->merchants->clearEntity();
        try {
            $this->merchants->update($merchant->id, [
                'website' => $shop->domain,
            ]);
        } catch (\Exception $exception) {

        }

        // Create/Update BigCommerce integration
        try {
            // Default BigCommerce integration settings
            $defaultSettings = [
                'order_settings' => [
                    'reward_status'           => 'completed',
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
                app('merchant_service')->deactivateEcommerceIntegrations($merchant, [$bigcommerceIntegration->id]);
            } catch (\Exception $e) {
                Log::error('BigCommerce integration installing: Error on attempting to deactivate e-commerce integration (merchant #'.$merchant->id.').');
                Log::error($e->getMessage());
            }

            $this->merchants->updateIntegrations($merchant, $bigcommerceIntegration->id, [
                'status'      => 1,
                'external_id' => $shop_domain_code,
                'token'       => $access_token,
                'settings'    => json_encode($defaultSettings),
            ]);

            // Update merchant details
            $merchant_details = $this->merchantDetails->updateOrCreate([
                'merchant_id' => $merchant->id,
            ], [
                'ecommerce_shop_domain' => $shop_domain_code,
            ]);
        } catch (\Exception $exception) {
            Log::error('Create/Update Bigcommerce integration error: '.$exception->getMessage());

            return abort(500, 'An unexpected error has occurred while attempting to save merchant integration.');
        }

        $this->bcService->makeScriptApiRequests( 'v3/content/scripts', $access_token, $hash, $merchant_details );

        // Run jobs after successful BigCommerce installation
        $this->installWebhooks( $merchant, $access_token, $hash );

        // Set current merchant
        Auth::user()->switchToTeam($merchant);

        $login_url = env('APP_URL') . '/login';
        $installed_text = <<<THANKS
<div style='padding: 3.2rem 3.2rem;'>
    <h1>Thanks for Installing Lootly!</h1>    
    <p>Please review the instructions below to start utilizing BigCommerce for your project</p>
    <h2>How to Login to Lootly</h2>
    <p>You should have just received an email from us containing your temporary password to log into Lootly. Your username is the same email you use to login to BigCommerce, and the password is inside of the email just sent to you.</p>
    <p>
        <a target="_BLANK" href="$login_url">Click Here</a> to log into your new Lootly Account.
    </p>
</div>
THANKS;
        echo $installed_text;
    }
/*
    private function getDomain( $url ) {

        $ch = curl_init( $url );
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $headers = curl_exec($ch);
        curl_close($ch);
        // Check if there's a Location: header (redirect)
        if (preg_match('/^Location: (.+)$/im', $headers, $matches))
            return trim($matches[1]);
        // If not, there was no redirect so return the original URL
        return $url;
    }*/

    protected function installWebhooks( $merchant, $access_token, $hash )
    {
        $webhooks = config('integrations.bigcommerce.webhooks');
        if (count($webhooks) > 0) {
            dispatch( new BigcommerceWebhookInstaller( $merchant, $webhooks, $access_token, $hash, $this->bcService ) );
        }
    }

    public function load( Request $request )
    {
        $signedPayload = $request->input('signed_payload');
        if (!empty($signedPayload)) {
            $verifiedSignedRequestData = $this->verifySignedRequest($signedPayload, $request);
            if ($verifiedSignedRequestData !== null) {

                $merchant = $this->bcService->getMerchant( $verifiedSignedRequestData['context'] );

                $bigcommerceIntegration = $this->bcService->getBigcommerceIntegration();
                $checkIntegration = $this->merchantModel->findIntegrationWithToken( $merchant, $bigcommerceIntegration );
                $token = trim($checkIntegration->pivot->token);

                if( ! Auth::user() ) {
                    $request->session()->put( 'store_hash', $verifiedSignedRequestData['context'] );
                    $request->session()->put( 'access_token', $token );
                    Auth::loginUsingId( $merchant->owner_id, true );
                }
                return redirect()->route('dashboard');
            } else {
                return redirect()->action('BigcommerceIntegrationController@error')->with('error_message', 'The signed request from BigCommerce could not be validated.');
            }
        } else {
            return redirect()->action('BigcommerceIntegrationController@error')->with('error_message', 'The signed request from BigCommerce was empty.');
        }
    }

    private function verifySignedRequest($signedRequest, $appRequest)
    {
        list($encodedData, $encodedSignature) = explode('.', $signedRequest, 2);

        // decode the data
        $signature = base64_decode($encodedSignature);
        $jsonStr = base64_decode($encodedData);
        $data = json_decode($jsonStr, true);

        // confirm the signature
        $expectedSignature = hash_hmac('sha256', $jsonStr, env('BC_APP_SECRET'), $raw = false );
        if (!hash_equals($expectedSignature, $signature)) {
            error_log('Bad signed request from BigCommerce!');
            return null;
        }
        return $data;
    }

    public function uninstall( Request $request ) {

        $signedPayload = $request->input('signed_payload');
        if (!empty($signedPayload)) {
            $verifiedSignedRequestData = $this->verifySignedRequest($signedPayload, $request);
            if ($verifiedSignedRequestData !== null) {
                $external_id = $verifiedSignedRequestData['context'];

                $bigcommerceIntegration = $this->bcService->getBigcommerceIntegration();
                $merchants = $this->bcService->getMerchants( $external_id );

                foreach ($merchants as $merchant) {
                    Log::info('Deactivating BigCommerce integration for merchant #'.$merchant->id);
                    $this->merchants->updateIntegrations($merchant, $bigcommerceIntegration->id, [
                        'status' => 0,
                    ]);
                }

            } else {
                return redirect()->action('BigcommerceIntegrationController@error')->with('error_message', 'The signed request from BigCommerce could not be validated.');
            }
        } else {
            return redirect()->action('BigcommerceIntegrationController@error')->with('error_message', 'The signed request from BigCommerce was empty.');
        }

        return redirect('/');
    }

    public function error(Request $request)
    {
        $errorMessage = "Internal Application Error";

        if ($request->session()->has('error_message')) {
            $errorMessage = $request->session()->get('error_message');
        }

        echo '<h4>An issue has occurred:</h4> <p>' . $errorMessage . '</p> <a href="'.$this->baseURL.'">Go back to home</a>';
    }

    public function test() {

        /*'customer_id' => 2122162700348,*/
        /*,
            "point_value" => 50*/
        $data = [
            "id"              => "007",
            "email"           => "jamesbond@lootly.io",
            "first_name"      => "James",
            "last_name"       => "Bond",
            "birthday"        => "1970-01-01",
            "state"           => "enabled",
            "default_address" => [
                "zip"     => "12345",
                "country" => "United States",
            ],
            'key'          => 'STbEy1CBJpkVQ9Oats0LVl4aB0IYyxaOEhOJgSfEYbJSuPrBaeJtLAikzBqN',
        ];

        ksort($data);

        $api_secret = 'y7kw1y0sc416VOiWxDoTVt6vjrLPObgPDOQylZzNTBM7jjecgit3DR76U2cO';
        $hmac = base64_encode( hash_hmac('sha256', json_encode( $data ), $api_secret, true ) );

        echo $hmac;

       /* $data = [
            'customer_id' => 2122162700348,
            "point_value" => 50,
            'key'         => 'C4EtmJRiZB7TyMeljHwtDqKlyJvVF93Ce2jyHOgXkkGblxE4DbLDHCXgPR7D',
            'hmac'        => $hmac
        ];*/

        //echo( json_encode( $data ) );


    }
}
