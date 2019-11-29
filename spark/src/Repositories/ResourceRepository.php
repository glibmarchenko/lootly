<?php

namespace Laravel\Spark\Repositories;

use App\Models\Resource as ResourceModel;
use Laravel\Spark\Contracts\Repositories\ResourceRepository as ResourceRepositoryContract;

class ResourceRepository implements ResourceRepositoryContract
{
    public function all()
    {
        return ResourceModel::all();
    }

    public function get()
    {
        return ResourceModel::query();
    }

    public function find($id)
    {
        return ResourceModel::find($id);
    }

    public function findOrFail($id)
    {
        return ResourceModel::findOrFail($id);
    }

    public function create(array $attributes = [])
    {
        return ResourceModel::create($attributes);
    }

    public function makeImagePath()
    {
        return ResourceModel::PATH_IMAGES . date('/Y/m/d');
    }

    public function update(ResourceModel $resource, array $attributes = [], array $options = [])
    {
        return $resource->update($attributes, $options);
    }
}
