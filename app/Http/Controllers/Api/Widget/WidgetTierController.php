<?php

namespace App\Http\Controllers\Api\Widget;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\TierRepository;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\LowestSpendValueFirst;
use App\Transformers\TierTransformer;
use Illuminate\Http\Request;

class WidgetTierController extends Controller
{
    protected $tiers;

    public function __construct(TierRepository $tiers)
    {
        $this->tiers = $tiers;
    }

    public function getTier(Request $request, $id)
    {
        if (! $request->get('merchant_id') || ! trim($request->get('merchant_id'))) {
            return response()->json([], 200);
        }

        $active_tiers = $this->tiers->withCriteria([
            new EagerLoad(['tierBenefits']),
            new LowestSpendValueFirst()
        ])->findWhere([
            'merchant_id' => $request->get('merchant_id'),
            'status'      => 1,
            'id'          => $id
        ]);

        return fractal($active_tiers)->transformWith(new TierTransformer)->toArray();
    }

    public function getTiers(Request $request)
    {
        if (! $request->get('merchant_id') || ! trim($request->get('merchant_id'))) {
            return response()->json([], 200);
        }

        $active_tiers = $this->tiers->withCriteria([
            new EagerLoad(['tierBenefits']),
            new LowestSpendValueFirst()
        ])->findWhere([
            'merchant_id' => $request->get('merchant_id'),
            'status'      => 1,
        ]);

        return fractal($active_tiers)->transformWith(new TierTransformer)->toArray();
    }
}