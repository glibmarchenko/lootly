<?php

namespace App\Models;

use App\Models\PaidPermission;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    const TYPE_GROWTH = 'growth';
    const TYPE_ULTIMATE = 'ultimate';
    const TYPE_ENTERPRISE = 'enterprise';
    const TYPE_FREE = 'free';

    protected $table = 'plans';

    protected $guarded = [];

    public function paid_permissions(){
        return $this->belongsToMany(PaidPermission::class, 'plans_permissions', 'plan_id', 'paid_permission_id');
    }

    // public function merchants(){
    //     return $this->hasMany('App\Merchant', 'plan_id');
    // }

    public function getUniqueFeatures(){
        return \Config::get('permissions.features.'.$this->type);
    }

    public function subscription() {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    public function billings(){
        return $this->hasMany(Billing::class, 'plan_id');
    }

    public function isEnterprise()
    {
        return $this->type === self::TYPE_ENTERPRISE;
    }
}
