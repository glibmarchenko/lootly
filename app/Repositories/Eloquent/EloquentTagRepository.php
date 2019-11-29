<?php

namespace App\Repositories\Eloquent;

use App\Models\Tag;
use App\Repositories\Contracts\TagRepository;
use App\Repositories\RepositoryAbstract;

class EloquentTagRepository extends RepositoryAbstract implements TagRepository
{
    public function entity()
    {
        return Tag::class;
    }
}