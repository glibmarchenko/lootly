<?php

namespace App\SparkExtensions;

use Laravel\Spark\CanJoinTeams as SparkCanJoinTeams;

trait CanJoinTeams
{
    use SparkCanJoinTeams;
    /**
     * Get all of the teams that the user belongs to.
     */
    public function teams()
    {
        return $this->belongsToMany(
            \Spark::teamModel(), 'merchant_users', 'user_id', 'merchant_id'
        )->withPivot(['role'])->orderBy('name', 'asc');
    }
}
