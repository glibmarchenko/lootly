<?php

namespace App\Http\Controllers\Api\Settings\Referrals;

use App\Http\Controllers\Controller;
use App\Merchant;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Repositories\Eloquent\Criteria\Limit;
use App\Transformers\ReferralActivityTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReferralsController extends Controller
{
    protected $orders;

    protected $merchants;

    /**
     * ReferralsController constructor.
     *
     * @param \App\Repositories\Contracts\OrderRepository    $orders
     * @param \App\Repositories\Contracts\MerchantRepository $merchants
     */
    public function __construct(OrderRepository $orders, MerchantRepository $merchants)
    {
        $this->orders = $orders;
        $this->merchants = $merchants;
    }

    public function getActivity(Request $request, Merchant $merchant)
    {
        $limit = 1000;
        if ($request->get('latest')){
            $limit = min((int)$request->get('latest'), 10);
        }

        $start = $request->get('start');
        $end = $request->get('end');

        $criteria = [
            new LatestFirst(),
            new EagerLoad([
                'customer',
                'referral',
            ]),
            new Limit($limit)
        ];

        // Get referral orders
        $orders = $this->merchants->referredOrders($merchant->id)->withCriteria($criteria);
        if (!empty($start) && !empty($end)) {
            $startDate = new \DateTime($start);
            $endDate = new \DateTime($end);
            $orders = $orders
                ->findWhere([
                    ['orders.created_at', '>', Carbon::instance($startDate)],
                    ['orders.created_at', '<', Carbon::instance($endDate)]
                ]);
        } else {
            $orders = $orders->all();
        }

        return fractal($orders)->transformWith(new ReferralActivityTransformer())->toArray();
    }
}
