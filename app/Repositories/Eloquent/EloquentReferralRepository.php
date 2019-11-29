<?php

namespace App\Repositories\Eloquent;

use App\Models\Referral;
use App\Repositories\Contracts\ReferralRepository;
use App\Repositories\RepositoryAbstract;

class EloquentReferralRepository extends RepositoryAbstract implements ReferralRepository
{
    public function entity()
    {
        return Referral::class;
    }
}