<?php

namespace Laravel\Spark\Repositories;

use App\Models\Author;
use Laravel\Spark\Contracts\Repositories\AuthorRepository as AuthorRepositoryContract;

class AuthorRepository implements AuthorRepositoryContract
{
    public function all()
    {
        return Author::all();
    }

    public function get()
    {
        return Author::query();
    }

    public function find($id)
    {
        return Author::find($id);
    }

    public function findOrFail($id)
    {
        return Author::findOrFail($id);
    }

    public function create(array $attributes = [])
    {
        return Author::create($attributes);
    }
}
