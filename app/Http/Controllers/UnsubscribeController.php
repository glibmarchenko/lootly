<?php

namespace App\Http\Controllers;

use App\Repositories\CustomerRepository;
use App\Repositories\EmailBlacklistRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UnsubscribeController extends Controller
{

    private $customerRepo;
    private $emailBlacklistRepo;

    public function __construct()
    {
        $this->customerRepo = new CustomerRepository;
        $this->emailBlacklistRepo = new EmailBlacklistRepository();
    }

    public function store(Request $request)
    {
        $customer = $this->customerRepo->getByEmail($request->get('email'));

        if (!$customer) {
            abort(404);
        }

        $merchant = $customer->merchant;

        if (decrypt($request->get('token')) !== $customer->email . $merchant->id) {
            abort(404);
        }

        if ($this->emailBlacklistRepo->check($merchant, $customer->email)) {
            abort(404);
        }

        $this->emailBlacklistRepo->create([
            'email' => $customer->email
        ], $merchant);

        return view('tmp.unsubscribed', compact('merchant'));
    }
}
