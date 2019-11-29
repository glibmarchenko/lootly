<?php

namespace App\Transformers;

use App\Models\ReferralSharing;
use League\Fractal\TransformerAbstract;

class ReferralSharingTransformer extends TransformerAbstract
{
    public function transform(ReferralSharing $item)
    {
        return [
            'id'          => $item->id,
            'merchant_id' => $item->merchant_id,

            'facebook_status'    => $item->facebook_status,
            'facebook_message'   => $item->facebook_message,
            'facebook_icon'      => $item->facebook_icon,
            'facebook_icon_name' => $item->facebook_icon_name,

            'twitter_status'    => $item->twitter_status,
            'twitter_message'   => $item->twitter_message,
            'twitter_icon'      => $item->twitter_icon,
            'twitter_icon_name' => $item->twitter_icon_name,

            'google_status'    => $item->google_status,
            'google_message'   => $item->google_message,
            'google_icon'      => $item->google_icon,
            'google_icon_name' => $item->google_icon_name,

            'email_status'  => $item->email_status,
            'email_subject' => $item->email_subject,
            'email_body'    => $item->email_body,

            'share_title'       => $item->share_title,
            'share_description' => $item->share_description,

            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
        ];
    }
}