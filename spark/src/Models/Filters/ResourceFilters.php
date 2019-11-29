<?php

namespace Laravel\Spark\Models\Filters;

use App\Filters\QueryFilters;

class ResourceFilters extends QueryFilters
{
    /**
     * Filter by id.
     *
     * @param  string $value
     * @return Builder
     */
    public function id($value)
    {
        return $this->builder->where('id', $value);
    }

    /**
     * Filter by title.
     *
     * @param  string $value
     * @return Builder
     */
    public function title($value)
    {
        return $this->builder->where('title', 'LIKE', "%{$value}%");
    }

    /**
     * Filter by multiple fields.
     *
     * @param  string $value
     * @return Builder
     */
    public function search($value)
    {
        return $this->builder->where(function($query) use ($value) {
            $query->where('title', 'LIKE', "%{$value}%")
                ->orWhere('body', 'LIKE', "%{$value}%");
        });
    }
}
