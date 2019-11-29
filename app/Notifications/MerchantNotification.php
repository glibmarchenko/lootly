<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Laravel\Spark\Notifications\SparkChannel;
use Laravel\Spark\Notifications\SparkNotification;

class MerchantNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $body;


    public function __construct($data)
    {
        $this->body = $data;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */

    public function via($notifiable)
    {
        return [SparkChannel::class, 'mail'];
    }

    public function toSpark($notifiable)
    {

        $url =preg_replace('#^https?://#', '', \URL::current());
        return (new SparkNotification())
            ->action($this->body, $url)
            ->icon('fa-user')
            ->body($this->body);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $from = 'support@shopify.com';
        $subject = 'Event Occurs';
        return (new MailMessage)
            ->from($from)
            ->subject($subject)
            ->line($this->body);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
