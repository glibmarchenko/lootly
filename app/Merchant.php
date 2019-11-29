<?php

namespace App;

use App\User;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Integration;
use App\Models\MerchantAction;
use App\Models\MerchantDetail;
use App\Models\MerchantReward;
use App\Models\Tag;
use App\Models\Tier;
use App\Models\Plan;
use App\Models\Point;
use App\Models\Subscription;
use App\Models\Invitation;
use App\Models\PointSetting;
use App\Models\ReferralSetting;
use App\Models\RewardSetting;
use App\Models\RewardsPageBranding;
use App\Models\MerchantEmailNotificationSettings;
use App\Filters\Filterable;
use Laravel\Spark\Team as SparkTeam;

class Merchant extends SparkTeam
{
    use Filterable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'merchants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'website',
        'slug',
        'owner_id',
        'notification',
        'currency',
        'currency_id',
        'currency_display_sign',
        'language',
        'store_id',
        'location_id',
        'current_billing_plan',
        'billing_city',
        'billing_zip',
        'billing_country',
        'billing_address',
        'billing_address_line_2',
        'payment_provider',
        'stripe_id',
        'card_brand',
        'card_last_four',
        'card_country',
        'card_expiration',
        'customer_accounts_enabled',
        'trial_ends_at',
    ];

    protected $appends = [
        'photo_url',
    ];

    public function scopeSortDefault($query)
    {
        return $query->orderBy('id', 'desc');
    }

    /**
     * Get all of the users that belong to the team.
     */
    public function users()
    {
        return $this->belongsToMany(\Spark::userModel(), 'merchant_users', 'merchant_id', 'user_id')
            ->withPivot('role', 'invited_by_name', 'invited_by_email')
            ->withTimestamps();
    }

    /**
     * Get all of the team's invitations.
     */
    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'team_id', 'id');
    }

    public function detail()
    {
        return $this->hasOne(MerchantDetail::class, 'merchant_id');
    }



    /**
     * Get all of the subscriptions for the team.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    /*public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'merchant_id')->orderBy('created_at', 'desc');
    }*/

    public function tags()
    {
        return $this->hasMany(Tag::class, 'merchant_id');
    }

    public function rewards()
    {
        return $this->hasMany(MerchantReward::class, 'merchant_id');
    }

    public function merchant_currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    public function owner()
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'merchant_id');
    }

    public function integrations()
    {
        return $this->belongsToMany(Integration::class, 'merchant_integrations', 'merchant_id', 'integration_id')
            ->withPivot('status', 'settings')
            ->withTimestamps();
    }

    public function integrationsWithToken()
    {
        return $this->belongsToMany(Integration::class, 'merchant_integrations', 'merchant_id', 'integration_id')
            ->withPivot('status', 'settings', 'external_id', 'token', 'refresh_token', 'expires_at', 'api_endpoint')
            ->withTimestamps();
    }

    public function points_settings()
    {
        return $this->hasOne(PointSetting::class, 'merchant_id');
    }

    public function points()
    {
        return $this->hasMany(Point::class, 'merchant_id');
    }

    public function email_notification_settings()
    {
        return $this->hasOne(MerchantEmailNotificationSettings::class, 'merchant_id');
    }

    public function merchant_actions()
    {
        return $this->hasMany(MerchantAction::class, 'merchant_id');
    }

    public function referrals_settings()
    {
        return $this->hasOne(ReferralSetting::class, 'merchant_id');
    }

    public function reward_settings()
    {
        return $this->hasOne(RewardSetting::class, 'merchant_id');
    }

    public function rewards_branding_page()
    {
        return $this->hasOne(RewardsPageBranding::class, 'merchant_id');
    }

    public function tiers()
    {
        return $this->hasMany(Tier::class, 'merchant_id');
    }

    public function plan_subscription()
    {
        return $this->hasOne(Subscription::class, 'merchant_id');
    }

    public function plan()
    {
        $subscription = $this->plan_subscription;

        if (empty($subscription) || (! $subscription->isActive() && ! $subscription->isTrial() && ! is_null($subscription->status))) {
            return new Plan();

        } else {
            return $subscription->plan;
        }
    }

    public function orders()
    {
        return $this->hasManyThrough('App\Models\Order', 'App\Models\Customer', 'merchant_id', 'customer_id', 'id', 'id');
    }

    public function referred_orders()
    {
        return $this->hasManyThrough('App\Models\Order', 'App\Models\Customer', 'merchant_id', 'referring_customer_id', 'id', 'id');
    }

    public function checkPermitionByTypeCode(string $type_code)
    {
        // Get current subscription plan
        $currentPlan = $this->plan();

        // Check if a merchant has active subscription
        if ($currentPlan && ($currentPlan->status == 'active' || is_null($currentPlan->status))) {
            // Check if current subscription plan has paid permissions
            return $currentPlan->paid_permissions->where('type_code', '=', $type_code)->first() !== null;
        } else {
            return false;
        }
    }

    public function shares(){
        return $this->hasManyThrough('App\Models\CustomerReferralShare', Customer::class);
    }

    public function clicks(){
        return $this->hasManyThrough('App\Models\CustomerReferralClick', Customer::class);
    }

    public function tier_history(){
        return $this->hasManyThrough('App\Models\TierHistory', Customer::class);
    }
}
