<?php

namespace App\Interactions\Settings\Store;

use Illuminate\Support\Facades\Validator;

use App\Contracts\Interactions\Settings\Store\UpdateStoreDetail as Contract;

class UpdateStoreDetail implements Contract
{
    /**
     * {@inheritdoc}
     */
    public function validator($store, array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'currency' => 'required|max:255',
            'currency_display' => 'required|max:5',
            'language' => 'required|max:255',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function handle($store, array $data)
    {
        $store->forceFill([
            'name' => $data['name'],
            'currency' => $data['currency'],
            'currency_display' => $data['currency_display'],
            'language' => $data['language'],
        ])->save();


        return $store;
    }
}
