<?php

namespace App\Events;

use App\Models\Customer;
use App\Models\Tier;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CustomerEarnedVipTier
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $customer;

    public $tier;
    public $coupons;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Customer $customer
     * @param \App\Models\Tier     $tier
     * @param array                $coupons
     */
    public function __construct(Customer $customer, Tier $tier, $coupons = [])
    {
        $this->customer = $customer;
        $this->tier = $tier;
        $this->coupons = $coupons;
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
