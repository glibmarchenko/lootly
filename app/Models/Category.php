<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    const SLUG_RESOURCES = 'resources';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'slug',
        'name',
    ];

    public function isCaseStudies()
    {
        return $this->slug === 'case-studies';
    }

    public function isEcommerce()
    {
        return $this->slug === 'ecommerce';
    }

    public function isNews()
    {
        return $this->slug === 'news';
    }

    public function isLoyaltyProgram()
    {
        return $this->slug === 'loyalty-program';
    }
}
