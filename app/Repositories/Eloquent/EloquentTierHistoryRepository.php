<?php

namespace App\Repositories\Eloquent;

use Carbon\Carbon;
use App\Merchant;
use App\Models\Customer;
use App\Models\TierHistory;
use App\Repositories\Contracts\TierHistoryRepository;
use App\Repositories\RepositoryAbstract;

class EloquentTierHistoryRepository extends RepositoryAbstract implements TierHistoryRepository
{
    public function entity()
    {
        return TierHistory::class;
    }

    /**
     * Get tier history related to merchant
     * @param int $merchant
     * @return array[]
     */
    public function getByMerchant(int $merchantId){
        return Merchant::find($merchantId)->tier_history;
    }

    /**
     * Get tier history for the $merchant during $period
     * @param DatePeriod $period with DateInterval in days,
     * @param Merchant|null $merchant for activity data will be collected
     * @param array includes for TierHistory
     * 
     * @return \Collection[TierHistory] 
     */
    public function getByPeriod(\DatePeriod $period, Merchant $merchant = null, array $includes = []){
        if(empty($merchant)){
            $merchant = $this->merchantRepo->getCurrent();
        }

        $start = Carbon::instance($period->getStartDate());
        $end = Carbon::instance($period->getStartDate()->add($period->getDateInterval()));
        return $merchant->tier_history()
            ->where('tier_history.created_at', '>', $start)
            ->where('tier_history.created_at', '<', $end)
            ->with($includes)
            ->get();
    }
}