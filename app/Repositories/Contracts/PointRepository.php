<?php

namespace App\Repositories\Contracts;

interface PointRepository
{
    public function rollbackPoints($pointId, array $properties);
}