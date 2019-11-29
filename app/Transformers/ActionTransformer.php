<?php

namespace App\Transformers;

use App\Models\MerchantAction;
use League\Fractal\TransformerAbstract;

class ActionTransformer extends TransformerAbstract
{

    public function transform(MerchantAction $action)
    {
        return [
            'action_icon'             => $action->action_icon,
            'action_icon_name'        => $action->action_icon_name,
            'action_id'               => $action->action_id,
            'action_name'             => $action->action_name,
            'active_flag'             => $action->active_flag,
            'content_url'             => $action->content_url,
            'created_at'              => $action->created_at,
            'earning_limit'           => $action->earning_limit,
            'earning_limit_value'     => $action->earning_limit_value,
            'earning_limit_type'      => $action->earning_limit_type,
            'earning_limit_period'    => $action->earning_limit_period,
            'fb_page_url'             => $action->fb_page_url,
            'goal'                    => $action->goal,
            'id'                      => $action->id,
            'is_fixed'                => $action->is_fixed,
            'merchant_id'             => $action->merchant_id,
            'option_1'                => $action->option_1,
            'option_2'                => $action->option_2,
            'point_value'             => $action->point_value,
            'review_status'           => $action->review_status,
            'review_type'             => $action->review_type,
            'reward_email_text'       => $action->reward_email_text,
            'reward_id'               => $action->reward_id,
            'reward_text'             => $action->reward_text,
            'send_email_notification' => $action->send_email_notification,
            'share_message'           => $action->share_message,
            'share_url'               => $action->share_url,
            'twitter_username'        => $action->twitter_username,
            'updated_at'              => $action->updated_at,
        ];
    }

}
