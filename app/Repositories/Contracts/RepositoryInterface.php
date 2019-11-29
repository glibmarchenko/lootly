<?php

namespace App\Repositories\Contracts;



interface RepositoryInterface
{
    public function all();

    public function find($id);

    public function findWhere(array $conditions);

    public function findWhereFirst(array $conditions);

    public function paginate($perPage = 10);

    public function create(array $properties);

    public function update($id, array $properties);

    public function delete($id = null);
}