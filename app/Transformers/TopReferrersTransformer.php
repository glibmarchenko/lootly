<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Customer;
use Carbon\Carbon;

class TopReferrersTransformer extends TransformerAbstract
{

    protected $start;
    protected $end;

    public function __construct(\DatePeriod $period)
    {
        $this->start = Carbon::instance($period->getStartDate());
        $this->end = Carbon::instance($period->getStartDate()->add($period->getDateInterval()));
    }

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Customer $customer)
    {
        $referralOrders = $customer->orders()
            ->whereNotNull('referring_customer_id')
            ->where('created_at', '>', $this->start)
            ->where('created_at', '<', $this->end);

        if (isset($referralOrders)) {
            $ordersCount = $referralOrders->count();
            $ordersValue = $referralOrders->sum('total_price');

            return [
                'id' => $customer->id,
                'email' => $customer->email,
                'shares' => !empty($customer->shares) ? $customer->shares->count() : 0,
                'clicks' => !empty($customer->clicks) ? $customer->clicks->count() : 0,
                'orders' => $ordersCount,
                'avg_order' => $ordersCount === 0 ? 0 : round($ordersValue / $ordersCount, 2),
                'revenue' => $ordersValue,
            ];
        }
        return [
            'id' => $customer->id,
            'email' => $customer->email,
            'shares' => !empty($customer->shares) ? $customer->shares->count() : 0,
            'clicks' => !empty($customer->clicks) ? $customer->clicks->count() : 0,
            'orders' => 0,
            'avg_order' => 0,
            'revenue' => 0,
        ];
    }
}
