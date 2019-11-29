<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ReportsOverviewRepository;
use App\Repositories\MerchantRepository;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->reportsOverviewRepo = new ReportsOverviewRepository;
        $this->merchantRepo = new MerchantRepository;
    }

    public function overview()
    {
        $merchant = $this->merchantRepo->getCurrent();
        $interval = new \DateInterval("P" . env('DefaultReportingPeriod', 30) . "D");
        $dateTime = new \DateTime;
        $start = $dateTime->modify('today')//get start of this day
        ->add(new \DateInterval("P1D"))//set to end of the day
        ->sub($interval);
        $period = new \DatePeriod($start, $interval, 1);
        $data = $this->reportsOverviewRepo->getOverviewStatsForPeriod($period, $merchant);
        // dd($data);
        return view('reports.overview', $data);
    }

    public function getOverviewData(Request $request)
    {
        $merchant = $this->merchantRepo->getCurrent();
        $start = new \DateTime($request->get('start'));
        $end = new \DateTime($request->get('end'));
        $period = new \DatePeriod($start, $start->diff($end), 1);
        $data = $this->reportsOverviewRepo->getOverviewStatsForPeriod($period, $merchant);
        // dd($data);
        $response = $this->reportsOverviewRepo->transormResponse($data);
        $response['rewardsIssued'] = $this->reportsOverviewRepo->transormResponse($data['rewardsIssued']);
        $response['pointsEarned']['popularEarningData'] = $data['pointsEarned']['popularEarningData'];
        $response['rewardsIssued']['popularSpendingData'] = $data['rewardsIssued']['popularSpendingData'];
        $response['investment']['orderDiscounts'] = $data['investment']['orderDiscounts'];
        $response['investment']['freeShippingDiscounts'] = $data['investment']['freeShippingDiscounts'];
        $response['investment']['freeProductDiscounts'] = $data['investment']['freeProductDiscounts'];
        $response['investment']['lootlyPlanCost'] = $data['investment']['lootlyPlanCost'];
        return $response;
    }

    public function referrals()
    {
        $merchant = $this->merchantRepo->getCurrent();
        $interval = new \DateInterval("P" . env('DefaultReportingPeriod', 30) . "D");
        $dateTime = new \DateTime;
        $start = $dateTime->modify('today')//get start of this day
        ->add(new \DateInterval("P1D"))//set to end of the day
        ->sub($interval);
        $period = new \DatePeriod($start, $interval, 1);
        $data = $this->reportsOverviewRepo->getReferralsStatsForPeriod($period, $merchant);
        unset($data['investment']['orderDiscounts'], $data['investment']['freeShippingDiscounts'],
            $data['investment']['freeProductDiscounts'], $data['investment']['lootlyPlanCost']);
        // dd($data);
        return view('reports.referrals', $data);
    }

    public function getReferralsData(Request $request)
    {
        $merchant = $this->merchantRepo->getCurrent();
        $start = new \DateTime($request->get('start'));
        $end = new \DateTime($request->get('end'));
        $period = new \DatePeriod($start, $start->diff($end), 1);

        $data = $this->reportsOverviewRepo->getReferralsStatsForPeriod($period, $merchant);
        unset($data['investment']['orderDiscounts'], $data['investment']['freeShippingDiscounts'],
            $data['investment']['freeProductDiscounts'], $data['investment']['lootlyPlanCost']);
        // dd($data);
        $response = $data;
        $response['valueGenerated'] = [
            'main' => $data['valueGenerated'],
            'investment' => $data['investment'],
            'orderCount' => $data['orderCount'],
            'averageOrderValue' => $data['averageOrderValue'],
        ];
        $response['referrers'] = $data['topReferrers'];
        unset($response['investment'], $response['orderCount'], $response['averageOrderValue']);
        // dd($data, $response);
        return \response()->json($response);
    }
}
