<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SwitchMerchantController extends Controller
{

    public function index(Request $request)
    {
        $redirectQueue = session('redirect_queue', []);
        // Check if user logged in
        if(!Auth::id()){
            if(end($redirectQueue) != url()->current()) {
                $redirectQueue[] = url()->current();
            }
            session(['redirect_queue' => $redirectQueue]);
            return redirect('/login');
        }

        return view('tmp.select-merchant');

    }

    public function get()
    {

    }

}
