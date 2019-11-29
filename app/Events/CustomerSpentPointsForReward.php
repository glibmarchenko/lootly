<?php

namespace App\Events;

use App\Models\Coupon;
use App\Models\MerchantAction;
use App\Models\MerchantReward;
use App\Models\Point;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CustomerSpentPointsForReward
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $merchantReward;

    public $point;

    public $coupon;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\MerchantReward $merchantReward
     * @param \App\Models\Point          $point
     * @param \App\Models\Coupon         $coupon
     *
     */
    public function __construct(MerchantReward $merchantReward, Point $point, Coupon $coupon)
    {
        $this->merchantReward = $merchantReward;
        $this->point = $point;
        $this->coupon = $coupon;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
