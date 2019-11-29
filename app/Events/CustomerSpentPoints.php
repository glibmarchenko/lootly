<?php

namespace App\Events;

use App\Models\Customer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CustomerSpentPoints
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $merchantReward;

    public $customer;

    public $pointsBalance;

    /**
     * Create a new event instance.
     *
     * @param null                 $merchantReward
     * @param \App\Models\Customer $customer
     * @param int                  $pointsBalance
     *
     */
    public function __construct($merchantReward = null, Customer $customer, $pointsBalance = 0)
    {
        $this->merchantReward = $merchantReward;
        $this->customer = $customer;
        $this->pointsBalance = $pointsBalance;
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
