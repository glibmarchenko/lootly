<?php

namespace App\Repositories\Chart;

use App\Merchant;
use App\Repositories\MerchantRepository;
use App\Repositories\ReportsRepository;

/**
 * Use for parse chart data for more than 31 days period
 */
class LargeRangeChartRepository extends ChartRepository
{
    public function __construct()
    {
        $this->merchantRepo = new MerchantRepository;
        $this->reportsRepo = new ReportsRepository;
    }

    /**
     * Parse and format data for given $totalPeriod (if period is 32 days or more) for a chart
     *
     * @param \DatePeriod $totalPeriod total period
     * @param integer $numOfSections number of sections in a chart
     * @param \Closure $dataFunc function for getting data during $period for the $merchant
     * @param Merchant $merchant for which data will be collected
     * @param string $additionalFunc additional function which will be called on every piece of chart data
     * @param array $addFuncParams parameters of additional function
     * @return array
     */
    public function getChartData(\DatePeriod $totalPeriod, int $numOfSections, \Closure $dataFunc, Merchant $merchant, string $additionalFunc = null, array $addFuncParams = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }
        $numOfSections = env('NumOfChartSections', 6);
        $chartDates = $this->getChartDates($totalPeriod);
        $chartData = ['labels' => [], 'data' => [], 'tooltip' => []];
        $daysInPeriod = $this->getDaysInPeriod($totalPeriod);
        $totalData = $dataFunc($totalPeriod, $merchant);
        $rawData = null;
        if ($daysInPeriod > 180) {
            for ($key = 0; $key < count($chartDates) - 1; $key++) {
                $rawData = $totalData
                    ->where('created_at', '>', $chartDates[$key])
                    ->where('created_at', '<', $chartDates[$key + 1]);
                if (!empty($additionalFunc)) {
                    $chartData['data'][] = \call_user_func_array([$rawData, $additionalFunc], $addFuncParams);
                } else {
                    $chartData['data'][] = $rawData;
                }
                $chartData['labels'][] = $this->getLabel($daysInPeriod, $chartDates, $key, $numOfSections);
                $chartData['tooltip'][] = $this->getTooltip($chartDates[$key], $daysInPeriod);
            }
        } else {
            //set first point
            $chartData['data'][] = 0;
            $chartData['labels'][] = $this->getLabel($daysInPeriod, $chartDates, 0, $numOfSections);
            $chartData['tooltip'][] = $chartDates[0]->format('M j, Y');
            // set points in middle
            for ($key = 1; $key < count($chartDates) - 1; $key++) {
                $dateClone = clone $chartDates[$key];
                $rawData = $totalData
                    ->where('created_at', '>', $chartDates[$key - 1])
                    ->where('created_at', '<', $chartDates[$key]);
                if (!empty($additionalFunc)) {
                    $chartData['data'][] = \call_user_func_array([$rawData, $additionalFunc], $addFuncParams);
                } else {
                    $chartData['data'][] = $rawData;
                }
                $chartData['labels'][] = $this->getLabel($daysInPeriod, $chartDates, $key, $numOfSections);
                $chartData['tooltip'][] = $this->getTooltip($chartDates[$key], $daysInPeriod);
            }
            //set last point
            $key = count($chartDates) - 2;
            $dateClone = clone $chartDates[count($chartDates) - 1];
            $dateClone->add(new \DateInterval('P1D'));
            $rawData = $totalData
                ->where('created_at', '>', $chartDates[$key])
                ->where('created_at', '<', $dateClone);

            if (!empty($additionalFunc)) {
                $chartData['data'][] = \call_user_func_array([$rawData, $additionalFunc], $addFuncParams);
            } else {
                $chartData['data'][] = $rawData;
            }
            $chartData['labels'][] = $this->getLabel($daysInPeriod, $chartDates, count($chartDates) - 1, $numOfSections);
            $chartData['tooltip'][] = $this->getTooltip($chartDates[count($chartDates) - 1], $daysInPeriod);
        }
        // dd($chartData);
        return $chartData;
    }

    /**
     * Generate label depend on period
     * @param integer $daysInPeriod days in total period
     * @param array $chartDates chart period
     * @param integer $index label's index
     * @param integer $numOfSections number of sections in a chart
     * @return string formatted date for label
     */
    protected function getLabel(int $daysInPeriod, array $chartDates, int $index, int $numOfSections)
    {
        $dateClone = clone $chartDates[$index];
        if ($daysInPeriod < 63) {
            if ($index == 0) {
                return '';
            }
            $dateClone->sub(new \DateInterval('P1D'));
            if ($this->shouldSetLabel($index, count($chartDates), $numOfSections)) {
                return $dateClone->format('M d');
            }
        }
        if ($daysInPeriod < 180) {
            if ($index != 0) {
                $dateClone->sub(new \DateInterval('P1D'));
            }
            if ($index == count($chartDates) - 1) {
                return '';
            }
            if ($dateClone->format('d') < '7' || $index == 0) {
                return $dateClone->format('M Y');
            }
            return '';
        } else {
            if ($this->shouldSetLabel($index, count($chartDates), $numOfSections))
                return $dateClone->format('M Y');

            return '';
        }
    }

    /**
     * Calculate period for one label in chart
     * @param \DatePeriod $totalPeriod total chart's period
     * @return array
     */
    protected function getChartDates(\DatePeriod $totalPeriod)
    {
        $daysInPeriod = $this->getDaysInPeriod($totalPeriod);
        $chartDates = [];
        $dateClone = $totalPeriod->getStartDate();
        $currentEnd = $totalPeriod->getStartDate()->add($totalPeriod->getDateInterval());
        switch (true) {
            case ($daysInPeriod <= 180):
                $newInterval = new \DateInterval("P7D");
                while ($dateClone < $currentEnd) {
                    $chartDates[] = clone $dateClone;
                    $dateClone->add($newInterval);
                }
                $chartDates[] = $currentEnd;
                break;

            case ($daysInPeriod > 180):
                while ($dateClone < $currentEnd) {
                    $chartDates[] = clone $dateClone;
                    $dateClone->modify('first day of next month');
                }
                $chartDates[] = $currentEnd;
                break;
        }

        return $chartDates;
    }

    /**
     * Check if need to set label based on index an number of sections in chart
     * @param int $index index of label
     * @param int $totalNum total number of indexes
     * @param int $numOfSections number of sections in chart
     * @return bool
     */
    protected function shouldSetLabel($index, $totalNum, $numOfSections)
    {
        if (!$numOfSections) {
            return false;
        }
        if ($totalNum <= $numOfSections) {
            return true;
        }

        return $index % ($totalNum / $numOfSections) == 0 || $index == $totalNum;
    }

    /**
     * generate tooltip based on days in period
     * @param \DateTime date for which tooltip will be created
     * @param int days in total period
     *
     * @return string
     */
    protected function getTooltip(\DateTime $date, int $daysInPeriod)
    {
        if ($daysInPeriod < 180) {
            $dateClone = clone $date;
            $dateClone->sub(new \DateInterval('P1D'));
            return $dateClone->format('M j, Y');
        }

        return $date->format('M Y');
    }
}
