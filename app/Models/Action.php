<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Action extends Authenticatable
{
    const CREATE_PARCHES_ACTION = 'Make a Purchase';

    protected $table = 'actions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'icon',
        'description',
        'url',
        'type',
        'priority',
    ];

    public function merchantAction()
    {
        return $this->hasMany('App\Models\MerchantAction', 'action_id', 'id');
    }

    public function getEarnedActionTextAttribute()
    {
        $earnedActionText = $this->earned_action_text;

        switch ($earnedActionText) {
            default:
                $earnedActionText = 'completing action '.$this->name;
                break;
        }

        return $earnedActionText;
    }
}
