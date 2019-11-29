<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TierSettings extends Model
{
    const PROGRAM_STATUS_ENABLED = 'Enabled';
    const PROGRAM_STATUS_DISABLED = 'Disabled';

    protected $table = 'tier_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'merchant_id', 'program_status', 'requirement_type', 'rolling_period'
    ];

    public function tier()
    {
        return $this->hasOne('App\Models\Tier');
    }

    public function isProgramStatus()
    {
        return strtolower($this->program_status) === strtolower(self::PROGRAM_STATUS_ENABLED);
    }

    public function getRollingPeriodType()
    {
        $rollingPeriod = $this->rolling_period;

        if (trim($rollingPeriod)) {
            $rollingPeriodExplode = explode('-', $rollingPeriod);

            if (count($rollingPeriodExplode) > 1) {
                $rollingPeriodNum = intval($rollingPeriodExplode[0]);

                if ($rollingPeriodNum) {
                    $rollingPeriodType = trim($rollingPeriodExplode[1]);

                    if ($rollingPeriodType) {
                        return strtolower($rollingPeriodType);
                    }
                }
            }
        }

        return null;
    }

    public function getRollingPeriodNumber()
    {
        $rollingPeriod = $this->rolling_period;

        if (trim($rollingPeriod)) {
            $rollingPeriodExplode = explode('-', $rollingPeriod);

            if (count($rollingPeriodExplode) > 1) {
                $rollingPeriodNumber = intval($rollingPeriodExplode[0]);

                if ($rollingPeriodNumber) {
                    return $rollingPeriodNumber;
                }
            }
        }

        return null;
    }
}
