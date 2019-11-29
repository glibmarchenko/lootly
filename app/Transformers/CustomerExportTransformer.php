<?php

namespace App\Transformers;

use App\Models\Customer;
use League\Fractal\TransformerAbstract;

class CustomerExportTransformer extends TransformerAbstract
{
    protected $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function transform(Customer $customer)
    {

        $total_spent = (float)isset($customer->orders) ? $customer->orders->sum('total_price') : 0;

        return [
            'id'                    => $customer->id,
            'created_at'            => $customer->created_at->format('Y-m-d H:i:s'),
            'name'                  => trim($customer->name) ? : '#'.$customer->id,
            'purchases'             => isset($customer->orders) ? $customer->orders->count() : 0,
            'total_spend'           => '$' . ($total_spent ? number_format($total_spent, 2) : $total_spent),
            'total_points_earned'   => isset($customer->earned_points) ? $customer->earned_points->sum('point_value') : 0,
            'current_points'        => isset($customer->points) ? $customer->points->sum('point_value') : 0,
            'tier'                  => $customer->getTierName(),
            'joined_tier'           => $customer->tier_history()->first() ? $customer->tier_history()->value('created_at')->format('Y-m-d H:i:s') : '',
        ];
    }
}
