<?php

namespace App\Repositories\Eloquent;

use App\Models\ReferralSharing;
use App\Repositories\Contracts\ReferralSharingRepository;
use App\Repositories\RepositoryAbstract;

class EloquentReferralSharingRepository extends RepositoryAbstract implements ReferralSharingRepository
{
    public function entity()
    {
        return ReferralSharing::class;
    }

    public function updateOrCreate(array $conditions, array $data)
    {
        return $this->entity->updateOrCreate($conditions, $data);
    }
}