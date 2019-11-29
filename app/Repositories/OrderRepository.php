<?php

namespace App\Repositories;

use App\Merchant;
use App\Models\Customer;
use App\Models\Order;
use App\Contracts\Repositories\OrderRepository as OrderRepositoryContract;

class OrderRepository implements OrderRepositoryContract
{
    public $baseQuery;

    public function __construct()
    {
        $this->baseQuery = Order::query();
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    public function findByOrderId(Merchant $merchant, $orderId){
        return Order::where(['order_id' => $orderId])->with(['customer' => function($q) use ($merchant) {
            $q->where('merchant_id', $merchant->id);
        }])->whereHas('customer', function($q) use ($merchant) {
            $q->where('merchant_id', $merchant->id);
        })->orderBy('created_at', 'desc')->first();
    }

    public function create($customer, array $data = [])
    {
        if (! isset($data['order_id']) || ! trim($data['order_id'])) {
            return null;
        }

        $order = Order::make();
        $order->customer_id = $customer->id;
        $order->fill($data);
        $order->save();

        return $order;
    }

    public function update($orderId, array $data = [])
    {
        if (! isset($orderId) || ! trim($orderId)) {
            return null;
        }

        return Order::where('id', $orderId)->update($data);

    }

    public function delete($user)
    {
        // TODO: Implement delete() method.
    }

    public function getTotalSpent(Customer $customer)
    {
        $paidOrders = $customer->orders()->where(function ($q) {
                $q->where(function ($q_) {
                    //$q_->where('status', '!=', 'refunded');
                    $q_->where('status', '!=', 'voided');
                });
                $q->orWhereNull('status');
            });

        $total_price_sum = $paidOrders->sum('total_price');
        $refunded_total_sum = $paidOrders->sum('refunded_total');

        return floatval($total_price_sum) - floatval($refunded_total_sum);
    }

    public function countValidOrders(Customer $customer)
    {
        return $customer->orders()->where(function ($q) {
                $q->where(function ($q_) {
                    $q_->where('status', '!=', 'refunded');
                    $q_->where('status', '!=', 'voided');
                });
                $q->orWhereNull('status');
            })->count();
    }
}
