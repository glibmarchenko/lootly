<?php

namespace App\Events;

use App\Models\MerchantAction;
use App\Models\Point;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CustomerEarnedPointsForAction
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $merchantAction;

    public $point;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\MerchantAction $merchantAction
     * @param \App\Models\Point          $point
     *
     */
    public function __construct(MerchantAction $merchantAction, Point $point)
    {
        $this->merchantAction = $merchantAction;
        $this->point = $point;
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
