<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Filters\Filterable;
use Illuminate\Support\Str;
use App\Models\ResourceCaseStudies;

class Resource extends Model
{
    use Filterable;

    const PATH_IMAGES = 'resources';

    const MINI_IMAGE_MAX_WIDTH = 400;
    const MINI_IMAGE_MAX_HEIGHT = 200;

    const FEATURED_IMAGE_MAX_WIDTH = 1000;
    const FEATURED_IMAGE_MAX_HEIGHT = 700;

    const STATUS_UNPUBLISHED = 0;
    const STATUS_PUBLISHED = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'author_id',
        'category_id',
        'title',
        'slug',
        'body',
        'description',
        'meta_description',
        'mini_image',
        'featured_image',
        'status',
    ];

    public static function getAllStatuses()
    {
        return [
            self::STATUS_UNPUBLISHED => __('Unpublished'),
            self::STATUS_PUBLISHED => __('Published'),
        ];
    }

    public function scopeSortDefault($query)
    {
        return $query->orderBy('id', 'desc');
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value, '-');
    }

    public function author()
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function caseStudy()
    {
        return $this->hasOne(ResourceCaseStudies::class, 'resource_id', 'id');
    }

    public function isPublished()
    {
        return $this->status == self::STATUS_PUBLISHED;
    }
}
