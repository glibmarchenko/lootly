<?php

namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenericInviteEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    public $subject;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject)
    {
        $this->subject = $subject;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $from = 'invite@' . preg_replace('#^https?://#', '', \URL::to('/'));

        return $this->from(trim($from))
            ->subject($this->subject)
            ->view('vendor.spark.emails.invite_employee')
            ->with([
                'title' => $this->subject,
            ]);
    }
}
