<?php

namespace App\Transformers;

use App\Models\Customer;
use League\Fractal\TransformerAbstract;

class ReceiverRewardTransformer extends TransformerAbstract
{
    public function transform(Customer $item)
    {
        $reward = $item->merchant->rewards[0];

        return [
            'customer_id'             => $item->id,
            'merchant_id'             => $item->merchant_id,
            'customer_name'           => $item->name,
            'referral_slug'           => $item->referral_slug,
            'reward_id'               => $reward->reward_id,
            'type_id'                 => $reward->type_id,
            'reward_type'             => $reward->reward_type,
            'reward_text'             => $reward->reward_text,
            'reward_name'             => $reward->reward_name,
            'reward_icon'             => $reward->reward_icon,
            'reward_icon_name'        => $reward->reward_icon_name,
            'points_required'         => $reward->points_required,
            'reward_value'            => $reward->reward_value,
            'variable_reward_value'   => $reward->variable_reward_value,
            'variable_point_cost'     => $reward->variable_point_cost,
            'variable_point_min'      => $reward->variable_point_min,
            'variable_point_max'      => $reward->variable_point_max,
            'max_shipping'            => $reward->max_shipping,
            'coupon_prefix'           => $reward->coupon_prefix,
            'coupon_expiration'       => $reward->coupon_expiration,
            'coupon_expiration_time'  => $reward->coupon_expiration_time,
            'order_minimum'           => $reward->order_minimum,
            'category_id'             => $reward->category_id,
            'product'                 => $reward->product,
            'send_email_notification' => $reward->send_email_notification,
            'active_flag'             => $reward->active_flag,
            'created_at'              => $reward->created_at,
            'updated_at'              => $reward->updated_at,
        ];
    }
}