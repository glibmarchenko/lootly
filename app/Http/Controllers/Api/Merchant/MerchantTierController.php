<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Merchant\TierStoreRequest;
use App\Merchant;
use App\Models\Tier;
use App\Repositories\Contracts\TagRepository;
use App\Repositories\Contracts\TierBenefitRepository;
use App\Repositories\Contracts\TierRepository;
use App\Repositories\Contracts\TierRestrictionRepository;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\ByTier;
use App\Services\Amazon\UploadFile;
use App\Transformers\TierRestrictionTransformer;
use App\Transformers\TierTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MerchantTierController extends Controller
{
    protected $tiers;

    protected $tags;

    protected $tierRestrictions;

    protected $tierBenefits;

    public function __construct(
        TierRepository $tiers,
        TagRepository $tags,
        TierRestrictionRepository $tierRestrictions,
        TierBenefitRepository $tierBenefits
    ) {
        $this->tiers = $tiers;
        $this->tags = $tags;
        $this->tierRestrictions = $tierRestrictions;
        $this->tierBenefits = $tierBenefits;
    }

    public function get(Request $request, Merchant $merchant)
    {
        $merchantTiers = $this->tiers->withCriteria([
            new ByMerchant($merchant->id),
        ])->all();

        return fractal($merchantTiers)->transformWith(new TierTransformer)->toArray();
    }

    public function store(TierStoreRequest $request, Merchant $merchant, Tier $tier = null)
    {
        $data = $request->all();

        if ($tier) {
            if ($tier->merchant_id !== $merchant->id) {
                return response()->json(['errors' => ['Unauthorized action']], 403);
            }
        }

        $data['merchant_id'] = $merchant->id;

        $preparedData = [];
        $preparedData['merchant_id'] = $merchant->id;

        // @todo: better validation for uploaded image and remove old image
        $amazon = new UploadFile();
        if ($data['iconPreview'] != $data['program']['reward_icon']) {
            $file = $data['iconPreview'];            
            $preparedData['image_url'] = '';            
            $icon_url = $amazon->upload($merchant, $file, $id = null);
            if (isset($icon_url) && $icon_url) {
                $preparedData['image_url'] = $icon_url;
            }
        }

        $preparedData['name'] = $data['program']['name'];
        $preparedData['text_email'] = $data['program']['emailText'];
        $preparedData['text_email_default'] = $data['program']['emailDefaultText'];
        $preparedData['status'] = $data['program']['status'];
        $preparedData['spend_value'] = $data['spend']['value'];
        $preparedData['requirement_text'] = $data['spend']['text'];
        $preparedData['requirement_text_default'] = $data['spend']['defaultText'];
        $preparedData['multiplier_text'] = $data['points']['text'];
        $preparedData['multiplier_text_default'] = $data['points']['defaultText'];
        $preparedData['multiplier'] = $data['points']['value'];
        $preparedData['email_notification'] = $data['emailNotification'];
        $preparedData['rolling_days'] = isset($data['rolling_days']) ? $data['rolling_days'] : null;
        $preparedData['currency'] = $data['currency'];
        $preparedData['image_name'] = $data['program']['icon_name'];
        $preparedData['default_icon_color'] = $data['program']['defaultIconColor'];
        $preparedData['restrictions_enabled'] = isset($data['restrictions']['status']) && $data['restrictions']['status'] ? 1 : 0;

        // Update/Create Tier
        if ($tier) {
            $this->tiers->update($tier->id, $preparedData);
            $this->tiers->clearEntity();
            $merchantTier = $this->tiers->find($tier->id);
        } else {
            $merchantTier = $this->tiers->create($preparedData);
        }

        // Update/Create Tier Benefits
        try {
            $this->tierBenefits->withCriteria([
                new ByTier($merchantTier->id),
            ])->delete();
            $this->tierBenefits->clearEntity();
            $this->tierBenefits->createMany($merchantTier->id, $data['benefits']);
        } catch (\Exception $exception) {
            Log::error('Tier Benefits Error: '.$exception->getMessage());
        }

        $customerRestrictions = [
            'merchant_id'  => $merchant->id,
            'tier_id'      => $merchantTier->id,
            'type'         => 'customer',
            'restrictions' => [],
        ];

        // Validating customer restrictions
        if (isset($data['restrictions']['customer']) && count($data['restrictions']['customer'])) {
            $customer_restrictions = [];

            // Get Merchant Tags list
            $merchantTags = $this->tags->withCriteria([
                new ByMerchant($merchant->id),
            ])->all();
            $this->tags->clearEntity();
            foreach ($data['restrictions']['customer'] as $restriction) {
                if (! in_array(strtolower($restriction['conditional']), [
                    'has-any-of',
                    'is',
                ])) {
                    continue;
                }
                if ($restriction['type'] == 'customer-tags') {
                    $values = [];
                    // Check for new tags and create
                    for ($j = 0; $j < count($restriction['values']); $j++) {
                        $exists = false;
                        for ($i = 0; $i < count($merchantTags); $i++) {
                            if (strtolower($merchantTags[$i]->name) == strtolower($restriction['values'][$j])) {
                                $values[] = $merchantTags[$i]->id;
                                $exists = true;
                                break;
                            }
                        }
                        if (! $exists) {
                            // create new tag
                            $newMerchantTag = $this->tags->create([
                                'merchant_id' => $merchant->id,
                                'name'        => $restriction['values'][$j],
                            ]);
                            if ($newMerchantTag) {
                                $values[] = $newMerchantTag->id;
                            }
                            $this->tags->clearEntity();
                        }
                    }
                    $customer_restrictions[] = [
                        'type'      => 'customer-tags',
                        'condition' => strtolower($restriction['conditional']),
                        'value'     => $values,
                    ];
                }
            }
            $customerRestrictions['restrictions'] = $customer_restrictions;
        }

        $this->tierRestrictions->updateOrCreate([
            'merchant_id' => $merchant->id,
            'tier_id'     => $merchantTier->id,
            'type'        => 'customer',
        ], $customerRestrictions);
        $this->tierRestrictions->clearEntity();

        return fractal($merchantTier)->transformWith(new TierTransformer())->toArray();
    }

    public function getTierRestrictions(Request $request, Merchant $merchant, $tierId)
    {
        $restrictions = $this->tierRestrictions->withCriteria([
            new ByMerchant($merchant->id),
        ])->findWhere(['tier_id' => $tierId]);

        return fractal($restrictions)->transformWith(new TierRestrictionTransformer())->toArray();
    }
}