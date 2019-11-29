<?php

namespace App\Mail;

use App\Models\Plan;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MerchantSubscriptionCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $plan;

    /**
     * Create a new message instance.
     *
     * @param \App\User        $user
     * @param \App\Models\Plan $plan
     *
     * @internal param bool $createdAutomatically
     * @internal param string $password
     */
    public function __construct(User $user, Plan $plan)
    {
        $this->user = $user;
        $this->plan = $plan;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Lootly Plan Upgrade')->view('emails.merchants.subscription-cancelled');
    }
}
