<?php

namespace App\Models;
use App\Models\Plan;

use Illuminate\Database\Eloquent\Model;

class PaidPermission extends Model
{
    protected $table = 'paid_permissions';

    protected $guarded = [];

    public function hasTypeCode(string $type_code) {
        if($this->type_code === $type_code){
            return true;
        } else {
            return false;
        }
    }

    public static function checkTypeCode($type_code){
        return PaidPermission::query()->where('type_code', '=', $type_code)->first();
    }

    public function plans(){
        return $this->belongsToMany(Plan::class, 'plans_permissions', 'paid_permission_id', 'plan_id');
    }

    public static function getByTypeCode($type_code){
        return PaidPermission::where('type_code', '=', $type_code)->first();
    }

    public function getMinPlan(){
        return $this->plans->sortBy('growth_order')->first();
    }
}