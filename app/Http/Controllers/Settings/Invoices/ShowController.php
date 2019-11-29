<?php

namespace App\Http\Controllers\Settings\Invoices;

use App\Http\Controllers\Controller;
use App\Repositories\InvoicesRepository;


class ShowController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(InvoicesRepository $invoicesRepository)
    {
        $this->invoicesRepository = $invoicesRepository;
        $this->middleware('auth');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function show()
    {
        //
    }
}
