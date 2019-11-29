<?php

namespace App\Repositories\Contracts;

interface SubscriptionRepository
{
    public function updateOrCreate(array $conditions, array $data);

    public function where($column, $operator = null, $value = null, $boolean = 'and');
}
