<?php

namespace App\Repositories\Eloquent;

use App\Models\CustomerReferralClick;
use App\Repositories\Contracts\CustomerReferralClickRepository;
use App\Repositories\RepositoryAbstract;

class EloquentCustomerReferralClickRepository extends RepositoryAbstract implements CustomerReferralClickRepository
{
    public function entity()
    {
        return CustomerReferralClick::class;
    }
}