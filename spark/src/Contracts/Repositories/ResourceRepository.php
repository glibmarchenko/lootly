<?php

namespace Laravel\Spark\Contracts\Repositories;

use App\Models\Resource as ResourceModel;

interface ResourceRepository
{
    public function all();

    public function get();

    public function find($id);

    public function findOrFail($id);

    public function create(array $attributes = []);

    public function makeImagePath();

    public function update(ResourceModel $resource, array $attributes = [], array $options = []);
}
