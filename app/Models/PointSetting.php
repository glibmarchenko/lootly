<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Merchant;

class PointSetting extends Model
{

    const DEFAULT_SETTINGS = [
        'name' => 'Point',
        'plural_name' => 'Points'
    ];

    protected $table = 'point_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'plural_name',
        'currency',
        'merchant_id',
        'status',
        'experient_after',
        'experient_status',
        'reminder_status',
        'final_reminder_status',
        'reminder_day',
        'final_reminder_day',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
