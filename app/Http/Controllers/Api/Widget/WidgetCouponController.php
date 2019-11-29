<?php

namespace App\Http\Controllers\Api\Widget;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\CouponRepository;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Repositories\Eloquent\Criteria\WithTrashedMerchantRewards;
use App\Transformers\CouponTransformer;
use Illuminate\Http\Request;

class WidgetCouponController extends Controller
{
    protected $coupons;

    public function __construct(CouponRepository $coupons)
    {
        $this->coupons = $coupons;
    }

    public function getByCode(Request $request, $code)
    {
        if (! $request->get('merchant_id') || ! trim($request->get('merchant_id'))) {
            return response()->json([], 200);
        }

        $coupon = $this->coupons->withCriteria([
            new WithTrashedMerchantRewards(),
            new LatestFirst(),
        ])->findWhereFirst([
            'merchant_id' => $request->get('merchant_id'),
            'coupon_code' => $code,
        ]);

        return fractal($coupon)->transformWith(new CouponTransformer)->toArray();
    }
}