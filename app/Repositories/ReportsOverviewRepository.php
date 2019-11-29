<?php

namespace App\Repositories;

use App\Merchant;
use App\Models\MerchantReward;
use App\Transformers\TopReferrersTransformer;
use Carbon\Carbon;

class ReportsOverviewRepository extends DashboardRepository
{

    public static $REWARD_REVENUE = 'reward';
    public static $REFERRAL_REVENUE = 'referral';

    public function __construct()
    {
        $this->merchantRepo = new MerchantRepository;
        $this->reportsRepo = new ReportsRepository;
        $this->chartRepo = new Chart\ChartRepository;
        $this->smallChartRepo = new Chart\SmallRangeChartRepository;
        $this->largeChartRepo = new Chart\LargeRangeChartRepository;
    }

    /**
     * Collect statistical data for Reports Overview page
     * @param \DatePeriod $currentPeriod period with DateInterval in days,
     * @param Merchant $merchant for which data will be collected
     * @return array
     */
    public function getOverviewStatsForPeriod(\DatePeriod $currentPeriod, Merchant $merchant = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $pastPeriod = $this->calcPastPeriod($currentPeriod);
        $daysInInterval = $currentPeriod->getDateInterval()->d ? $currentPeriod->getDateInterval()->d : $currentPeriod->getDateInterval()->days;
        $numOfSections = env('NumOfChartSections', 6);

        $pointsData = $this->getPointsData($currentPeriod, $pastPeriod, $numOfSections, $merchant);
        $pointsData['popularEarningData'] = $this->reportsRepo->getPopularEarningActions($currentPeriod, $merchant);
        if (empty($merchant->points_settings)) {
            $pointsData['pointName'] = 'Point';
            $pointsData['pointNamePlural'] = 'Points';
        } else {
            $pointsData['pointName'] = $merchant->points_settings->name;
            $pointsData['pointNamePlural'] = $merchant->points_settings->plural_name;
        }

        $currencySettings = $merchant->merchant_currency;
        if (empty($currencySettings)) {
            $currency = '$';
        } else {
            $currency = $currencySettings->currency_sign;
        }

        $results = [
            'valueStatistic' => $this->getValueGeneratedData($currentPeriod, $pastPeriod, $numOfSections, $merchant),
            'rewardRevenue' => $this->getRevenue($currentPeriod, $pastPeriod, $numOfSections, $merchant, $this::$REWARD_REVENUE),
            'referralRevenue' => $this->getRevenue($currentPeriod, $pastPeriod, $numOfSections, $merchant, $this::$REFERRAL_REVENUE),
            'pointsEarned' => $pointsData,
            'completedEarningActions' => $this->getActivitiesData($currentPeriod, $pastPeriod, $numOfSections, $merchant),
            'rewardOrderCount' => $this->getRewardOrderCount($currentPeriod, $pastPeriod, $numOfSections, $merchant),
            'referralOrderCount' => $this->getRelerralOrderCount($currentPeriod, $pastPeriod, $numOfSections, $merchant),
            'rewardsIssued' => $this->getIssuedRewardsData($currentPeriod, $pastPeriod, $numOfSections, $merchant),
            'investment' => $this->getInvestmentData($currentPeriod, $pastPeriod, $numOfSections, $merchant),
            'currencySign' => $currency,
        ];
        return $results;
    }

    /**
     * Collect statistical data for Reports Referrals page
     * @param \DatePeriod $currentPeriod period with DateInterval in days,
     * @param Merchant $merchant for which data will be collected
     * @return array
     */
    public function getReferralsStatsForPeriod(\DatePeriod $currentPeriod, Merchant $merchant = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $pastPeriod = $this->calcPastPeriod($currentPeriod);
        $daysInInterval = $currentPeriod->getDateInterval()->d ? $currentPeriod->getDateInterval()->d : $currentPeriod->getDateInterval()->days;

        $numOfSections = env('NumOfChartSections', 6);

        if (empty($merchant->points_settings)) {
            $pointsData['pointName'] = 'Point';
            $pointsData['pointNamePlural'] = 'Points';
        } else {
            $pointsData['pointName'] = $merchant->points_settings->name;
            $pointsData['pointNamePlural'] = $merchant->points_settings->plural_name;
        }

        $currencySettings = $merchant->merchant_currency;
        if (empty($currencySettings)) {
            $currency = '$';
        } else {
            $currency = $currencySettings->currency_sign;
        }

        $results = array_merge(
            [
                'valueGenerated' => $this->getReferralsData($currentPeriod, $pastPeriod, $numOfSections),
                'investment' => $this->getInvestmentData($currentPeriod, $pastPeriod, $numOfSections, $merchant),
                'orderCount' => $this->getRelerralOrderCount($currentPeriod, $pastPeriod, $numOfSections, $merchant),
                'averageOrderValue' => $this->getAverageOrderValue($currentPeriod, $pastPeriod, $numOfSections, $merchant),
            ],
            $this->getSharesData($currentPeriod, $pastPeriod, $numOfSections, $merchant),
            $this->getClicksData($currentPeriod, $pastPeriod, $numOfSections, $merchant)
        );

        return array_merge(
            $this->transormResponse($results),
            [
                'topReferrers' => $this->getTopReferrers($currentPeriod, $merchant),
                'currencySign' => $currencySettings['currency_sign'],
                'pointsData' => $pointsData
            ]
        );
    }

