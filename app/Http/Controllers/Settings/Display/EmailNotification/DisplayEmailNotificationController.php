<?php

namespace App\Http\Controllers\Settings\Display\EmailNotification;

use Illuminate\Http\Request;
use App\Models\PaidPermission;
use App\Http\Controllers\Controller;
use App\Repositories\MerchantRepository;

class DisplayEmailNotificationController extends Controller
{
    public function __construct(){

        $this->merchantRepo = new MerchantRepository();
    }

    public function pointsEarned(Request $request){
        return view('display.email.points.points-earned', $this->getDataForViews());
    }

    public function pointsSpent(Request $request){
        return view('display.email.points.points-spent', $this->getDataForViews());
    }

    public function rewardAviable(Request $request){
        return view('display.email.points.reward-available', $this->getDataForViews());
    }

    public function pointExiration(Request $request){
        return view('display.email.points.point-expiration', $this->getDataForViews());
    }

    public function vipTierEarned(Request $request){
        return view('display.email.points.vip-tier-earned', $this->getDataForViews());
    }

    public function shareEmail(Request $request){
        return view('display.email.referral.share-email', $this->getDataForViews());
    }

    public function receiverReward(Request $request){
        return view('display.email.referral.receiver-reward', $this->getDataForViews());
    }

    public function senderReward(Request $request){
        return view('display.email.referral.sender-reward', $this->getDataForViews());
    }

    protected function getDataForViews(){
        $paidFeature = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.EmailCustomization'));
        $merchant = $this->merchantRepo->getCurrent();

        $has_editor_permissions = $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.HTML_Editor'));
        $editor_upsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.HTML_Editor'));
        return [
            'feature' => $paidFeature,
            'merchant' => $merchant,
            'has_editor_permissions' => $has_editor_permissions,
            'editor_upsell' => $editor_upsell,
        ];
    }
}
