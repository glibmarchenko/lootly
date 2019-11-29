<?php

namespace App\Repositories\Eloquent;

use App\Models\CustomerReferralShare;
use App\Repositories\Contracts\CustomerReferralShareRepository;
use App\Repositories\RepositoryAbstract;

class EloquentCustomerReferralShareRepository extends RepositoryAbstract implements CustomerReferralShareRepository
{
    public function entity()
    {
        return CustomerReferralShare::class;
    }
}