<?php

namespace Laravel\Spark\Contracts\Repositories;

use App\Merchant;

interface MerchantRepository
{
    public function get();

    public function getWithOwner();

    public function find($id);

    public function findOrFail($id);
}
