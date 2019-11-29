<?php

namespace App\Mail;

use App\Merchant;
use App\Models\MerchantReward;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MerchantCouponsEmpty extends Mailable
{
    use Queueable, SerializesModels;

    public $merchant;

    public $reward;

    /**
     * Create a new message instance.
     *
     * @param Merchant        $merchant
     * @param MerchantReward  $reward
     *
     */
    public function __construct(Merchant $merchant, MerchantReward $reward)
    {
        $this->merchant = $merchant;
        $this->reward = $reward;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Lootly - Reward Codes Empty')->view('emails.merchants.coupons-empty');
    }
}
