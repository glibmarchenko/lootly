<?php

namespace App\Models;

use App\Merchant;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    const SLUG_CUSTOM_API = 'custom-api';

    protected $casts = [
        'is_api' => 'boolean',
    ];

    public function merchant()
    {
        return $this->belongsToMany(
            Merchant::class, 'merchant_integrations', 'integration_id', 'merchant_id'
        )->withPivot('status', 'settings')->withTimestamps();
    }

    public function isActive(Merchant $merchant){
        try{
            $activeMerchant = $this->merchant->where('id', '=', $merchant->id);
            if($activeMerchant->isEmpty()) {
                return false;
            }
            return true;
        } catch (Exception $e){
            return false;
        }
    }

    public function isCustomApi()
    {
        return $this->slug === self::SLUG_CUSTOM_API;
    }

    public function showForPlan(Plan $plan): bool
    {
        if ($this->isCustomApi()) {
            if (! $plan->isEnterprise()) {
                return false;
            }
        }
        return true;
    }

    public function isActiveApiByMerchant(Merchant $merchant): bool
    {
        return $this->is_api && $this->getActiveMerchantIntegration($merchant) !== null;
    }

    public function getActiveMerchantIntegration(Merchant $merchant): ?MerchantIntegrations
    {
        return MerchantIntegrations::where([
            'merchant_id' => $merchant->id,
            'integration_id' => $this->id,
            'status' => 1
        ])->first();
    }
}
