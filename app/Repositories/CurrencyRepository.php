<?php

namespace App\Repositories;

use App\Models\Currency;
use App\Contracts\Repositories\CurrencyRepository as CurrencyRepositoryContract;

class CurrencyRepository implements CurrencyRepositoryContract
{
    public function get()
    {
        return Currency::all();
    }

    public function find($id)
    {
        return Currency::where('id', $id)->first();
    }

}
