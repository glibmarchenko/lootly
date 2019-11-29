<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Merchant\Earning\ActionStoreRequest;
use App\Merchant;
use App\Models\MerchantAction;
use App\Repositories\Contracts\ActionRepository;
use App\Repositories\Contracts\MerchantActionRepository;
use App\Repositories\Contracts\MerchantActionRestrictionRepository;
use App\Repositories\Contracts\TagRepository;
use App\Repositories\Contracts\TierRepository;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Services\Amazon\UploadFile;
use App\Transformers\MerchantActionRestrictionTransformer;
use App\Transformers\MerchantActionTransformer;
use App\Transformers\TierTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MerchantActionController extends Controller
{
    protected $merchantActionRestrictions;

    public function __construct(MerchantActionRestrictionRepository $merchantActionRestrictions)
    {
        $this->merchantActionRestrictions = $merchantActionRestrictions;
    }

    public function getActionRestrictions(Request $request, Merchant $merchant, $actionId)
    {
        $restrictions = $this->merchantActionRestrictions->withCriteria([
            new ByMerchant($merchant->id),
        ])->findWhere(['merchant_action_id' => $actionId]);

        return fractal($restrictions)->transformWith(new MerchantActionRestrictionTransformer())->toArray();
    }
}