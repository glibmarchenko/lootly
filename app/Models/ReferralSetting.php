<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/20/18
 * Time: 2:06 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralSetting extends Model
{
    protected $table = 'referral_settings';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','merchant_id', 'referral_domain', 'referral_link', 'program_status', 'referral_domain_status',
    ];


}