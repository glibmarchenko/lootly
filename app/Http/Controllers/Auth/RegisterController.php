<?php

namespace App\Http\Controllers\Auth;

use App\Mail\MerchantWelcome;
use App\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\RegistersUsers;
use Laravel\Spark\Contracts\Http\Requests\Auth\RegisterRequest;
use Laravel\Spark\Contracts\Interactions\Auth\Register;
use Laravel\Spark\Contracts\Interactions\Settings\Teams\CreateTeam;
use Laravel\Spark\Events\Auth\UserRegistered;
use App\Services\Stripe as StripeService;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\PlanRepository;
use App\Repositories\Contracts\PaymentRepository;
use App\Repositories\Contracts\SubscriptionRepository;
use App\Repositories\Contracts\UserRepository;
use App\Models\Plan;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected $planRepository;

    protected $userRepository;

    protected $paymentRepository;

    protected $merchantRepository;

    protected $subscriptionRepository;

    protected $stripeService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        PlanRepository $planRepository,
        UserRepository $userRepository,
        PaymentRepository $paymentRepository,
        MerchantRepository $merchantRepository,
        SubscriptionRepository $subscriptionRepository,
        StripeService $stripeService
    ) {
        $this->middleware('guest');

        $this->planRepository = $planRepository;
        $this->userRepository = $userRepository;
        $this->paymentRepository = $paymentRepository;
        $this->merchantRepository = $merchantRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->stripeService = $stripeService;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     *
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'max:191',
            'last_name'  => 'max:191',
            'company'    => 'max:191',
            'email'      => 'required|email|max:191|unique:users',
            'password'   => 'required|min:'.\Spark::minimumPasswordLength(),
            'plan'       => 'integer',
        ], [
            'address_line_2' => __('second address line'),
            'team'           => __('store'),
        ]);

        $validator->sometimes('company', 'required|max:191', function ($input) {
            return \Spark::usesTeams() && \Spark::onlyTeamPlans() && ! isset($input['invitation']);
        });

        $validator->sometimes('company_slug', 'required|alpha_dash|unique:merchants,slug', function ($input) {
            return \Spark::usesTeams() && \Spark::onlyTeamPlans() && \Spark::teamsIdentifiedByPath() && ! isset($input['invitation']);
        });

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => __('The given data was invalid.'),
                    'errors' => $validator->errors(),
                ], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (! $request->input(['first_name']) || ! trim($request->input(['first_name']))) {
            $request->merge([
                'first_name' => trim(explode('@', $request->input('email'))[0]),
            ]);
        }
        if (! $request->input(['last_name']) || ! trim($request->input(['last_name']))) {
            $request->merge([
                'last_name' => '',
            ]);
        }

        $plan = (int) $request->input('plan', 0);
        $isYearly = (bool) $request->input('yearly', false);

        $planType = $request->input('plan_type');
        $planPeriod = $isYearly ? 'yearly' : 'monthly';
        $period = $isYearly ? 365 : 30;
        $paymentProvider = 'stripe';

        if ($plan === 0) {
            $request->merge([
                'plan' => $plan,
            ]);
        }

        $data = $request->all();

        // Create new user with merchant
        $user = app('user_service')->createNewUser($data);

        if (! $user) {
            abort(500, 'An error has occurred while attempting to create new user record.');
        }

        // Create New Merchant
        $merchant = app('user_service')->configureMerchant($user, $data);

        /*$merchant = \Spark::interact(CreateTeam::class, [
            $user,
            [
                'name' => ($request->input('company') ?: 'New Store'),
                'slug' => '',
            ],
        ]);*/
        if (! $merchant) {
            abort(500, 'An error has occurred while attempting to create new merchant record.');
        }

        Auth::login($user);

        //event(new UserRegistered($user));
        Mail::to($user->email)->queue(new MerchantWelcome($user));

        $paymentData = [];

        if ($plan !== 0 && $paymentProvider === 'stripe' && $planType) {
            $stripeServicePlans = config('services.stripe.plans');
            $stripePlans = config('plans');

            $stripeTokenId = $request->input('stripe_token_id');

            $paymentId = $this->stripeService->makePaymentId($merchant, $user);
            $planModel = $this->planRepository->findWhereFirst(['type' => $planType]);

            if (! $planModel) {
                abort(404, 'Failed to activate free trial. Selected plan does not exist.');
            }

            $stripePrice = $stripePlans[$planModel->type]['price'][$planPeriod];

            $paymentData = $this->stripeService->getCheckoutRequestData(
                $stripeServicePlans[$planModel->type][$planPeriod],
                $paymentId
            );

            $this->paymentRepository->create([
                'merchant_id' => $merchant->id,
                'user_id' => $user->id,
                'service' => $paymentProvider,
                'payment_id' => $paymentId,
                'status' => 'pending',
                'price' => floatval($stripePrice),
                'plan_id' => $planModel->id,
                'type' => $planPeriod,
            ]);

            $customer = $this->stripeService->createCustomerWithSource($stripeTokenId);
            $subscriptionTrial = $this->stripeService->createSubscriptionTrial(
                $customer,
                $stripeServicePlans[$planModel->type][$planPeriod]
            );

            if (! $customer || ! $subscriptionTrial) {
                abort(500, 'Failed to activate free trial.');
            }

            $customerDefaultSource = $this->stripeService->getCustomerDefaultSource($customer);

            $this->merchantRepository->update($merchant->id, [
                'payment_provider' => $paymentProvider,
                'trial_ends_at' => $subscriptionTrial->trial_end ?? null,
                'stripe_id' => $subscriptionTrial->customer ?? null,
                'card_brand' => $customerDefaultSource['card_brand'] ?? null,
                'card_last_four' => $customerDefaultSource['card_last_four'] ?? null,
                'card_expiration' => $customerDefaultSource['card_expiration'] ?? null,
                'card_country' => $customerDefaultSource['card_country'] ?? null,
            ]);

            $this->userRepository->update($user->id, [
                'trial_ends_at' => $subscriptionTrial->trial_end ?? null,
            ]);

            $this->subscriptionRepository->create([
                'name' => $planModel->name,
                'merchant_id' => $merchant->id,
                'plan_id' => $planModel->id,
                'user_id' => $user->id,
                'stripe_customer_id' => $subscriptionTrial->customer ?? null,
                'stripe_id' => $subscriptionTrial->id ?? null,
                'stripe_plan' => $subscriptionTrial->plan->id ?? null,
                'quantity' => $subscriptionTrial->quantity ?? null,
                'status' => $subscriptionTrial->status ?? null,
                'length' => $period,
                'trial_ends_at' => $subscriptionTrial->trial_end ?? null,
                'ends_at' => Carbon::createFromTimestamp($subscriptionTrial->trial_end)->addDays($period),
            ]);
        }

        $redirectTo = $this->redirectPath();

        if (session('redirect_queue')) {
            $redirectQueue = session('redirect_queue');
            if (count($redirectQueue)) {
                $redirectTo = array_pop($redirectQueue);
                session(['redirect_queue' => $redirectQueue]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'data' => [
                    'user' => $user->only(['id', 'name']),
                    'payment_data' => $paymentData,
                    'redirect_to' => route('dashboard'),
                ],
            ], 200);
        }

        return redirect($redirectTo);
    }

    public function signup($plan = 0, $yearly = false)
    {
        $yearly = $yearly == 'yearly' ? true : false;
        $expiryMonths = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

        $plans = config('plans');

        if ($plan == 1) {
            $data['title'] = 'Lootly is on a mission to empower <br> eCommerce Brands';
            $data['name'] = 'Growth';
            $data['type'] = Plan::TYPE_GROWTH;
            $data['price'] = $yearly
                ? $plans[Plan::TYPE_GROWTH]['price']['yearly']
                : $plans[Plan::TYPE_GROWTH]['price']['monthly'];
            $data['features'] = [
                [
                    'icon'  => 'images/assets/main/auth/Startup Plan/Email Customization.svg',
                    'title' => 'Email Customization',
                ],
                [
                    'icon'  => 'images/assets/main/auth/Startup Plan/Referrals.svg',
                    'title' => 'Referral Program',
                ],
                [
                    'icon'  => 'images/assets/main/auth/Startup Plan/Import.svg',
                    'title' => 'Import your existing customers',
                ],
                [
                    'icon'  => 'images/assets/main/auth/Growth Plan/Remove Lootly Branding.svg',
                    'title' => 'Remove Lootly Branding',
                ],
            ];
        } else if ($plan == 2) {
            $data['title'] = 'Lootly is on a mission to empower <br> eCommerce Brands';
            $data['name'] = 'Ultimate';
            $data['type'] = Plan::TYPE_ULTIMATE;
            $data['price'] = $yearly
                ? $plans[Plan::TYPE_ULTIMATE]['price']['yearly']
                : $plans[Plan::TYPE_ULTIMATE]['price']['monthly'];
            $data['features'] = [
                [
                    'icon'  => 'images/assets/main/auth/Premium Plan/Design Customization.svg',
                    'title' => 'Advanced Customization',
                ],
                [
                    'icon'  => 'images/assets/main/auth/Premium Plan/VIP Program.svg',
                    'title' => 'VIP Program',
                ],
                [
                    'icon'  => 'images/assets/main/auth/Premium Plan/Rewards Page.svg',
                    'title' => 'Rewards Page',
                ],
                [
                    'icon'  => 'images/assets/main/auth/Premium Plan/Reports.svg',
                    'title' => 'Insights & Reports',
                ],
            ];
        } else if ($plan == 3) {
            $data['title'] = 'Lootly is on a mission to empower <br> eCommerce Brands';
            $data['name'] = 'Enterprise';
            $data['type'] = Plan::TYPE_ENTERPRISE;
            $data['price'] = $yearly
                ? $plans[Plan::TYPE_ENTERPRISE]['price']['yearly']
                : $plans[Plan::TYPE_ENTERPRISE]['price']['monthly'];
            $data['features'] = [
                [
                    'icon'  => 'images/assets/main/auth/Ultimate Plan/HTML Editor.svg',
                    'title' => 'HTML Editor',
                ],
                [
                    'icon'  => 'images/assets/main/auth/Ultimate Plan/Custom Domain.svg',
                    'title' => 'Custom Domain',
                ],
                [
                    'icon'  => 'images/assets/main/auth/Ultimate Plan/Points Expiration.svg',
                    'title' => 'Points Expiration',
                ],
                [
                    'icon'  => 'images/assets/main/auth/Ultimate Plan/Account Manager.svg',
                    'title' => 'Dedicated Account Manager',
                ],
            ];
        } else {
            $plan = 0;
            $data['title'] = 'Lootly is on a mission to empower <br> eCommerce Brands';
            $data['name'] = 'Free';
            $data['type'] = Plan::TYPE_FREE;
            $data['price'] = $yearly
                ? $plans[Plan::TYPE_FREE]['price']['yearly']
                : $plans[Plan::TYPE_FREE]['price']['monthly'];
            $data['features'] = [
                [
                    'icon'  => 'images/assets/main/auth/Free Plan/Points.svg',
                    'title' => '9 Ways for Customers to Earn Points',
                ],
                [
                    'icon'  => 'images/assets/main/auth/Free Plan/Paint Brush.svg',
                    'title' => 'Basic Design Customization',
                ],
                [
                    'icon'  => 'images/assets/main/auth/Free Plan/Program Branding.svg',
                    'title' => 'Brand your Rewards Program',
                ],
                [
                    'icon'  => 'images/assets/main/auth/Free Plan/Real-Time Notifications.svg',
                    'title' => 'Email & Chat Support',
                ],
            ];
        }

        return view('website.auth.signup', compact('plan', 'data', 'expiryMonths', 'yearly'));
    }
}
