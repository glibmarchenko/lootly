<?php

namespace App\Transformers;

use App\Models\Customer;
use App\Models\NotificationSettings;
use League\Fractal\TransformerAbstract;

class NotificationSettingsTransformer extends TransformerAbstract
{

    public function transform(NotificationSettings $notificationSettings)
    {

        return [
            'id' => $notificationSettings->id,
            'merchant_id' => $notificationSettings->merchant_id,
            'status' => $notificationSettings->status,
            'subject' => $notificationSettings->subject,
            'body' => $notificationSettings->body,
            'button_text' => $notificationSettings->button_text,
            'button_color' => $notificationSettings->button_color,
            'notification_type' => $notificationSettings->notification_type,
            'created_at' => $notificationSettings->created_at,
            'updated_at' => $notificationSettings->updated_at,
        ];
    }

    /*
    public function includeTier(Customer $customer)
    {
        return $this->collection($customer->tier, new TierTransformer);
    }
    */

}