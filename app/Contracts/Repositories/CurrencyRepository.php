<?php

namespace App\Contracts\Repositories;

interface CurrencyRepository
{
    public function get();

    public function find($id);
}
