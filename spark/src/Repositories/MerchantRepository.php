<?php

namespace Laravel\Spark\Repositories;

use App\Merchant;
use App\User;
use Laravel\Spark\Contracts\Repositories\MerchantRepository as MerchantRepositoryContract;

class MerchantRepository implements MerchantRepositoryContract
{
    public function get()
    {
        return Merchant::query();
    }

    public function getWithOwner()
    {
        return Merchant::select('merchants.*')
            ->join('users', 'users.id', '=', 'merchants.owner_id')
            ->groupBy('merchants.id')
            ->orderBy('users.created_at', 'desc');
    }

    public function find($id)
    {
        return Merchant::find($id);
    }

    public function findOrFail($id)
    {
        return Merchant::findOrFail($id);
    }
}
