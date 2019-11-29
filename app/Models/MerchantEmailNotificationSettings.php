<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Merchant;

class MerchantEmailNotificationSettings extends Model
{
    protected $fillable = [
        'from_name',
        'reply_to_name',
        'reply_to_email',
        'reply_to_email',
        'company_logo',
        'custom_domain',
        'remove_branding',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
