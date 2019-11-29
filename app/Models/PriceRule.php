<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PriceRule extends Model
{
    protected $table = 'price_rule';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rule_id', 'title', 'value_type', 'value', 'usage_limit'
    ];
}
