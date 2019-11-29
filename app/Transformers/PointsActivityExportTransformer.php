<?php

namespace App\Transformers;

use App\Models\Point;
use League\Fractal\TransformerAbstract;

class PointsActivityExportTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param Point $point
     *
     * @return array
     */
    public function transform(Point $point)
    {
        return [
            'customer_id'             => $point->customer_id,
            'name'                    => $point->customer_name,
            'activity_name'           => $point->getActionName(),
            'order_id'                => $point->order_id,
            'total_order_amount'      => $point->total_order_amount,
            'rewardable_order_amount' => $point->rewardable_order_amount,
            'points_value'            => $point->points_value,
            'tier_multiple'           => $point->tier_multiplier,
            'coupon_code'             => $point->coupon ? $point->coupon->coupon_code : null,
            'point_value'             => $point->point_value,
            'is_earning'              => $point->merchant_action_id ?? 0,
            'is_spending'             => $point->merchant_reward_id ?? 0,
            'created_at'              => $point->created_at . '',
        ];
    }
}
