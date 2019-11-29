<?php

namespace App\Events;

use App\Models\Customer;
use App\Models\MerchantReward;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CustomerHaveEnoughPointsForReward
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $merchantReward;

    public $customer;
    public $pointsBalance;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\MerchantReward $merchantReward
     * @param \App\Models\Customer       $customer
     * @param int                        $pointsBalance
     *
     */
    public function __construct(MerchantReward $merchantReward, Customer $customer, $pointsBalance = 0)
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
