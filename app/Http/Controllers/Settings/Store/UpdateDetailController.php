<?php

namespace App\Http\Controllers\Settings\Store;

use App\Repositories\Contracts\MerchantRepository;
use App\Transformers\MerchantDetailsTransformer;
use App\Transformers\MerchantTransformer;
use Illuminate\Http\Request;
use App\Contracts\Interactions\Settings\Store\UpdateStoreDetail;
use App\Http\Controllers\Controller;
use App\Merchant;
use App\Models\MerchantDetail;

//use Laravel\Spark\Contracts\Interactions\Settings\Store\UpdateDetail;

class UpdateDetailController extends Controller
{
    protected $merchants;

    /**
     * Create a new controller instance.
     *
     * @param \App\Repositories\Contracts\MerchantRepository $merchants
     */
    public function __construct(
        MerchantRepository $merchants
    ) {
        $this->middleware('auth');
        $this->merchants = $merchants;
    }

    /**
     * Update the user's contact information settings.
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function update(Request $request)
    {
        $this->interaction($request, UpdateStoreDetail::class, [
            $request->user(),
            $request->all(),
        ]);
    }

    public function currentMerchant(Request $request, $store_id)
    {
        $merchant = $this->merchants->find($store_id);

        abort_unless($request->user()->onTeam($merchant), 404);

        $request->user()->switchToTeam($merchant);

        return response('', 204);
        //return $this->merchantRepository->putSessionId($store_id);
    }

    public function showMerchant(Request $request)
    {

        //$current_store = $this->merchantRepository->getCurrent();
        $current_store = $request->user()->current_team;

        $merchants = Merchant::query()->where('owner_id', '=', $request->user()->id)->get();

        if (! $current_store) {
            $current_store = $request->user();
        }

        return response()->json([
            'merchants'     => $merchants,
            'current_store' => $current_store,
        ]);
    }

    public function currentMerchantDetails(Request $request)
    {

        //$current_store = $this->merchantRepository->getCurrent();
        $current_store = $request->user()->current_team;

        if (! $current_store) {
            return response()->json([], 404);
        }

        $details = $current_store->detail()->first();
        if (empty($details)) {
            $details = new MerchantDetail;
        }

        return fractal()->item($details)->parseIncludes([])->transformWith(new MerchantDetailsTransformer)->toArray();
    }

    public function getCurrent(Request $request)
    {
        //$currenctMerchant = $this->merchantRepository->current();
        $currenctMerchant = $request->user()->current_team;

        if (! $currenctMerchant) {
            return response()->json([], 404);
        }

        return fractal()
            ->item($currenctMerchant)
            ->parseIncludes(['merchant_currency'])
            ->transformWith(new MerchantTransformer)
            ->toArray();
    }
}


