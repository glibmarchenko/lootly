<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class WelcomeController extends Controller
{
    /**
     * Show the application splash screen.
     *
     * @return Response
     */
    public function show()
    {
        return view('welcome');
    }

    public function requestDemo() {
        $email = Input::get('email', '');
        return view('website.company.request-demo')->withEmail($email);
    }
}
