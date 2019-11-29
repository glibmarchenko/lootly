<?php

namespace App\Repositories\Contracts;

use App\Models\RewardSetting;
use App\Merchant;

interface DisplaySettingsRepository
{
    public function getCurrent();

    public function create(array $properties, RewardSetting $rewardModel, Merchant $merchant);
}