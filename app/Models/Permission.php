<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\EntrustPermission;


class Permission extends EntrustPermission
{


    protected $table = 'permissions';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'display_name', 'description'
    ];


    public function Role()
    {
        return $this->belongsToMany('App\Models\Role');
    }
}
