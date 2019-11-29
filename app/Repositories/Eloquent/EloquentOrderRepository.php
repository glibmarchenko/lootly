<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\RepositoryAbstract;

class EloquentOrderRepository extends RepositoryAbstract implements OrderRepository
{
    public function entity()
    {
        return Order::class;
    }
}