<?php

namespace App\Repositories;


use App\Models\ChargePlan;
use App\Contracts\Repositories\PaymentRepository as PaymentRepositoryContract;


class PaymentRepository implements PaymentRepositoryContract
{
    public function find($id)
    {
        // TODO: Implement find() method.
    }


    /**
     * @param $charge_plan
     * @param $merchant_id
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function create($charge_plan, $merchant_id)
    {
        $charge_plan = ChargePlan::query()->create([
            'recurring_charge_id' => $charge_plan->id,
            'merchant_id' => $merchant_id,
            'name' => $charge_plan->name,
            'price' => $charge_plan->price,
        ]);
        return $charge_plan;
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        ChargePlan::query()->where('recurring_charge_id','=', $id)->delete();
    }
}
