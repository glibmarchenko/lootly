<?php

namespace App\Transformers;

use App\Models\Point;
use League\Fractal\TransformerAbstract;

class PointProfileTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Point $point)
    {
        return [
            'activity' => $point->getActionName(),
            'points' => $point->point_value,
            'code' => $point->coupon ? $point->coupon->code : '',
            'date' => $point->created_at . ""
        ];
    }
}
