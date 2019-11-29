<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TierHistory extends Model
{
    const ACTIVITY_NEW_MEMBER = 'New VIP Member';
    const ACTIVITY_UPGRADED = 'Upgraded Tier';
    const ACTIVITY_DOWNGRADED = 'Downgraded Tier';
    const ACTIVITY_UPDATE = 'Update Tier';
    const ACTIVITY_ADMIN_UPGRADED = 'Admin Upgrade';
    const ACTIVITY_ADMIN_DOWNGRADED = 'Admin Downgraded';
    const ACTIVITY_USER_CHANGES = 'Manual changes';

    protected $table = 'tier_history';

    protected $fillable = [
        'new_tier_id',
        'old_tier_id',
        'customer_id',
        'activity'
    ];

    protected $appends = ['created_at_with_tz', 'joined_human_date'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function new_tier()
    {
        return $this->belongsTo(Tier::class, 'new_tier_id');
    }

    public function old_tier()
    {
        return $this->belongsTo(Tier::class, 'old_tier_id');
    }

    public function getCreatedAtWithTzAttribute()
    {
        return $this->created_at->format('Y-m-d\TH:i:sP');
    }

    public function getJoinedHumanDateAttribute()
    {
        return $this->created_at->format('F d, Y');
    }

    public function isActivityUserChanges()
    {
        return $this->activity === self::ACTIVITY_USER_CHANGES;
    }
}
