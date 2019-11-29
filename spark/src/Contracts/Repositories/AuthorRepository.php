<?php

namespace Laravel\Spark\Contracts\Repositories;

interface AuthorRepository
{
    public function all();

    public function get();

    public function find($id);

    public function findOrFail($id);

    public function create(array $attributes = []);
}
