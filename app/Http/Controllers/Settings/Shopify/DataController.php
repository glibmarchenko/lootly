<?php

namespace App\Http\Controllers\Settings\Shopify;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\MerchantDetailRepository;


class DataController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MerchantDetailRepository $merchantDetailRepository)
    {
        $this->merchantDetailRepository = $merchantDetailRepository;
        $this->middleware('auth');
    }

    /**
     * Update the user's contact information settings.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $merchantObj = $request;
        $this->merchantDetailRepository->create($merchantObj);


    }
}
