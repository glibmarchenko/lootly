<?php

namespace App\Transformers;

use App\Models\MerchantReward;
use League\Fractal\TransformerAbstract;

class PointRewardTransformer extends TransformerAbstract
{
    public function transform(MerchantReward $reward)
    {
        return [
            'active_flag' => $reward->active_flag,
            'category_id' => $reward->category_id,
            'coupon_expiration' => $reward->coupon_expiration,
            'coupon_expiration_time' => $reward->coupon_expiration_time,
            'coupon_prefix' => $reward->coupon_prefix,
            'created_at' => $reward->created_at,
            'id' => $reward->id,
            'max_shipping' => $reward->max_shipping,
            'merchant_id' => $reward->merchant_id,
            'order_minimum' => $reward->order_minimum,
            'points_required' => $reward->points_required,
            'product' => $reward->product,
            'reward_email_text' => $reward->reward_email_text,
            'reward_icon' => $reward->reward_icon,
            'reward_icon_name' => $reward->reward_icon_name,
            'reward_id' => $reward->reward_id,
            'reward_name' => $reward->reward_name,
            'reward_text' => $reward->reward_text,
            'reward_type' => $reward->reward_type,
            'reward_value' => $reward->reward_value,
            'send_email_notification' => $reward->send_email_notification,
            'type_id' => $reward->type_id,
            'updated_at' => $reward->updated_at,
            'variable_point_cost' => $reward->variable_point_cost,
            'variable_point_max' => $reward->variable_point_max,
            'variable_point_min' => $reward->variable_point_min,
            'variable_reward_value' => $reward->variable_reward_value,
        ];
    }
}