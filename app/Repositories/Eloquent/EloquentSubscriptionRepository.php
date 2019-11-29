<?php

namespace App\Repositories\Eloquent;

use App\Models\Subscription;
use App\Repositories\Contracts\SubscriptionRepository;
use App\Repositories\RepositoryAbstract;

class EloquentSubscriptionRepository extends RepositoryAbstract implements SubscriptionRepository
{
    public function entity()
    {
        return Subscription::class;
    }

    public function updateOrCreate(array $conditions, array $data)
    {
        return $this->entity->updateOrCreate($conditions, $data);
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        return $this->entity->where($column, $operator, $value, $boolean);
    }
}
