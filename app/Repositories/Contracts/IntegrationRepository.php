<?php

namespace App\Repositories\Contracts;

interface IntegrationRepository
{
    public function findMerchant($integrationId);

    public function findMerchantWhere($integrationId, array $conditions);
}