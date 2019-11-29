<?php

namespace App\Repositories;

use App\Models\Billing;
use App\Contracts\Repositories\BillingRepository as BillingRepositoryContract;

class BillingRepository implements BillingRepositoryContract
{
    public function add($userObj, $data)
    {
        Billing::query()->create([
            'user_id' => $userObj->id,
            'name' => $data->name,
            'price' => $data->price,
            'date' => $data->billing_on,
        ]);

        return response()->json(['message' => 'Success Add billing']);
    }

    public function create(array $data)
    {
        return Billing::query()->create($data);
    }

    public function getByMerchant($merchantObj)
    {
        $billing = Billing::query()
            ->where('merchant_id', '=', $merchantObj->id)
            ->get();

        return $billing;
    }

    public function get($userObj)
    {
        $billing = Billing::query()
            ->where('user_id', '=', $userObj->id)
            ->get();

        return $billing;
    }
}
