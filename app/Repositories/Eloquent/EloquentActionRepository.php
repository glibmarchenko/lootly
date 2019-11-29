<?php

namespace App\Repositories\Eloquent;

use App\Models\Action;
use App\Repositories\Contracts\ActionRepository;
use App\Repositories\RepositoryAbstract;

class EloquentActionRepository extends RepositoryAbstract implements ActionRepository
{
    public function entity()
    {
        return Action::class;
    }

    public function findBySlug($slug)
    {
        return $this->entity->where(['url' => $slug])->first();
    }
}