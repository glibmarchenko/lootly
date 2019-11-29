<?php

namespace App\Http\Controllers\Settings\Point\Earning;

use App\Http\Controllers\Controller;
use App\Repositories\ActionRepository;
use App\Repositories\MerchantActionRepository;
use App\Repositories\MerchantRepository;
use App\Models\PaidPermission;


class EarningPointsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MerchantActionRepository $merchantActionRepository, MerchantRepository $merchantRepository,
                                ActionRepository $actionRepository)
    {
        $this->merchantActionRepository = $merchantActionRepository;
        $this->merchantRepository = $merchantRepository;
        $this->actionRepository = $actionRepository;
        $this->middleware('auth');
    }


    public function getMerchantAction()
    {
        $merchant = $this->merchantRepository->getCurrent();
        $earningActions = $this->merchantActionRepository->get($merchant);
        $points_settings = $merchant->points_settings;
        $readContentUpsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.ReadContent'));
        $trustSpotUpsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.TrustSpotReview'));

        return view('points.earning.index', compact(
            'earningActions',
            'points_settings',
            'merchant',
            'readContentUpsell',
            'trustSpotUpsell'
        ));
    }

    public function getDefaultActions()
    {
        $actions = $this->actionRepository->get();
        $actionTypes = $this->actionRepository->getType();
        $merchant = $this->merchantRepository->getCurrent();
        $addStoreType = false;
        if(!$merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.ReadContent'))){
            $readContentAction = $this->actionRepository->findByUrl('read-content');
            if($actions->filter(function($value){ return $value->url == 'read-content'; })->count() === 0) {
                $actions->push($readContentAction);
                $addStoreType = true;
            }
        }
        if(!$merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.TrustSpotReview'))){
            $trustSpotAction = $this->actionRepository->findByUrl('trustspot-review');
            if($actions->filter(function($value){ return $value->url == 'trustspot-review'; })->count() === 0) {
                $actions->push($trustSpotAction);
                $addStoreType = true;
            }
        }

        if ($merchant->integrations->filter(function ($integration) { return $integration->slug === 'zapier'; })) {
            if($actions->filter(function($value){ return $value->url == 'custom-earning'; })->count() === 0) {
                $customAction = $this->actionRepository->findByUrl('custom-earning');
                $actionTypes->push($customAction);
                $actions->push($customAction);
            }
        }

        if($addStoreType && !$actionTypes->filter(function($value){ return $value->url == 'read-content'; })){
            $actionTypes->push($this->actionRepository->findByUrl('read-content'));
        }

        $readContentUpsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.ReadContent'));
        $trustSpotUpsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.TrustSpotReview'));

        return view('points.earning.actions.index', compact('actions', 'actionTypes', 'merchant', 'readContentUpsell', 'trustSpotUpsell'));
    }

}
