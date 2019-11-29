<?php

namespace App;

use App\Mail\MerchantForgotPassword;
use App\Models\Order;
use App\Models\Point;
use App\Models\Billing;
use App\Models\UserNotificationType;
use App\SparkExtensions\CanJoinTeams;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Spark\User as SparkUser;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends SparkUser
{
    use EntrustUserTrait, CanJoinTeams;

    const EMPLOYEE_ROLE = 'employee';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'billing_email',
        'password',
        'last_read_announcements_at',
        'trial_ends_at',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'authy_id',
        'country_code',
        'phone',
        'two_factor_reset_code',
        'card_brand',
        'card_last_four',
        'card_country',
        'billing_address',
        'billing_address_line_2',
        'billing_city',
        'billing_zip',
        'billing_country',
        'extra_billing_information',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'trial_ends_at'        => 'datetime',
        'uses_two_factor_auth' => 'boolean',
    ];

    protected $appends = [
        'name',
    ];

    public function getNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public static function getAuthClient()
    {
        return Auth::user();
    }

    public function role()
    {
        return $this->belongsToMany('App\Models\Role', 'role_user', 'user_id', 'role_id');
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'user_id', 'id')->withDefault();
    }

    public function points()
    {
        return $this->hasMany(Point::class, 'user_id', 'id');
    }

    public function merchant()
    {
        return $this->belongsToMany(Merchant::class, 'merchant_users');
    }

    public function planModel() {
        return $this->plan_subscription->plan;
    }

    public function billings() {
        return $this->hasMany(Billing::class, 'user_id');
    }

    public function notification_types()
    {
        return $this->belongsToMany(UserNotificationType::class, 'user_notifications', 'user_id', 'user_notification_type_id')
            ->withPivot('active')
            ->withTimestamps();
    }

    public function sendPasswordResetNotification($token)
    {
        $user = User::find($this->id);
        Mail::to($this->email)->queue(new MerchantForgotPassword($user, $token));

        //$this->notify(new PasswordReset($token));
    }
}
