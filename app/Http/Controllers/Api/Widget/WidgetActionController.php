<?php

namespace App\Http\Controllers\Api\Widget;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\MerchantActionRepository;
use App\Repositories\Contracts\PointRepository;
use App\Repositories\Eloquent\Criteria\ByCustomer;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\EarnedPoints;
use App\Repositories\Eloquent\Criteria\HasActionWhere;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Repositories\Eloquent\Criteria\WithEarnedPoints;
use App\Repositories\Eloquent\Criteria\WithEarnedPointsInYear;
use App\Repositories\Eloquent\Criteria\WithTierHistory;
use App\Repositories\Eloquent\Criteria\WithUsedCoupons;
use App\Transformers\MerchantActionTransformer;
use App\Transformers\PointTransformer;
use Illuminate\Http\Request;

class WidgetActionController extends Controller
{
    protected $merchantActions;

    protected $customers;

    protected $points;

    public function __construct(
        MerchantActionRepository $merchantActions,
        CustomerRepository $customers,
        PointRepository $points
    ) {
        $this->merchantActions = $merchantActions;
        $this->customers = $customers;
        $this->points = $points;
    }

    public function getActions(Request $request)
    {
        if (! $request->get('merchant_id') || ! trim($request->get('merchant_id'))) {
            return response()->json([], 200);
        }

        $active_merchant_actions = $this->merchantActions->withCriteria([
            new EagerLoad(['action']),
        ])->findWhere([
            'merchant_id' => $request->get('merchant_id'),
            'active_flag' => 1,
        ]);

        return fractal($active_merchant_actions)->transformWith(new MerchantActionTransformer)->toArray();
    }

    public function getAction(Request $request, $slug)
    {
        if (! $request->get('merchant_id') || ! trim($request->get('merchant_id')) || ! trim($slug)) {
            return response()->json([], 404);
        }

        try {
            $active_merchant_action = $this->merchantActions->withCriteria([
                new ByMerchant($request->get('merchant_id')),
                new EagerLoad(['action']),
                new HasActionWhere([
                    'url' => trim($slug),
                ]),
            ])->findWhereFirst([
                'active_flag' => 1,
            ]);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }

        return fractal($active_merchant_action)->transformWith(new MerchantActionTransformer)->toArray();
    }

    public function completeAction(Request $request, $actionId)
    {
        if (! $request->get('customer_id') || ! trim($request->get('customer_id')) || ! trim($actionId)) {
            return response()->json([], 404);
        }

        try {
            $action = $this->merchantActions->withCriteria([
                new EagerLoad(['action']),
                new ByMerchant($request->get('merchant_id')),
            ])->find($actionId);

            if ($action) {
                try {
                    $points = app('action_service')->creditPointsForAction($action, $request->get('customer_id'));

                    return fractal($points)->transformWith(new PointTransformer)->toArray();
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => 'Action can not be completed',
                        'error'   => $e->getMessage(),
                    ], 405);
                }
            } else {
                return response()->json(['message' => 'Action not found'], 404);
            }
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Action not found'], 404);
        }
    }
}