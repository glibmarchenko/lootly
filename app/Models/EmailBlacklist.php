<?php

namespace App\Models;

use App\Merchant;
use Illuminate\Database\Eloquent\Model;

class EmailBlacklist extends Model
{

    protected $table = 'email_blacklist';

    protected $fillable = [
        'merchant_id',
        'email',
    ];

    const UPDATED_AT = null;

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
