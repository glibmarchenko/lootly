<?php

namespace App\Repositories;


use App\Models\Tag;
use App\Contracts\Repositories\TagRepository as TagRepositoryContract;


class TagRepository implements TagRepositoryContract
{
    public $baseQuery;

    public function __construct()
    {
        $this->baseQuery = Tag::query();
    }

    public function all($merchantObj)
    {
        $tags = $this->baseQuery
            ->where('tags.merchant_id', '=', $merchantObj->id)
            ->get();

        return $tags;
    }
}
