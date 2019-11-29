<?php

namespace App\Repositories;


use App\Models\MerchantUser;
use App\Contracts\Repositories\UserMerchantRepository as UserMerchantRepositoryContract;


class UserMerchantRepository implements UserMerchantRepositoryContract
{
    public function get($id)
    {
        // TODO: Implement find() method.
    }

    public function create($userObj, $merchantId)
    {
        MerchantUser::query()->create([
            'user_id' => $userObj->id,
            'merchant_id' => $merchantId,
            'role' => 'owner'
        ]);
    }
}
