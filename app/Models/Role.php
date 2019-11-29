<?php

namespace App\Models;


use Zizaco\Entrust\EntrustRole;


class Role extends EntrustRole
{


    protected $table = 'roles';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'display_name', 'description'
    ];

    public function getCustomer()
    {
        return Role::query()->where('name', '=', 'customer')->first();
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
