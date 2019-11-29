<?php

namespace App\Transformers;

use App\Models\Point;
use League\Fractal\TransformerAbstract;

class PointTransformer extends TransformerAbstract
{

    protected $availableIncludes = ['action', 'reward', 'coupon'];

    public function transform(Point $point)
    {
        //dd($customer);
        return [
            'id' => $point->id,
            'merchant_id' => $point->merchant_id,
            'customer_id' => $point->customer_id,
            'point_value' => $point->point_value,
            'rollback' => $point->rollback,
            'merchant_action_id' => $point->merchant_action_id,
            'merchant_reward_id' => $point->merchant_reward_id,
            'coupon_id' => $point->coupon_id,
            'reason' => $point->reason,
            'title' => $point->title,
            'action_name' => $point->getActionName(),
            'order_id' => $point->order_id,
            'total_order_amount' => $point->total_order_amount,
            'rewardable_order_amount' => $point->rewardable_order_amount,
            'type' => $point->type,
            'expiration_date' => $point->expiration_date,
            'tier_multiplier' => $point->tier_multiplier,
            'referral_id' => $point->referral_id,
            'created_at' => $point->created_at ? $point->created_at->format('Y-m-d\TH:i:sP') : null,
            'updated_at' => $point->updated_at ? $point->updated_at->format('Y-m-d\TH:i:sP') : null,
        ];
    }

    public function includeAction(Point $point)
    {
        $action = $point->action;

        if(!$action){
            return null;
        }

        return $this->item($action, new ActionTransformer);
    }

    public function includeReward(Point $point)
    {
        $reward = $point->reward;

        if(!$reward){
            return null;
        }

        return $this->item($reward, new PointRewardTransformer);
    }

    public function includeCoupon(Point $point)
    {
        $coupon = $point->coupon;

        if(!$coupon){
            return null;
        }

        return $this->item($coupon, new CouponTransformer);
    }

}