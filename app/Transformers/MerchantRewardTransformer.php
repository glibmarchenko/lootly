<?php

namespace App\Transformers;

use App\Models\MerchantReward;
use League\Fractal\TransformerAbstract;

class MerchantRewardTransformer extends TransformerAbstract
{
    public function transform(MerchantReward $merchantReward)
    {
        return [
            'id'                      => $merchantReward->id,
            'merchant_id'             => $merchantReward->merchant_id,
            'reward_id'               => $merchantReward->reward_id,
            'reward'                  => $merchantReward->reward,
            'type_id'                 => $merchantReward->type_id,
            'reward_type'             => $merchantReward->reward_type,
            'reward_text'             => $merchantReward->reward_text,
            'reward_default_text'     => $merchantReward->rewardDefaultText,
            'reward_name'             => $merchantReward->reward_name,
            'reward_display_name'     => $merchantReward->reward_display_name,
            'reward_icon'             => $merchantReward->reward_icon,
            'reward_icon_name'        => $merchantReward->reward_icon_name,
            'points_required'         => $merchantReward->points_required,
            'reward_value'            => $merchantReward->reward_value,
            'variable_reward_value'   => $merchantReward->variable_reward_value,
            'variable_point_cost'     => $merchantReward->variable_point_cost,
            'variable_point_min'      => $merchantReward->variable_point_min,
            'variable_point_max'      => $merchantReward->variable_point_max,
            'max_shipping'            => $merchantReward->max_shipping,
            'coupon_prefix'           => $merchantReward->coupon_prefix,
            'coupon_expiration'       => $merchantReward->coupon_expiration,
            'coupon_expiration_time'  => $merchantReward->coupon_expiration_time,
            'zap_status'              => $merchantReward->zap_status,
            'zap_name'                => $merchantReward->zap_name,
            'order_minimum'           => $merchantReward->order_minimum,
            'category_id'             => $merchantReward->category_id,
            'product'                 => $merchantReward->product,
            'product_title'           => $merchantReward->product_title,
            'send_email_notification' => $merchantReward->send_email_notification,
            'active_flag'             => $merchantReward->active_flag,
            'restrictions_enabled'    => $merchantReward->restrictions_enabled,
            'created_at'              => $merchantReward->created_at,
            'updated_at'              => $merchantReward->updated_at,
        ];
    }
}
