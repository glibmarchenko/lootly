<?php

namespace App\Repositories\Eloquent;

use App\Models\MerchantDetail;
use App\Repositories\Contracts\MerchantDetailsRepository;
use App\Repositories\RepositoryAbstract;

class EloquentMerchantDetailsRepository extends RepositoryAbstract implements MerchantDetailsRepository
{

    public function entity()
    {
        return MerchantDetail::class;
    }

    public function updateOrCreate(array $conditions, array $data)
    {
        return $this->entity->updateOrCreate($conditions, $data);
    }

}