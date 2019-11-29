<?php

namespace App\Transformers;

use App\Models\Integration;
use League\Fractal\TransformerAbstract;

class MerchantIntegrationTransformer extends TransformerAbstract
{
    public function transform(Integration $integration)
    {
        if ($integration->pivot) {
            return [
                'merchant_id' => $integration->pivot->merchant_id,
                'status'      => $integration->pivot->status,
                'settings'    => isset($integration->pivot->settings) ? json_decode($integration->pivot->settings,true) : [],
                'created_at'  => $integration->pivot->created_at,
                'created'     => $integration->pivot->created_at ? $integration->pivot->created_at->toDateTimeString() : null,
                'updated_at'  => $integration->pivot->updated_at,
                'updated'     => $integration->pivot->updated_at ? $integration->pivot->updated_at->toDateTimeString() : null,
                'integration' => [
                    'id'          => $integration->id,
                    'title'       => $integration->title,
                    'slug'        => $integration->slug,
                    'description' => $integration->description,
                    'icon'        => $integration->icon,
                    'logo'        => $integration->logo,
                    'status'      => $integration->status,
                    'created_at'  => $integration->created_at,
                    'created'     => $integration->created_at ? $integration->created_at->toDateTimeString() : null,
                    'updated_at'  => $integration->updated_at,
                    'updated'     => $integration->updated_at ? $integration->updated_at->toDateTimeString() : null,
                ],
            ];
        }

        return [];
    }
}