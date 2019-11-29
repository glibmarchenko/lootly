<?php

namespace App\Repositories\Eloquent;

use App\Models\MerchantUser;
use App\Repositories\Contracts\MerchantUserRepository;
use App\Repositories\RepositoryAbstract;

class EloquentMerchantUserRepository extends RepositoryAbstract implements MerchantUserRepository
{
    public function entity()
    {
        return MerchantUser::class;
    }
}