<?php

namespace App\Transformers;

use App\Models\Point;
use League\Fractal\TransformerAbstract;

class PointsActivityViewTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Point $point)
    {
        return [
            'action_name' => $point->getActionName(),
            // 'name' => $point->customer->name,
            'name' => $point->customer_name,
            'id' => $point->customer_id,
            'point_value' => $point->point_value,
            'created_at' => $point->created_at . '',
            'is_earning' => $point->merchant_action_id ?? 0,
            'is_spending' => $point->merchant_reward_id ?? 0,
            'action_type' => (isset($action->merchant_action_id) || isset($action->merchant_reward_id)) ? $action->getActionName() : 'Admin'
        ];
    }
}