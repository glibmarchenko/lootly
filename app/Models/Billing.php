<?php

namespace App\Models;

use App\User;
use App\Merchant;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $table = 'billings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'merchant_id', 'plan_id', 'name', 'price', 'period', 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
