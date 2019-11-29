<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Merchant;

class Currency extends Model
{

    const DEFAULT_CURRENCY_NAME = 'USD';
    const DEFAULT_CURRENCY_DISPLAY_SIGN = true;

    protected $table = 'currencies';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'display_type'
    ];

    public function merchants()
    {
        return $this->hasMany(Merchant::class, 'currency_id');
    }

}
