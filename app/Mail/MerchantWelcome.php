<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MerchantWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $createdAutomatically;
    public $password;

    /**
     * Create a new message instance.
     *
     * @param \App\User $user
     * @param bool      $createdAutomatically
     * @param string    $password
     */
    public function __construct(User $user, $createdAutomatically = false, $password = '')
    {
        $this->user = $user;
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
        return $this->subject('Lootly New Account Welcome')->view('emails.merchants.welcome');
    }
}
