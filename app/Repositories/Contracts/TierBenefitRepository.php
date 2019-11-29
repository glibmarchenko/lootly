<?php

namespace App\Repositories\Contracts;

interface TierBenefitRepository
{

    public function createMany($tierId, array $benefits);

}