<?php

namespace App\Repositories\Contracts;

use App\Merchant;
use App\User;

interface MerchantRepository
{
    public function updateIntegrations(Merchant $merchant, $integrationId, array $data);

    public function createMerchant(User $user, array $data);

    public function orders($merchantId);

    public function referredOrders($id);
}