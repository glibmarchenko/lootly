<?php

namespace App\Repositories;


use App\Models\Plan;
use App\Models\PaidPermission;
use App\Repositories\MerchantRepository;
class PlansRepository
{
    public $baseQuery;

    public function __construct()
    {
        $this->baseQuery = Plan::query();
        $merchantRepository = new MerchantRepository();
        $this->merchantRepository = $merchantRepository;
    }

    public function checkPermissions($type_code, $merchant = null){
        if(PaidPermission::checkTypeCode($type_code) === null){
            throw new Exception("Type code none found", 404);
            return false;
        }

        if(!$merchant){
            $merchant = $this->merchantRepository->getCurrent();
        }

        if($merchant->checkPermitionByTypeCode($type_code)){
            return true;
        } else {
            return false;
        }
    }

    public function findOrFail($id)
    {
        return $this->baseQuery->findOrFail($id);
    }
}