    /**
     * Parse data for Reward Revenue for given period
     * @param \DatePeriod $period time range for which orders will selected
     * @param Merchant $merchant for which data will be collected
     * @return mixed
     */
    protected function getRewardRevenue(\DatePeriod $period, $merchant)
    {
        return $this->reportsRepo->getValueGeneratedStatistic($period, $merchant)
            ->whereNotNull('coupon_id')
            ->whereNull('orders.referral_slug')
            ->get();
    }

    /**
     * Parse data for Referral Revenue for given period
     * @param \DatePeriod $period time range for which orders will selected
     * @param Merchant merchant for which data will be collected
     * @return mixed
     */
    protected function getRefferalRevenue(\DatePeriod $period, $merchant)
    {
        return $this->reportsRepo->getValueGeneratedStatistic($period, $merchant)
            ->whereNotNull('orders.referral_slug')
            ->whereNull('coupon_id')
            ->get();
    }

    /**
     * Collect data for rewards
     * @param \DatePeriod $currentPeriod period with DateInterval in days,
     * @param \DatePeriod $pastPeriod
     * @param integer $numOfSections Num of sections in chart, need for splitting chart data
     * @param Merchant $merchant for which data will be collected
     * @param string type of revenue for which data will be collected. There two types: "reward" and "referral".
     * @return array
     */
    public function getRevenue(\DatePeriod $currentPeriod, \DatePeriod $pastPeriod, $numOfSections, Merchant $merchant = null, $type = 'reward')
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        return [
            'currentNum' => $type == 'reward' ?
                $this->getRewardRevenue($currentPeriod, $merchant)->sum('total_price') :
                $this->getRefferalRevenue($currentPeriod, $merchant)->sum('total_price'),

            'pastNum' => $type == 'reward' ?
                $this->getRewardRevenue($pastPeriod, $merchant)->sum('total_price') :
                $this->getRefferalRevenue($pastPeriod, $merchant)->sum('total_price'),

            'chartData' => $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) use ($type) {
                return $type == 'reward' ?
                    $this->getRewardRevenue($period, $merchant) :
                    $this->getRefferalRevenue($period, $merchant);
            }, $merchant, 'sum', ['total_price'])
        ];
    }

    /**
     * Count data for rewards orders
     * @param \DatePeriod $currentPeriod period with DateInterval in days,
     * @param \DatePeriod $pastPeriod
     * @param integer $numOfSections Num of sections in chart, need for splitting chart data
     * @param Merchant|null $merchant for which data will be collected
     * @return array
     */
    public function getRewardOrderCount(\DatePeriod $currentPeriod, \DatePeriod $pastPeriod, $numOfSections, Merchant $merchant = null): array
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        return [
            'currentNum' => $this->getRewardRevenue($currentPeriod, $merchant)->count(),
            'pastNum' => $this->getRewardRevenue($pastPeriod, $merchant)->count(),
            'chartData' => $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) {
                return $this->getRewardRevenue($period, $merchant);
            }, $merchant, 'count')
        ];
    }

    /**
     * Collect data for referrals
     * @param \DatePeriod $currentPeriod period with DateInterval in days,
     * @param \DatePeriod $pastPeriod
     * @param integer $numOfSections Num of sections in chart, need for splitting chart data
     * @param Merchant|null $merchant for which data will be collected
     * @return array
     */
    public function getRelerralOrderCount(\DatePeriod $currentPeriod, \DatePeriod $pastPeriod, $numOfSections, Merchant $merchant = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        return [
            'currentNum' => $this->getRefferalRevenue($currentPeriod, $merchant)->count(),
            'pastNum' => $this->getRefferalRevenue($pastPeriod, $merchant)->count(),
            'chartData' => $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) {
                return $this->getRefferalRevenue($period, $merchant);
            }, $merchant, 'count')
        ];
    }

    /**
     * Collect data for merchant investments
     * @param \DatePeriod currentPeriod period with DateInterval in days,
     * @param \DatePeriod pastPeriod
     * @param integer numOfSections Num of sections in chart, need for splitting chart data
     * @param Merchant|null $merchant for which data will be collected
     * @return array
     */
    public function getInvestmentData(\DatePeriod $currentPeriod, \DatePeriod $pastPeriod, $numOfSections, Merchant $merchant = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        return \array_merge([
            'currentNum' => $this->reportsRepo->calculateInvestmentValue($currentPeriod, $merchant),
            'pastNum' => $this->reportsRepo->calculateInvestmentValue($pastPeriod, $merchant),
            'chartData' => $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) {
                return collect($this->reportsRepo->calculateInvestmentValue($period, $merchant, 'array'));
            }, $merchant, 'sum', ['sum'])
        ],
            $this->reportsRepo->calculateInvestmentValue($currentPeriod, $merchant, 'separately'));
    }

    /**
     * Collect data for issued rewards
     * @param \DatePeriod $currentPeriod period with DateInterval in days,
     * @param \DatePeriod $pastPeriod
     * @param integer $numOfSections Num of sections in chart, need for splitting chart data
     * @param Merchant $merchant for which data will be collected
     * @return array
     */
    public function getIssuedRewardsData(\DatePeriod $currentPeriod, \DatePeriod $pastPeriod, $numOfSections, Merchant $merchant = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $rewardsIssued = [
            'currentNum' => $this->reportsRepo->getCouponsStatistic($currentPeriod, $merchant)->count(),
            'pastNum' => $this->reportsRepo->getCouponsStatistic($pastPeriod, $merchant)->count(),
            'chartData' => $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) {
                return $this->reportsRepo->getCouponsStatistic($period, $merchant);
            }, $merchant, 'count')
        ];

        $redemptions = [
            'currentNum' => $this->reportsRepo->getCouponsStatistic($currentPeriod, $merchant)->count(),
            'pastNum' => $this->reportsRepo->getCouponsStatistic($pastPeriod, $merchant)->count(),
            'chartData' => $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) {
                return $this->reportsRepo->getCouponsStatistic($period, $merchant);
            }, $merchant, 'count')
        ];
        return [
            'rewards' => $rewardsIssued,
            'redemptions' => $redemptions,
            'popularSpendingData' => $this->reportsRepo->getPopularSpendingRewards($currentPeriod, $merchant),
        ];
    }

    /**
     * Transform data for view
     * @param array $data to transform
     * @return array
     */
    public function transormResponse(array $data)
    {
        $newData = [];
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                continue;
            }
            $newData[$key] = [
                'value' => array_key_exists('currentNum', $value) ? $value['currentNum'] : null,
                'pastValue' => array_key_exists('pastNum', $value) ? $value['pastNum'] : null,
                'chart' => array_key_exists('chartData', $value) ? $value['chartData'] : null
            ];
        }
        return $newData;
    }

    /**
     * Collect data for average order value
     * @param \DatePeriod $currentPeriod period with DateInterval in days,
     * @param \DatePeriod $pastPeriod
     * @param integer numOfSections Num of sections in chart, need for spliting chart data
     * @param Merchant $merchant for which data will be collected
     * @return array
     */
    public function getAverageOrderValue(\DatePeriod $currentPeriod, \DatePeriod $pastPeriod, $numOfSections, Merchant $merchant = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $orderValueData = $this->getRevenue($currentPeriod, $pastPeriod, $numOfSections, $merchant, $this::$REFERRAL_REVENUE);
        $orderCountData = $this->getRelerralOrderCount($currentPeriod, $pastPeriod, $numOfSections, $merchant);
        $chartData = $orderCountData['chartData'];
        foreach ($orderCountData['chartData']['data'] as $key => $data) {
            if (!empty($data)) {
                $chartData['data'][$key] = round($orderValueData['chartData']['data'][$key] / $data, 2);
            }
        }
        return [
            'currentNum' => $orderCountData['currentNum'] != 0 ? $orderValueData['currentNum'] / $orderCountData['currentNum'] : 0,
            'pastNum' => $orderCountData['pastNum'] != 0 ? round($orderValueData['pastNum'] / $orderCountData['pastNum'], 2) : 0,
            'chartData' => $chartData
        ];
    }

    /**
     * Collect top referrers for the $merchant
     * @param \DatePeriod $period period with DateInterval in days
     * @param Merchant $merchant for which data will be collected
     * @return array
     */
    public function getTopReferrers(\DatePeriod $period, Merchant $merchant = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }
        $customers = $merchant
            ->customers()
            ->with(['orders', 'shares', 'clicks'])
            ->whereHas('orders', function ($query) use ($period) {
                $query->whereNotNull('referring_customer_id')
                    ->where('created_at', '>', Carbon::instance($period->getStartDate()))
                    ->where('created_at', '<', Carbon::instance($period->getStartDate()->add($period->getDateInterval())));
            })
            ->get();
        return fractal()->collection($customers)
            ->parseIncludes(['referral_orders'])
            ->transformWith(new TopReferrersTransformer($period))
            ->toArray()['data'];
    }

    /**
     * Collect data for customer`s shares
     * @param \DatePeriod $currentPeriod period with DateInterval in days,
     * @param \DatePeriod $pastPeriod
     * @param integer $numOfSections Num of sections in chart, need for splitting chart data
     * @param Merchant $merchant for which data will be collected
     * @return array
     */
    public function getSharesData(\DatePeriod $currentPeriod, \DatePeriod $pastPeriod, $numOfSections, Merchant $merchant = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        return [
            'shares' => [
                'currentNum' => $this->reportsRepo->getShares($currentPeriod, $merchant)->count(),
                'pastNum' => $this->reportsRepo->getShares($pastPeriod, $merchant)->count(),
                'chartData' => $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) {
                    return $this->reportsRepo->getShares($period, $merchant);
                }, $merchant, 'count')
            ],
            'sharesFacebook' => [
                'currentNum' => $this->reportsRepo->getShares($currentPeriod, $merchant, 'facebook')->count(),
                'pastNum' => $this->reportsRepo->getShares($pastPeriod, $merchant, 'facebook')->count(),
                'chartData' => $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) {
                    return $this->reportsRepo->getShares($period, $merchant, 'facebook');
                }, $merchant, 'count')
            ],
            'sharesTwitter' => [
                'currentNum' => $this->reportsRepo->getShares($currentPeriod, $merchant, 'twitter')->count(),
                'pastNum' => $this->reportsRepo->getShares($pastPeriod, $merchant, 'twitter')->count(),
                'chartData' => $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) {
                    return $this->reportsRepo->getShares($period, $merchant, 'twitter');
                }, $merchant, 'count')
            ],
            'sharesEmail' => [
                'currentNum' => $this->reportsRepo->getShares($currentPeriod, $merchant, 'email')->count(),
                'pastNum' => $this->reportsRepo->getShares($pastPeriod, $merchant, 'email')->count(),
                'chartData' => $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) {
                    return $this->reportsRepo->getShares($period, $merchant, 'email');
                }, $merchant, 'count')
            ]
        ];
    }

    /**
     * Collect data for customer`s clicks
     * @param \DatePeriod $currentPeriod period with DateInterval in days,
     * @param \DatePeriod $pastPeriod
     * @param integer $numOfSections Num of sections in chart, need for splitting chart data
     * @param Merchant $merchant for which data will be collected
     * @return array
     */
    public function getClicksData(\DatePeriod $currentPeriod, \DatePeriod $pastPeriod, $numOfSections, Merchant $merchant = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        return [
            'clicks' => [
                'currentNum' => $this->reportsRepo->getClicks($currentPeriod, $merchant)->count(),
                'pastNum' => $this->reportsRepo->getClicks($pastPeriod, $merchant)->count(),
                'chartData' => $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) {
                    return $this->reportsRepo->getClicks($period, $merchant);
                }, $merchant, 'count')
            ],
            'clicksFacebook' => [
                'currentNum' => $this->reportsRepo->getClicks($currentPeriod, $merchant, 'facebook')->count(),
                'pastNum' => $this->reportsRepo->getClicks($pastPeriod, $merchant, 'facebook')->count(),
                'chartData' => $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) {
                    return $this->reportsRepo->getClicks($period, $merchant, 'facebook');
                }, $merchant, 'count')
            ],
            'clicksTwitter' => [
                'currentNum' => $this->reportsRepo->getClicks($currentPeriod, $merchant, 'twitter')->count(),
                'pastNum' => $this->reportsRepo->getClicks($pastPeriod, $merchant, 'twitter')->count(),
                'chartData' => $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) {
                    return $this->reportsRepo->getClicks($period, $merchant, 'twitter');
                }, $merchant, 'count')
            ],
            'clicksEmail' => [
                'currentNum' => $this->reportsRepo->getClicks($currentPeriod, $merchant, 'email')->count(),
                'pastNum' => $this->reportsRepo->getClicks($pastPeriod, $merchant, 'email')->count(),
                'chartData' => $this->getChartData($currentPeriod, $numOfSections, function ($period, $merchant) {
                    return $this->reportsRepo->getClicks($period, $merchant, 'email');
                }, $merchant, 'count')
            ]
        ];
    }
}
