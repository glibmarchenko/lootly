<?php

namespace App\Http\Controllers\Auth;

use Laravel\Spark\Spark;
use Illuminate\Http\Request;

class SparkLoginController extends \Laravel\Spark\Http\Controllers\Auth\LoginController
{
    /**
     * Handle a successful authentication attempt.
     *
     * @param  Request  $request
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return Response
     */
    public function authenticated(Request $request, $user)
    {
        if (Spark::usesTwoFactorAuth() && $user->uses_two_factor_auth) {
            return $this->redirectForTwoFactorAuth($request, $user);
        }

        if(session('redirect_queue')){
            $redirectQueue = session('redirect_queue');
            if(count($redirectQueue)){
                $redirectTo = array_pop($redirectQueue);
                session(['redirect_queue' => $redirectQueue]);
                return redirect($redirectTo);
            }
        }

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $tmpSession = [];
        if (session('redirect_queue')){
            $tmpSession['redirect_queue'] = session('redirect_queue');
        }
        if (session('redirect_queue')){
            $tmpSession['redirect_queue'] = session('redirect_queue');
        }
        if (session('shopify_integration')){
            $tmpSession['shopify_integration'] = session('shopify_integration');
        }

        $this->guard()->logout();

        session()->flush();

        foreach ($tmpSession as $key => $value) {
            session([$key => $value]);
        }

        /*
        if(request()->get('redirect')){
            $this->redirectAfterLogout = request()->get('redirect');
        }*/

        return redirect(
            property_exists($this, 'redirectAfterLogout')
                    ? $this->redirectAfterLogout : '/login'
        );
    }
}
