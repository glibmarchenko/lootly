<?php

namespace App\Events;

use App\Models\MerchantReward;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ReferralShareEmail
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $customerId;

    public $receiverReward;

    public $receiverName;

    public $receiverEmail;

    public $emailSubject;

    public $emailBody;

    /**
     * Create a new event instance.
     *
     * @param                                           $customerId
     * @param \App\Models\MerchantReward                $receiverReward
     * @param                                           $receiverName
     * @param                                           $receiverEmail
     * @param                                           $emailSubject
     * @param                                           $emailBody
     *
     * @internal param \App\Merchant $merchant
     * @internal param \App\Models\Customer $sender
     * @internal param \App\Models\MerchantReward $merchantReward
     */
    public function __construct(
        $customerId,
        MerchantReward $receiverReward,
        $receiverName,
        $receiverEmail,
        $emailSubject,
        $emailBody
    ) {
        $this->customerId = $customerId;
        $this->receiverReward = $receiverReward;
        $this->receiverName = $receiverName;
        $this->receiverEmail = $receiverEmail;
        $this->emailSubject = $emailSubject;
        $this->emailBody = $emailBody;
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
