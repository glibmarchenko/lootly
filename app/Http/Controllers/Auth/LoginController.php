<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if (session('redirect_queue')) {
            $redirectQueue = session('redirect_queue');
            if (count($redirectQueue)) {
                $redirectTo = array_pop($redirectQueue);
                session(['redirect_queue' => $redirectQueue]);

                return redirect($redirectTo);
            }
        }

        return redirect()->intended($this->redirectPath());
    }

    protected function loggedOut(Request $request)
    {
        if (request()->get('redirect')) {
            $this->redirectTo = request()->get('redirect');
        }

        return redirect(property_exists($this, 'redirectTo') ? $this->redirectTo : '/');
    }
}
