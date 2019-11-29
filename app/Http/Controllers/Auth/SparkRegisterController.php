<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Laravel\Spark\Interactions\Settings\Teams\AddTeamMember;
use App\Repositories\InvitationRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\UserMerchantRepository;
use App\Repositories\UserRepository;
use Laravel\Spark\Spark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Spark\Events\Auth\UserRegistered;
use Laravel\Spark\Contracts\Interactions\Auth\Register;
use Laravel\Spark\Contracts\Http\Requests\Auth\RegisterRequest;

class SparkRegisterController extends \Laravel\Spark\Http\Controllers\Auth\RegisterController
{
    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(
        UserMerchantRepository $userMerchantRepository,
        MerchantRepository $merchantRepository,
        InvitationRepository $invitationRepository,
        UserRepository $userRepository
    ) {
        $this->middleware('guest');
        $this->userMerchantRepository = $userMerchantRepository;
        $this->userRepository = $userRepository;
        $this->merchantRepository = $merchantRepository;
        $this->invitationRepository = $invitationRepository;
        $this->redirectTo = Spark::afterLoginRedirect();
    }

    /**
     * Show the application registration form.
     *
     * @param  Request $request
     *
     * @return Response
     */
    /*public function showRegistrationForm(Request $request, $token = null)
    {
        if (Spark::promotion() && !$request->filled('coupon')) {
            // If the application is running a site-wide promotion, we will redirect the user
            // to a register URL that contains the promotional coupon ID, which will force
            // all new registrations to use this coupon when creating the subscriptions.
            return redirect($request->fullUrlWithQuery([
                'coupon' => Spark::promotion()
            ]));
        }

        return view('spark::auth.register', compact('token'));
    }*/

    /**
     * Handle a registration request for the application.
     *
     * @param  RegisterRequest $request
     *
     * @return Response
     */
    public function register(RegisterRequest $request)
    {
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

        Auth::login($user = Spark::interact(Register::class, [$request]));

        event(new UserRegistered($user));

        $redirectTo = $this->redirectPath();
        if (session('redirect_queue')) {
            $redirectQueue = session('redirect_queue');
            if (count($redirectQueue)) {
                $redirectTo = array_pop($redirectQueue);
                session(['redirect_queue' => $redirectQueue]);
            }
        }

        return response()->json([
            'redirect' => $redirectTo,
        ]);
        /*$token = $request->input('token');
        if ($token) {
            $invitationObj = $this->invitationRepository->getByToken($token);
            $merchantObj = $this->merchantRepository->find($invitationObj->merchant_id);
            $userObj = $this->userRepository->getByEmail($request->input('email'));
            $add_team_member = new AddTeamMember();
            $add_team_member->handle($merchantObj, $userObj, User::EMPLOYEE_ROLE);
            $this->userMerchantRepository->create($userObj, $merchantObj);
        }*/
    }
}
