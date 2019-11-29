<?php

namespace App\Transformers;

use App\Models\Currency;
use League\Fractal\TransformerAbstract;

class CurrencyTransformer extends TransformerAbstract
{

    public function transform(Currency $currency)
    {

        return [
            'id' => $currency->id,
            'name' => $currency->name,
            'display_type' => $currency->display_type,
            'currency_sign' => $currency->currency_sign,
            'created_at' => $currency->created_at,
            'updated_at' => $currency->updated_at,
        ];
    }

    /*
    public function includeTier(Customer $customer)
    {
        return $this->collection($customer->tier, new TierTransformer);
    }
    */

}