<?php

namespace App\Http\Controllers\Settings\Vip;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CustomerRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\TierRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Transformers\VipMembersExportTransformer;

class  MemberController extends Controller
{
    /**
     * MemberController constructor.
     * @param CustomerRepository $customerRepository
     * @param MerchantRepository $merchantRepository
     * @param TierRepository $tierRepository
     */
    public function __construct(CustomerRepository $customerRepository, MerchantRepository $merchantRepository, TierRepository $tierRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->merchantRepository = $merchantRepository;
        $this->tierRepository = $tierRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        $merchantObj = $this->merchantRepository->getCurrent();
        $startDate = new \DateTime();
        $startDate = $startDate->modify('today')->modify('-1 month')->add(new \DateInterval("P1D"));
        $endDate = new \DateTime();
        $vipMembers = $this->customerRepository->getVipMembers($merchantObj, $startDate, $endDate);
        $tiers = $this->tierRepository->get($merchantObj);
        $tiersType = [];
        foreach ($tiers as $tier) {
            $tiersType[] = $tier->name;
        }
        $members = $this->generateMembersResponse($vipMembers);
        $currency = $merchantObj->merchant_currency;
        $currencySign = '$';
        if(!empty($currency)) {
            $currencySign = $currency->currency_sign;
        }
        return view('vip.members', compact('members', 'tiersType', 'currencySign'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');
        $merchantObj = $this->merchantRepository->getCurrent();
        $vipMembers = collect();
        if(!empty($start) && !empty($end)){
            $startDate = new \DateTime($start);
            $endDate = new \DateTime($end);
            $vipMembers = $this->customerRepository->getVipMembers($merchantObj, $startDate, $endDate);
        } else {
            $vipMembers = $this->customerRepository->getVipMembers($merchantObj);
        }

        $members = $this->generateMembersResponse($vipMembers);
        return response()->json([
            'members' => $members
        ]);
    }


    /**
     * @return mixed
     */
    public function export(Request $request)
    {
        $merchant = $this->merchantRepository->getCurrent();
        $start = $request->get('start');
        $end = $request->get('end');
        $search = $request->get('search');
        $tier = $request->get('tier');
        $vipMembers = collect();
        if(!empty($start) && !empty($end)){
            $startDate = new \DateTime($start);
            $endDate = new \DateTime($end);
            $vipMembers = $this->customerRepository->getVipMembers($merchant, $startDate, $endDate);
        } else {
            $vipMembers = $this->customerRepository->getVipMembers($merchant);
        }

        if(!empty($tier) && $tier != 'All'){
            $vipMembers = $vipMembers->filter(function($item) use ($tier){
                return $item->tier->name == $tier;
            });
        }

        if(!empty($search)){
            $vipMembers = $vipMembers->filter(function($item) use ($search){
                return stripos($item->name, $search) !== false || stripos($item->tier->name, $search) !== false;
            });
        }

        $members = fractal()->collection($vipMembers)
            ->parseIncludes(['orders', 'points', 'tier'])
            ->transformWith(new VipMembersExportTransformer($this->customerRepository, $merchant))
            ->toArray()['data'];
        $members = collect($members)->sortByDesc('Last Ordered');
        return Excel::create('VipMemeber', function ($excel) use ($members) {
            $excel->setTitle('Customer Export');
            $excel->sheet('Excel sheet', function ($sheet) use ($members) {
                $sheet->setOrientation('landscape');
                $sheet->fromArray($members->toArray());
            });
        })->export('csv');
    }

    /**
     * @param \Illuminate\Support\Collection[App\Models\Customer] $vipMembers
     * @return \Illuminate\Support\Collection
     */
    public function generateMembersResponse($vipMembers)
    {
        $merchantObj = $this->merchantRepository->getCurrent();

        $members = collect();
        foreach ($vipMembers as $vipMember) {
            $orders = $vipMember->orders;
            $memberData = [
                'id' => $vipMember->id,
                'name' => $vipMember->name,
                'purchases' => count($orders),
                'vip_tier' => $vipMember->tier->name,
                'total_spend' => $orders->sum('total_price'),
                'points_earned' => $this->customerRepository->getEarnedPoints($merchantObj, $vipMember->id)->sum('point_value'),
                'last_ordered' => $vipMember->getLastOrdered()->created_at . "",
            ];
            $members->push($memberData);
        }
        return $members;
    }
}