<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReferralShareEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $sender;

    public $receiverName;

    public $receiverEmail;

    public $subject;

    public $body;

    /**
     * Create a new message instance.
     *
     * @param \App\User $sender
     * @param           $receiverName
     * @param           $receiverEmail
     * @param           $subject
     * @param           $body
     *
     */
    public function __construct(User $sender, $receiverName, $receiverEmail, $subject, $body)
    {
        $this->sender = $sender;
        $this->receiverName = $receiverName;
        $this->receiverEmail = $receiverEmail;
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->to($this->receiverEmail)->view('emails.referral.share-email');
    }
}
