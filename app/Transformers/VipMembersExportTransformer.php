<?php

namespace App\Transformers;

use App\Models\Customer;
use League\Fractal\TransformerAbstract;
use App\Repositories\CustomerRepository;
use App\Merchant;

class VipMembersExportTransformer extends TransformerAbstract
{

    public function __construct(CustomerRepository $customerRepo, Merchant $merchantObj){
        $this->customerRepository = $customerRepo;
        $this->merchant = $merchantObj;
    }
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Customer $customer)
    {
        $orders = $customer->orders;
        $currency = $this->merchant->merchant_currency;
        $currency_sign = '$';
        if(!empty($currency)) {
            $currency_sign = $currency->currency_sign;
        }
        return [
            'Customer ID' => $customer->id,
            'Customer Name' => $customer->name,
            'Purchases' => count($orders),
            'Total Spend' => $currency_sign . $orders->sum('total_price'),
            'Points Earned' => $this->customerRepository->getEarnedPoints($this->merchant, $customer->id)->sum('point_value'),
            'VIP Tier' => $customer->tier->name,
            'Last Ordered' => $customer->getLastOrdered()->created_at . "",
        ];
    }
}
