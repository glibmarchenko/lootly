<?php

namespace App\Contracts\Repositories;

use App\Models\Integration;

interface IntegrationRepository
{
    public function all();

    public function get();

    public function getSlugList();

    public function find($id);

    public function findBySlug($slug);

    public function findActiveBySlug($slug);

    public function getMerchantsWithActiveIntegration(Integration $integration, $externalId = null);
}
