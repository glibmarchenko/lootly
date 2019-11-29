<?php

namespace App\SparkExtensions;

use Laravel\Spark\TeamSubscription as SparkTeamSubscription;

class TeamSubscription extends SparkTeamSubscription
{
    protected $table = 'merchant_subscriptions';
}
