<?php

namespace App\Repositories\Chart;

use App\Merchant;
use App\Repositories\MerchantRepository;
use App\Repositories\ReportsRepository;

/**
 * Use for parse chart data for 3 days or less period
 */
class SmallRangeChartRepository extends ChartRepository
{
    public function __construct()
    {
        $this->merchantRepo = new MerchantRepository;
        $this->reportsRepo = new ReportsRepository;
    }

    /**
     * Parse and format data for given $totalPeriod (if period is 3 days or less) for a chart
     * @param \DatePeriod $totalPeriod total period
     * @param int $numOfSections number of sections in a chart
     * @param \Closure $dataFunc function for getting data during $period for the $merchant
     * @param Merchant $merchant for which data will be collected
     * @param string $additionalFunc which will be called on every piece of chart data
     * @param array $addFuncParams parameters of additional function
     * @return array
     */
    public function getChartData(\DatePeriod $totalPeriod, int $numOfSections, \Closure $dataFunc, Merchant $merchant, string $additionalFunc = null, array $addFuncParams = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $chartPeriod = $this->getPeriodForChart($totalPeriod);
        $chartData = ['labels' => [], 'data' => [], 'tooltip' => []];
        $totalData = $dataFunc($totalPeriod, $merchant);

        foreach ($chartPeriod as $key => $date) {
            $rawData = null;
            if ($key == 0) {
                $chartData['data'][] = 0;
            } else {
                $dateClone = clone $date;
                $rawData = $totalData
                    ->where('created_at', '>', $dateClone->sub($chartPeriod->getDateInterval()))
                    ->where('created_at', '<', $date);
                if (!empty($additionalFunc)) {
                    $chartData['data'][] = \call_user_func_array([$rawData, $additionalFunc], $addFuncParams);
                } else {
                    $chartData['data'][] = $rawData;
                }
            }

            $chartData['labels'][] = $this->genLabel($date, $totalPeriod, $chartPeriod, $key, $numOfSections);
            $dateClone = clone $date;
            if (($key % 6) == 0 && $key != 0) {
                $dateClone->sub(new \DateInterval("PT1S"));
            }
            $chartData['tooltip'][] = $dateClone->format('h:ia M jS');
        }
        return $chartData;
    }

    /**
     * Generate label depend on period
     * @param \DateTime $date for which label will generated
     * @param \DatePeriod $totalPeriod
     * @param \DatePeriod $chartPeriod
     * @param int $index label's index
     * @param int $numOfSections number of sections in a chart
     * @return string formatted date for label
     */
    protected function genLabel(\DateTime $date, \DatePeriod $totalPeriod, \DatePeriod $chartPeriod, int $index, int $numOfSections)
    {
        $daysInPeriod = $this->getDaysInPeriod($totalPeriod);
        if ($index == 0) {
            return '';
        }

        if ($index == $chartPeriod->recurrences - 1) {
            return '';
        }
        if ($daysInPeriod > 2) {
            if ($date->format('H') == '00') {
                return $date->format('M d');
            }
            return '';
        } else {
            if ($this->shouldSetLabel($index, $chartPeriod->recurrences - 1, $numOfSections))
                return $date->format('h:i A');

            return '';
        }
    }

    /**
     * Calculate period for one label in chart
     * @param \DatePeriod $totalPeriod chart's period
     * @return \DatePeriod period of one label
     */
    protected function getPeriodForChart(\DatePeriod $totalPeriod)
    {
        $currentStart = clone $totalPeriod->getStartDate();
        $newInterval = new \DateInterval("PT4H");
        $currentStartClone = clone $currentStart;
        $hours = abs(($currentStartClone->add($totalPeriod->getDateInterval())->getTimestamp() - ($currentStart)->getTimestamp()) / 3600 / 4);
        return new \DatePeriod($currentStart, $newInterval, $hours);
    }
}
