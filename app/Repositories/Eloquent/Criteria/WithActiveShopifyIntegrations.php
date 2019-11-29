<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class WithActiveShopifyIntegrations implements CriterionInterface
{
    public function apply($entity)
    {
        return $entity->with([
            'integrationsWithToken' => function ($q) {
                $q->where('merchant_integrations.status', 1);
                $q->where('integrations.slug', 'shopify');
            }
        ]);
    }
}
