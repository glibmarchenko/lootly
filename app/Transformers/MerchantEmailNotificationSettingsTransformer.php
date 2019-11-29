<?php

namespace App\Transformers;

use App\Models\MerchantEmailNotificationSettings;
use League\Fractal\TransformerAbstract;

class MerchantEmailNotificationSettingsTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'merchant'
    ];

    public function transform(MerchantEmailNotificationSettings $notificationSettings)
    {

        return [
            'id' => $notificationSettings->id,
            'merchant_id' => $notificationSettings->merchant_id,
            'from_name' => $notificationSettings->from_name,
            'reply_to_name' => $notificationSettings->reply_to_name,
            'reply_to_email' => $notificationSettings->reply_to_email,
            'company_logo' => $notificationSettings->company_logo,
            'company_logo_name' => $notificationSettings->company_logo_name,
            'custom_domain' => $notificationSettings->custom_domain,
            'remove_branding' => $notificationSettings->remove_branding,
            'created_at' => $notificationSettings->created_at,
            'updated_at' => $notificationSettings->updated_at,
        ];
    }

    public function includeMerchant(MerchantEmailNotificationSettings $notificationSettings)
    {
        $merchant = $notificationSettings->merchant;

        if(!$merchant){
            return null;
        }

        return $this->item($merchant, new MerchantTransformer);
    }

}