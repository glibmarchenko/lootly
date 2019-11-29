<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardsPageBranding extends Model
{
    protected $guarded = [];
    protected $table = 'rewards_page_branding';

    public function merchant()
    {
        return $this->belongsTo("App\Merchant", 'merchant_id');
    }
}