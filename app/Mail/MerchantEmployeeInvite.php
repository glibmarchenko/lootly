<?php

namespace App\Mail;

use App\Merchant;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MerchantEmployeeInvite extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $merchant;

    public $createdAutomatically;

    public $password;

    /**
     * Create a new message instance.
     *
     * @param \App\User     $user
     * @param \App\Merchant $merchant
     * @param bool          $createdAutomatically
     * @param string        $password
     *
     * @internal param \App\Models\Plan $plan
     *
     * @internal param bool $createdAutomatically
     * @internal param string $password
     */
    public function __construct(User $user, Merchant $merchant, $createdAutomatically = false, $password = '')
    {
        $this->user = $user;
        $this->merchant = $merchant;
        $this->createdAutomatically = $createdAutomatically;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Lootly Invite Employee')->view('emails.merchants.employee-invite');
    }
}
