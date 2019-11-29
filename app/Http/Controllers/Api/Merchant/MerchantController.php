<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Merchant;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Transformers\MerchantTransformer;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Merchant\StoreRequest;
use App\Repositories\MerchantRepository;
use App\Repositories\UserMerchantRepository;
use Illuminate\Support\Facades\Auth;

class MerchantController extends Controller
{
    protected $merchantRepo;

    protected $userMerchantRepo;

    protected $merchants;

    public function __construct(
        \App\Repositories\Contracts\MerchantRepository $merchants,
        MerchantRepository $merchantRepository,
        UserMerchantRepository $userMerchantRepository
    ) {
        $this->merchants = $merchants;
        $this->merchantRepo = $merchantRepository;
        $this->userMerchantRepo = $userMerchantRepository;

        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $merchants = $this->merchantRepo->getMerchantsByOwner(\Auth::id());

        return fractal($merchants)->transformWith(new MerchantTransformer)->toArray();
    }

    public function show($id)
    {

        $merchant = $this->merchantRepo->findOwnedMerchantById(\Auth::id(), $id);

        return fractal($merchant)->transformWith(new MerchantTransformer)->toArray();
    }

    public function store(StoreRequest $request)
    {
        $data = $request->all();

        $merchant = $this->merchants->createMerchant($request->user(), $data);

        /*
        $merchant = $this->merchantRepo->createMerchant(\Auth::id(), $data);

        if ($merchant) {
            // Create UserMerchant owner record
            $this->userMerchantRepo->create(\Auth::user(), $merchant->id);
        }
        */

        return fractal($merchant)->transformWith(new MerchantTransformer)->toArray();
    }

    public function update(Request $request, $id)
    {

        $merchant = $this->merchantRepo->findOwnedMerchantById(\Auth::id(), $id);

        return fractal($merchant)->transformWith(new MerchantTransformer)->toArray();
    }

    public function delete($id)
    {

        $merchant = $this->merchantRepo->findOwnedMerchantById(\Auth::id(), $id);

        return fractal($merchant)->transformWith(new MerchantTransformer)->toArray();
    }

    public function getCommonSettings(Request $request, Merchant $merchant)
    {
        $merchantCommonSettings = $this->merchants->withCriteria([
            new EagerLoad([
                'points_settings',
                'merchant_currency',
                'email_notification_settings'
            ])
        ])->find($merchant->id);

        return fractal($merchantCommonSettings)->parseIncludes([
            'points_settings',
            'merchant_currency',
            'email_notification_settings',
        ])->transformWith(new MerchantTransformer())->toArray();
    }

    public function getSettings(Request $request, Merchant $merchant)
    {
        $merchantSettings = $this->merchants->withCriteria([
            new EagerLoad(['detail']),
        ])->find($merchant->id);

        return fractal($merchantSettings)
            ->parseIncludes(['details'])
            ->transformWith(new MerchantTransformer)
            ->toArray();
    }

    public function generateSecuredHashString(Request $request, Merchant $merchant)
    {
        $string = str_random(60);
        $hash = base64_encode(hash_hmac('sha256', $string, config('app.key'), true));

        return response()->json([
            'string' => $string,
            'hash'   => $hash,
        ], 200);
    }
}