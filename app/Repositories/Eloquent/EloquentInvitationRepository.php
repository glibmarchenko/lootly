<?php

namespace App\Repositories\Eloquent;

use App\Models\Invitation;
use App\Repositories\Contracts\InvitationRepository;
use App\Repositories\RepositoryAbstract;

class EloquentInvitationRepository extends RepositoryAbstract implements InvitationRepository
{
    public function entity()
    {
        return Invitation::class;
    }
}