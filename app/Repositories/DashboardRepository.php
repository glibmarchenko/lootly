<?php

namespace App\Repositories;

use App\Merchant;

class DashboardRepository
{
    public function __construct()
    {
        $this->merchantRepo = new MerchantRepository;
        $this->reportsRepo = new ReportsRepository;
        $this->chartRepo = new Chart\ChartRepository;
        $this->smallChartRepo = new Chart\SmallRangeChartRepository;
        $this->largeChartRepo = new Chart\LargeRangeChartRepository;
    }

    /**
     * Collect statistical data for Dashboard page
     * @param \DatePeriod $currentPeriod period with DateInterval in days,
     * @param Merchant $merchant for which data will be collected
     * @param string $section
     * @return array
     */
    public function getDashboardStatsForPeriod(\DatePeriod $currentPeriod, Merchant $merchant = null, $section = '')
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }
        $pastPeriod = $this->calcPastPeriod($currentPeriod);

        $numOfSections = env('NumOfChartSections', 6);

        $results['customersStatistic'] = $this->getNewMembersData($currentPeriod, $pastPeriod, $numOfSections, $merchant, $section);
        $results['pointsStatistic'] = $this->getPointsData($currentPeriod, $pastPeriod, $numOfSections, $merchant, $section);
        $results['activities'] = $this->getActivitiesData($currentPeriod, $pastPeriod, $numOfSections, $merchant, $section);
        $results['valueStatistic'] = $this->getValueGeneratedData($currentPeriod, $pastPeriod, $numOfSections, $merchant, [], $section);
        return $results;
    }

    /**
     * Collect data for new customers during given period
     * @param \DatePeriod $currentPeriod current period during which data will collected (also for the chart)
     * @param \DatePeriod $pastPeriod
     * @param int $numOfSections Num of sections in chart, need for splitting chart data
     * @param Merchant $merchant for which data will be collected
     * @param string $section
     * @return array
     */
    public function getNewMembersData(\DatePeriod $currentPeriod, \DatePeriod $pastPeriod, $numOfSections = 1, Merchant $merchant = null, $section = 'customersStatistic')
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $data = [
            'currentNum' => $this->reportsRepo->getCustomersStatistic($currentPeriod, $merchant)->count(),
            'pastNum' => $this->reportsRepo->getCustomersStatistic($pastPeriod, $merchant)->count(),
            'chartData' => ['labels' => [], 'data' => [], 'tooltip' => []]
        ];

        if ($section === 'customersStatistic') {
            $data['chartData'] = $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) {
                return $this->reportsRepo->getCustomersStatistic($period, $merchant, ['get' => null]);
            }, $merchant, 'count');
        }

        return $data;
    }

    /**
     * Collect data for earned and spent points during given period
     * @param \DatePeriod $currentPeriod current period during which data will collected (also for the chart)
     * @param \DatePeriod $pastPeriod
     * @param Integer $numOfSections Num of sections in chart, need for splitting chart data
     * @param Merchant $merchant for which data will be collected
     * @param string $section
     * @return array
     */
    public function getPointsData(\DatePeriod $currentPeriod, \DatePeriod $pastPeriod, $numOfSections, Merchant $merchant = null, $section = 'pointsStatistic')
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $earnedPointsData = [
            'currentNum' => $this->reportsRepo->getEarnedStatistic($currentPeriod, $merchant)->sum('point_value'),
            'pastNum' => $this->reportsRepo->getEarnedStatistic($pastPeriod, $merchant)->sum('point_value'),
            'chartData' => ['labels' => [], 'data' => [], 'tooltip' => []]
        ];

        if ($section === 'pointsStatistic') {
            $earnedPointsData['chartData'] = $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) {
                return $this->reportsRepo->getEarnedStatistic($period, $merchant, ['get' => null]);
            }, $merchant, 'sum', ['point_value']);
        }

        return $earnedPointsData;
    }

    /**
     * Collect data for customer`s activities (number of earned and spent action) during given period
     * @param \DatePeriod $currentPeriod current period during which data will collected (also for the chart)
     * @param \DatePeriod $pastPeriod
     * @param Integer $numOfSections Num of sections in chart, need for splitting chart data
     * @param Merchant $merchant for which data will be collected
     * @param string $section
     * @return array
     */
    public function getActivitiesData(\DatePeriod $currentPeriod, \DatePeriod $pastPeriod, $numOfSections, Merchant $merchant = null, $section = 'activities')
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $excludeImport = [
            'where' => ['type', '<>', 'Imported']
        ];

        $data = [
            'currentNum' => $this->reportsRepo->getActivitiesStatistic($currentPeriod, $merchant, $excludeImport)->count(),
            'pastNum' => $this->reportsRepo->getActivitiesStatistic($pastPeriod, $merchant, $excludeImport)->count(),
            'chartData' => ['labels' => [], 'data' => [], 'tooltip' => []]
        ];

        if ($section === 'activities') {
            $data['chartData'] = $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) use ($excludeImport) {
                return $this->reportsRepo->getActivitiesStatistic($period, $merchant,
                    array_merge($excludeImport, ['get' => null]));
            }, $merchant, 'count');
        }

        return $data;
    }

    /**
     * Collect data for generated value during given period
     * @param \DatePeriod $currentPeriod current period during which data will collected (also for the chart)
     * @param \DatePeriod $pastPeriod
     * @param int $numOfSections Num of sections in chart, need for splitting chart data
     * @param Merchant $merchant for which data will be collected
     * @param array $filters
     * @param string $section
     * @return array
     */
    public function getValueGeneratedData(\DatePeriod $currentPeriod, \DatePeriod $pastPeriod, $numOfSections, Merchant $merchant = null, array $filters = [], $section = 'valueStatistic')
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $data = [
            'currentNum' => (float)$this->reportsRepo->getValueGeneratedStatistic($currentPeriod, $merchant, $filters)->sum('total_price'),
            'pastNum' => (float)$this->reportsRepo->getValueGeneratedStatistic($pastPeriod, $merchant)->sum('total_price'),
            'chartData' => ['labels' => [], 'data' => [], 'tooltip' => []]
        ];

        if ($section === 'valueStatistic') {
            $data['chartData'] = $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) use ($filters) {
                return $this->reportsRepo->getValueGeneratedStatistic($period, $merchant, array_merge(['get' => null], $filters));
            }, $merchant, 'sum', ['total_price']);
        }

        return $data;
    }

    /**
     * Collect data for generated value during given period
     * @param \DatePeriod $currentPeriod current period during which data will collected (also for the chart)
     * @param \DatePeriod $pastPeriod
     * @param int $numOfSections Num of sections in chart, need for splitting chart data
     * @param Merchant $merchant for which data will be collected
     * @param array $filters
     * @return array
     */
    public function getReferralsData(\DatePeriod $currentPeriod, \DatePeriod $pastPeriod, $numOfSections, Merchant $merchant = null, array $filters = [])
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        return [
            'currentNum' => (float)$this->reportsRepo->getReferralGeneratedStatistic($currentPeriod, $merchant, $filters)->sum('total_price'),
            'pastNum' => (float)$this->reportsRepo->getValueGeneratedStatistic($pastPeriod, $merchant)->sum('total_price'),
            'chartData' => $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) use ($filters) {
                return $this->reportsRepo->getReferralGeneratedStatistic($period, $merchant, array_merge(['get' => null], $filters));
            }, $merchant, 'sum', ['total_price'])
        ];
    }

    /**
     * Collect data for latest points, referrals and rewards
     * @param Merchant $merchant for which data will be collected
     * @return array
     */
    public function getLatestSectionData(Merchant $merchant = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }
        $numRows = env('numOfLatestRows', 5);
        return [
            'latestPoints' => $this->reportsRepo->getPoints($merchant, $numRows),
            'latestRefferals' => $this->reportsRepo->getReferralOrders($merchant, $numRows),
            'latestRewards' => $this->reportsRepo->getRewards($merchant, $numRows)
        ];
    }

    /**
     * Calculate past period relative to given
     * @param \DatePeriod $period
     * @return \DatePeriod
     */
    protected function calcPastPeriod(\DatePeriod $period)
    {
        // count past period
        $pastStart = clone $period->getStartDate();
        $newDateInterval = $this->getNewDateInterval($period);
        $pastStart->sub($newDateInterval);
        return new \DatePeriod($pastStart, $newDateInterval, 1);
    }

    /**
     * Calculate date interval for the $period
     * @param \DatePeriod $period
     * @return \DateInterval
     */
    public function getNewDateInterval(\DatePeriod $period)
    {
        $days = $this->getDaysInPeriod($period);
        if (!$days) {
            return new \DateInterval("PT" . $period->getDateInterval()->h . "H");
        } else {
            return new \DateInterval("P" . $days . "D");
        }
    }

    /**
     * Get number of days in the $period
     * @param \DatePeriod
     * @return int
     */
    protected function getDaysInPeriod(\DatePeriod $period)
    {
        $interval = $period->getDateInterval();
        return $interval->days ? $interval->days : $interval->d;
    }

    /**
     * Parse and format data for given $totalPeriod for a chart
     * @param \DatePeriod $totalPeriod
     * @param integer number of sections in a chart
     * @param \Closure $dataFunc for getting data during $period for the merchant
     * @param Merchant $merchant for which data will be collected
     * @param string additional function which will be called on every piece of chart data
     * @param array parameters of additional function
     *
     * @return array
     */
    public function getChartData(\DatePeriod $totalPeriod, int $numOfSections, \Closure $dataFunc, Merchant $merchant, string $additionalFunc = null, array $addFuncParams = [null])
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $daysInPeriod = $this->getDaysInPeriod($totalPeriod);
        if ($daysInPeriod <= 3) {
            return $this->smallChartRepo->getChartData($totalPeriod, $numOfSections, $dataFunc, $merchant, $additionalFunc, $addFuncParams);
        } elseif ($daysInPeriod > 31) {
            return $this->largeChartRepo->getChartData($totalPeriod, $numOfSections, $dataFunc, $merchant, $additionalFunc, $addFuncParams);
        } else {
            return $this->chartRepo->getChartData($totalPeriod, $numOfSections, $dataFunc, $merchant, $additionalFunc, $addFuncParams);
        }
    }
}
