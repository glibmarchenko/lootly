<?php

namespace App\Repositories\Eloquent;

use App\Models\Currency;
use App\Repositories\Contracts\CurrencyRepository;
use App\Repositories\RepositoryAbstract;

class EloquentCurrencyRepository extends RepositoryAbstract implements CurrencyRepository
{
    public function entity()
    {
        return Currency::class;
    }
}