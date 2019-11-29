<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceCaseStudies extends Model
{
    const IMAGE_MAX_WIDTH = 600;
    const IMAGE_MAX_HEIGHT = 600;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'resource_id',

        'industry',
        'platform',
        'favorite_feature',

        'stat_first_title',
        'stat_first_value',

        'stat_second_title',
        'stat_second_value',

        'stat_third_title',
        'stat_third_value',

        'top_quote',

        'customer_name',
        'position_title',

        'company_body',
        'company_image',

        'challenge_body',
        'challenge_quote',

        'solution_body',
        'solution_quote',
        'solution_image',

        'results_body',
        'results_image',
    ];
}
