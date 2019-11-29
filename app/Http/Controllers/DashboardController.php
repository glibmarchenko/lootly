<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ReportsRepository;
use App\Repositories\DashboardRepository;
use App\Repositories\MerchantRepository;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->reportsRepo = new ReportsRepository;
        $this->merchantRepo = new MerchantRepository;
        $this->dashboardRepo = new DashboardRepository;
    }

    public function index()
    {
        $merchant = $this->merchantRepo->getCurrent();
        //$interval = new \DateInterval("P" . (env('DefaultReportingPeriod', 30)) . "D");
        //$dateTime = new \DateTime;
        //$start = $dateTime->modify('today')//get start of this day
        //->add(new \DateInterval("P1D"))//set to end of the day
        //->sub($interval);
        //$period = new \DatePeriod($start, $interval, 1);
        //$data = $this->dashboardRepo->getDashboardStatsForPeriod($period, $merchant);

        $emptyData = [
            'currentNum' => 0,
            'pastNum' => 0,
            'chartData' => ['labels' => [], 'data' => [], 'tooltip' => []]
        ];

        $data = [
            'customersStatistic' => $emptyData,
            'pointsStatistic' => $emptyData,
            'activities' => $emptyData,
            'valueStatistic' => $emptyData
        ];

        $currency = $merchant->merchant_currency;
        if (!empty($currency)) {
            $data['currency_sign'] = $currency->currency_sign;
        } else {
            $data['currency_sign'] = '$';
        }
        $data = array_merge($data, $this->dashboardRepo->getLatestSectionData($merchant));
        return view('dashboard', $data);
    }

    public function getStatistic(Request $request)
    {
        $section = $request->get('section');

        if (!in_array($section, ['customersStatistic', 'pointsStatistic', 'activities', 'valueStatistic'])) {
            abort(404);
        }

        $merchant = $this->merchantRepo->getCurrent();
        $start = new \DateTime($request->get('start'));
        $end = new \DateTime($request->get('end'));
        $period = new \DatePeriod($start, $start->diff($end), 1);
        return $this->dashboardRepo->getDashboardStatsForPeriod($period, $merchant, $section);
    }
}
