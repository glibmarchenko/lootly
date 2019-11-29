<?php

namespace App\Events;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\MerchantReward;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ReferralReceiverRewardCouponGenerated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $customer;

    public $merchantReward;

    public $coupon;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Customer       $customer
     * @param \App\Models\MerchantReward $merchantReward
     * @param \App\Models\Coupon         $coupon
     *
     */
    public function __construct(
        Customer $customer,
        MerchantReward $merchantReward,
        Coupon $coupon
    ) {
        $this->customer = $customer;
        $this->merchantReward = $merchantReward;
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
