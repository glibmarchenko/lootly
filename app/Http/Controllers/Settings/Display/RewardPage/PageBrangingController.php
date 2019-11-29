<?php

namespace App\Http\Controllers\Settings\Display\RewardPage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RewardsPageBranding;
use App\Models\PaidPermission;
use App\Repositories\MerchantRepository;
use App\Repositories\HeaderSettingsRepository;
use App\Http\Requests\Settings\Display\RewardPage\SavePageBrangingRequest;

class PageBrangingController extends Controller
{
    public function __construct(
        MerchantRepository $merchantRepository,
        HeaderSettingsRepository $headerSettingsRepository
    ) {
        $this->merchantRepo = $merchantRepository;
        $this->headerSettingsRepository = $headerSettingsRepository;
    }

    public function get(){
        $merchant = $this->merchantRepo->getCurrent();
        $branding = $merchant->rewards_branding_page;
        if(!isset($branding)){
            $branding = new RewardsPageBranding();
        }
        $has_remove_branding_permissions = $merchant
            ->checkPermitionByTypeCode(\Config::get('permissions.typecode.RemoveLootlyBranding'));
        $branding_upsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.RemoveLootlyBranding'));

        return view('display.reward-page.branding', [
            'rewards_branding' =>  $branding,
            'header' => $this->headerSettingsRepository->getCurrent($merchant),
            'has_remove_branding_permissions' => $has_remove_branding_permissions,
            'branding_upsell' => $branding_upsell,
        ]);
    }

    public function store(SavePageBrangingRequest $request){
        //TODO: validation
        $data = $request->all();
        
        $rewardBrandingModel = $this->merchantRepo->getCurrent()->rewards_branding_page;
        if(empty($rewardBrandingModel)){
            $rewardBrandingModel = new RewardsPageBranding();
            $rewardBrandingModel->merchant_id = $this->merchantRepo->getCurrent()->id;
        }

        $rewardBrandingModel->font = $data['font'];
        $rewardBrandingModel->remove_branding = boolval($data['widgetBranding']);
        try {
            $rewardBrandingModel->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Reward Page branding saved!',
                'id' => $rewardBrandingModel->id,
            ], 200);
        } catch(\Exeption $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}