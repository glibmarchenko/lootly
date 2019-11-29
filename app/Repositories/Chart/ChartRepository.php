<?php

namespace App\Repositories\Chart;

use App\Merchant;
use App\Repositories\MerchantRepository;
use App\Repositories\ReportsRepository;

/**
 * Use for parse chart data for 3 - 31 days period
 */
class ChartRepository
{
    public function __construct()
    {
        $this->merchantRepo = new MerchantRepository;
        $this->reportsRepo = new ReportsRepository;
    }

    /**
     * Calculate period for one label in chart
     * @param \DatePeriod $totalPeriod total chart's period
     * @return \DatePeriod period of one label
     */
    protected function getPeriodForChart(\DatePeriod $totalPeriod)
    {
        $days = $this->getDaysInPeriod($totalPeriod);
        $currentStart = clone $totalPeriod->getStartDate();
        $newInterval = new \DateInterval("P1D");
        return new \DatePeriod($currentStart, $newInterval, $days - 1);
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
     * The formatting date depends on the length of the period
     * @param \DateTime $date
     * @param \DatePeriod $period chart's period
     * @return string
     */
    public function getTooltipDateFormat(\DateTime $date, \DatePeriod $period, $index)
    {
        $days = $this->getDaysInPeriod($period);
        $dateClone = clone $date;
        return $dateClone->format('M j, Y');
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
     * Check if need to set label based on index an number of sections in chart
     * @param int $index of label
     * @param int $totalNum total number of indexes
     * @param int $numOfSections number of sections in chart
     * @return bool
     */
    protected function shouldSetLabel($index, $totalNum, $numOfSections)
    {
        if (!$numOfSections) {
            return false;
        }

        if ($totalNum < $numOfSections) {
            return true;
        }

        return ($index + 1) % ($totalNum / $numOfSections) == 0 || $index == $totalNum;
    }

    /**
     * Get number of days in the $period
     * @param \DatePeriod $period
     * @return int
     */
    protected function getDaysInPeriod(\DatePeriod $period)
    {
        $interval = $period->getDateInterval();
        return $interval->days ? $interval->days : $interval->d;
    }

    /**
     * Generate label depend on period
     * @param \DateTime $date for which label will generated
     * @param \DatePeriod $totalPeriod total period
     * @param \DatePeriod $chartPeriod chart period
     * @param int $index label's index
     * @param integer $numOfSections number of sections in a chart
     * @return string formatted date for label
     */
    protected function genLabel(\DateTime $date, \DatePeriod $totalPeriod, \DatePeriod $chartPeriod, int $index, int $numOfSections)
    {
        $daysInPeriod = $this->getDaysInPeriod($totalPeriod);
        if ($daysInPeriod < 31) {
            $numOfSections -= 1;
        } else {
            if ($index == $chartPeriod->recurrences - 1) { // disable last label for 31 days chart
                return '';
            }
        }

        if ($this->shouldSetLabel($index, $chartPeriod->recurrences - 1, $numOfSections)) {
            return $date->format('M d');
        } else {
            return '';
        }
    }

    /**
     * Parse and format data for given $totalPeriod for a chart
     * @param \DatePeriod $totalPeriod
     * @param int $numOfSections number of sections in a chart
     * @param \Closure $dataFunc function for getting data during $period for the $merchant
     * @param Merchant $merchant for which data will be collected
     * @param string|null additional function which will be called on every piece of chart data
     * @param array|null parameters of additional function
     * @return array
     */
    public function getChartData(\DatePeriod $totalPeriod, int $numOfSections, \Closure $dataFunc, Merchant $merchant, string $additionalFunc = null, array $addFuncParams = [null])
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $chartPeriod = $this->getPeriodForChart($totalPeriod);
        $chartData = ['labels' => [], 'data' => [], 'tooltip' => []];
        $totalData = $dataFunc($totalPeriod, $merchant);

        foreach ($chartPeriod as $key => $date) {
            $end = clone $date;
            $rawData = $totalData
                ->where('created_at', '>', $date)
                ->where('created_at', '<', $end->add($chartPeriod->getDateInterval()));
            if (!empty($additionalFunc)) {
                try {
                    $chartData['data'][] = \call_user_func_array([$rawData, $additionalFunc], $addFuncParams);
                } catch (\Trowable $e) {
                    \Log($e);
                }
            } else {
                $chartData['data'][] = $rawData;
            }
            $chartData['labels'][] = $this->genLabel($date, $totalPeriod, $chartPeriod, $key, $numOfSections);
            $chartData['tooltip'][] = $date->format('M j, Y');
        }
        return $chartData;
    }
}
