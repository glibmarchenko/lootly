<?php

namespace App\Repositories\Eloquent;

use App\Models\Integration;
use App\Repositories\Contracts\IntegrationRepository;
use App\Repositories\RepositoryAbstract;

class EloquentIntegrationRepository extends RepositoryAbstract implements IntegrationRepository
{
    public function entity()
    {
        return Integration::class;
    }

    public function findMerchant($integrationId)
    {
        return $this->find($integrationId)->merchant()->get();
    }

    public function findMerchantWhere($integrationId, array $conditions)
    {
        $table_name = 'merchant_integrations';
        $merchant_conditions = [];
        foreach ($conditions as $key => $value) {
            $merchant_conditions[$table_name.'.'.$key] = $value;
        }

        return $this->find($integrationId)->merchant()->where($conditions)->get();
    }
}