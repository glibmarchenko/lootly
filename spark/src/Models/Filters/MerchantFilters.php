<?php

namespace Laravel\Spark\Models\Filters;

use App\Filters\QueryFilters;

class MerchantFilters extends QueryFilters
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
     * Filter by name.
     *
     * @param  string $value
     * @return Builder
     */
    public function name($value)
    {
        return $this->builder->where('name', 'LIKE', "%{$value}%");
    }

    /**
     * Filter by multiple fields.
     *
     * @param  string $value
     * @return Builder
     */
    public function search($value)
    {
        return $this->builder->with(['owner', 'integrations', 'plan_subscription'])->where(function ($query) use ($value) {
            $query->where('name', 'LIKE', "%{$value}%")
                ->orWhereHas('owner', function ($q) use ($value) {
                    $q->where('email', 'LIKE', "%{$value}%");
                })->orWhereHas('integrations', function ($q) use ($value) {
                    $q->where('title', 'LIKE', "%{$value}%");
                })->orWhereHas('plan_subscription', function ($q) use ($value) {
                    $q->where('name', 'LIKE', "%{$value}%");
                });
        });
    }
}
