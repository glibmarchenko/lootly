<?php

namespace App\Transformers;

use App\Models\MerchantAction;
use League\Fractal\TransformerAbstract;

class CustomerMerchantActionTransformer extends TransformerAbstract
{
    public function transform(MerchantAction $item)
    {
        $link = '';
        if ($item->action) {
            switch ($item->action->url) {
                case 'facebook-like':
                    $link = $item->fb_page_url;
                    break;
                case 'facebook-share':
                    $link = $item->share_url;
                    break;
                case 'twitter-follow':
                    $link = trim($item->twitter_username, '@');
                    break;
                case 'instagram-follow':
                    $link = trim($item->instagram_username, '@');
                    break;
                case 'twitter-share':
                    $link = $item->share_url;
                    break;
                case 'trustspot-review':
                    $link = '';
                    break;
                case 'read-content':
                    $link = $item->content_url;
                    break;
            }
        }

        return [
            'id'                      => $item->id,
            'merchant_id'             => $item->merchant_id,
            'action_id'               => $item->action_id,
            'action'                  => $item->action,
            'action_name'             => $item->action_name,
            'action_icon'             => $item->action_icon,
            'action_icon_name'        => $item->action_icon_name,
            'earning_limit'           => $item->earning_limit,
            'earning_limit_value'     => $item->earning_limit_value,
            'earning_limit_type'      => $item->earning_limit_type,
            'earning_limit_period'    => $item->earning_limit_period,
            'reward_text'             => $item->reward_text,
            'reward_default_text'     => strtr($item->reward_default_text, [
                                            "{points}" => $item->point_value, 
                                            "{orders}" => $item->goal, 
                                            "{amount}" => $item->goal ]),
            'point_value'             => $item->point_value,
            'link'                    => $link,
            'fb_page_url'             => $item->fb_page_url,
            'share_url'               => $item->share_url,
            'share_message'           => $item->share_message,
            'twitter_username'        => $item->twitter_username,
            'instagram_username'      => $item->instagram_username,
            'content_url'             => $item->content_url,
            'review_type'             => $item->review_type,
            'review_status'           => $item->review_status,
            'goal'                    => $item->goal,
            'goal_unit'               => $item->goal_unit,
            'option_1'                => $item->option_1,
            'option_2'                => $item->option_2,
            'reward_id'               => $item->reward_id,
            'reward_email_text'       => $item->reward_email_text,
            'is_fixed'                => $item->is_fixed,
            'send_email_notification' => $item->send_email_notification,
            'active_flag'             => $item->active_flag,
            'is_completed'            => $item->completed ?? 0,
            'created_at'              => $item->created_at,
            'updated_at'              => $item->updated_at,
        ];
    }
}
