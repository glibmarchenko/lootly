<?php

namespace App\Repositories\Contracts;

interface CustomerRepository
{
    public function incrementOrdersCounter($id);

    public function incrementSharesCounter($id);

    public function incrementClicksCounter($id);

    public function findReferrer($customerId);

    public function findReferred($customerId);

    public function updateOrCreate(array $conditions, array $data);

    public function countValidOrders($customerId);

    public function getTotalSpent($customerId);
}