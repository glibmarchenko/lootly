<?php

namespace App\Transformers;

use App\Merchant;
use App\Models\Integration;
use App\Models\MerchantIntegrations;
use League\Fractal\TransformerAbstract;

class MerchantTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'merchant_currency',
        'points_settings',
        'details',
        'email_notification_settings'
    ];

    public function transform(Merchant $merchant)
    {
        return [
            'id' => $merchant->id,
            'owner_id' => $merchant->owner_id,
            'name' => $merchant->name,
            'website' => $merchant->website,
            'logo' => $merchant->logo_url,
            'logo_name' => $merchant->logo_name,
            'slug' => $merchant->slug,
            'store_id' => $merchant->store_id,
            'shopify_installed' => $merchant->shopify_installed ? true : false,
            'woocommerce' => $this->hasIntegration($merchant->integrations, 'woocommerce'),
            'zapier_connected' => $this->hasIntegration($merchant->integrations, 'zapier'),
            'integrations' => $this->getIntegrations($merchant),
            'currency_id' => $merchant->currency_id,
            'currency_display_sign' => $merchant->currency_display_sign ? 1 : 0,
            'language' => $merchant->language,
            'customer_accounts_enabled' => $merchant->customer_accounts_enabled,
            'created_at' => $merchant->created_at,
            'updated_at' => $merchant->updated_at,
        ];
    }

    public function getIntegrations(Merchant $merchant)
    {
        $integrations = [];
        foreach ($merchant->integrations as $integration) {
            $integrations[$integration->slug] = [
                'id' => $integration->id,
                'is_api' => $integration->isActiveApiByMerchant($merchant),
            ];
        }
        return $integrations;
    }

    public function includeMerchantCurrency(Merchant $merchant)
    {
        $currency = $merchant->merchant_currency;

        if (!$currency) {
            return null;
        }

        return $this->item($currency, new CurrencyTransformer);
    }

    public function includePointsSettings(Merchant $merchant)
    {
        $pointsSettings = $merchant->points_settings;

        if (!$pointsSettings) {
            return null;
        }

        return $this->item($pointsSettings, new PointSettingsTransformer());
    }

    public function includeDetails(Merchant $merchant)
    {
        $details = $merchant->detail;

        if (!$details) {
            return $this->null();
        }

        return $this->item($details, new OwnMerchantDetailsTransformer());
    }

    public function includeEmailNotificationSettings(Merchant $merchant)
    {
        $emailNotificationSettings = $merchant->email_notification_settings;

        if (!$emailNotificationSettings) {
            return $this->null();
        }

        return $this->item($emailNotificationSettings, new MerchantEmailNotificationSettingsTransformer());
    }

    private function hasIntegration($integrations, $slug): bool
    {
        return $integrations->filter(function ($integration) use ($slug) {
            return $integration->slug === $slug;
        }) ? true : false;
    }
}
