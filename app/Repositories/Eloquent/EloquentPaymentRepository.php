<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepository;
use App\Repositories\RepositoryAbstract;

class EloquentPaymentRepository extends RepositoryAbstract implements PaymentRepository
{
    public function entity()
    {
        return Payment::class;
    }
}