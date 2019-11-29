<?php

namespace App\Transformers;

use App\Models\MerchantAction;
use League\Fractal\TransformerAbstract;
use Carbon\Carbon;

class PopularEarningActionsTransformer extends TransformerAbstract
{
    protected $start;
    protected $end;

    public function __construct(\DatePeriod $period){
        $this->start = Carbon::instance($period->getStartDate())->startOfDay();
        $this->end = Carbon::instance($period->getStartDate()->add($period->getDateInterval()))->endOfDay();
    }

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(MerchantAction $action)
    {
        $points = $action->point;
        if(isset($points)){
            $points = $points
                ->where('created_at', '>', $this->start)
                ->where('created_at', '<', $this->end);
            return [
                'name' => $action->action_name,
                'action_type' => $action->action->name,
                'reward' => $action->reward_text,
                'points_earned' => $points->sum('point_value'),
                'completed_actions' => $points->count()
            ];
        } else {
            return [
                'name' => $action->action_name,
                'action_type' => $action->action->name,
                'reward' => $action->reward_text,
                'points_earned' => 0,
                'completed_actions' => 0
            ];
        }
    }
}
