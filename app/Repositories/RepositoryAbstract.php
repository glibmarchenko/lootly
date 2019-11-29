<?php

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Criteria\CriteriaInterface;
use App\Repositories\Exceptions\NoEntityDefined;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class RepositoryAbstract implements RepositoryInterface, CriteriaInterface
{
    protected $entity;

    public function __construct()
    {
        $this->clearEntity();
    }

    protected function resolveEntity()
    {
        if (! method_exists($this, 'entity')) {
            throw new NoEntityDefined();
        }

        return app()->make($this->entity());
    }

    public function clearEntity()
    {
        $this->entity = $this->resolveEntity();
    }

    public function withCriteria(...$criteria)
    {
        $criteria = array_flatten($criteria);

        foreach ($criteria as $criterion) {
            if ($criterion) {
                $this->entity = $criterion->apply($this->entity);
            }
        }

        return $this;
    }

    public function all()
    {
        // dd($this->entity->toSql());
        return $this->entity->get();
    }

    public function count()
    {
        return $this->entity->count();
    }

    public function find($id)
    {
        $model = $this->entity->find($id);

        if (! $model) {
            throw (new ModelNotFoundException)->setModel(get_class($this->entity->getModel()), $id);
        }

        return $model;
    }

    public function findWhere(array $conditions)
    {
        return $this->entity->where($conditions)->get();
    }

    public function findWhereFirst(array $conditions)
    {
        $model = $this->entity->where($conditions)->first();

        if (! $model) {
            throw (new ModelNotFoundException)->setModel(get_class($this->entity->getModel()));
        }

        return $model;
    }

    public function paginate($perPage = 10)
    {
        return $this->entity->paginate($perPage);
    }

    public function create(array $properties)
    {
        return $this->entity->create($properties);
    }

    public function update($id, array $properties)
    {
        return $this->find($id)->update($properties);
    }

    public function delete($id = null)
    {
        if (! is_null($id)) {
            return $this->find($id)->delete();
        } else {
            return $this->entity->delete();
        }
    }

    public function toSql()
    {
        return $this->entity->toSql();
    }

    public function findWhereFirstToSql(array $conditions)
    {
        $model = $this->entity->where($conditions)->toSql();

        if (! $model) {
            throw (new ModelNotFoundException)->setModel(get_class($this->entity->getModel()));
        }

        return $model;
    }
}