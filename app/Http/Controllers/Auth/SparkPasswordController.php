<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

class SparkPasswordController extends \Laravel\Spark\Http\Controllers\Auth\PasswordController
{
    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        return view('website.auth.forgot-password');
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Http\Response
     */
    public function showResetForm(Request $request, $token = null)
    {
        if (is_null($token)) {
            return $this->showLinkRequestForm();
        }

        return view('website.auth.reset-password')
            ->with(['token' => $token, 'email' => $request->email]);
    }
}
