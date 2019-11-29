<?php

namespace App\Helpers;

use App\Models\Customer;
use App\Models\Order;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Eloquent\Criteria\HasCustomerWhere;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected $customers;

    protected $orders;

    public function __construct(CustomerRepository $customers, OrderRepository $orders)
    {
        $this->customers = $customers;
        $this->orders = $orders;
    }

    public function create(Customer $customer, array $orderData = [])
    {
        if (! isset($orderData['customer_id']) || $orderData['customer_id'] != $customer->id) {
            $orderData['customer_id'] = $customer->id;
        }

        $order = $this->orders->create($orderData);

        if ($order) {
            try {
                $this->customers->incrementOrdersCounter($customer->id);
            } catch (\Exception $e) {
                Log::error('Can not increment orders counter for customer #'.$customer->id.'. '.$e->getMessage());
            }
        }

        return $order;
    }

    public function update(Order $order, array $data = [])
    {
        return $this->orders->update($order->id, $data);
    }

    public function cancel(Order $order, $status = 'voided')
    {
        return $this->orders->update($order->id, [
            'status' => $status,
        ]);
    }

    public function findWhere($merchantId, array $conditions)
    {
        $whereQuery = [];
        if (isset($conditions['order'])) {
            $whereQuery = array_merge($whereQuery, $conditions['order']);
        }
        $criteria = [];
        if (isset($conditions['customer'])) {
            $customerConditions = array_merge(['merchant_id' => $merchantId], $conditions['customer']);
            $criteria[] = new HasCustomerWhere($customerConditions);
        }

        try {
            $order = $this->orders;
            if (count($criteria)) {
                $order = $order->withCriteria($criteria);
            }
            $order = $order->findWhereFirst($whereQuery);

            return $order;
        } catch (\Exception $e) {

        }

        return null;
    }
}