<?php

namespace App\Http\Controllers\Api\Merchant\Earning;

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
use App\Repositories\PointRepository;
use App\Services\Amazon\UploadFile;
use App\Transformers\MerchantActionTransformer;
use App\Transformers\TierTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MerchantActionController extends Controller
{
    protected $actions;

    protected $merchantActions;

    protected $merchantActionRestrictions;

    protected $tags;

    protected $tiers;

    public function __construct(
        ActionRepository $actions,
        MerchantActionRepository $merchantActions,
        MerchantActionRestrictionRepository $merchantActionRestrictions,
        TagRepository $tags,
        TierRepository $tiers
    ) {
        $this->actions = $actions;
        $this->merchantActions = $merchantActions;
        $this->merchantActionRestrictions = $merchantActionRestrictions;
        $this->tags = $tags;
        $this->tiers = $tiers;
    }

    public function get(Request $request, Merchant $merchant)
    {
        $merchantTiers = $this->tiers->withCriteria([
            new ByMerchant($merchant->id),
        ])->all();

        return fractal($merchantTiers)->transformWith(new TierTransformer)->toArray();
    }


    public function store(ActionStoreRequest $request, Merchant $merchant, $action = null)
    {
        $data = $request->all();

        if (! $action) {
            $action = $this->actions->findBySlug(['name' => $data['action_slug']]);
        } else {
            $action = $this->actions->findBySlug(['name' => $action]);
        }

        $data['action_id'] = $action->id;
        $data['merchant_id'] = $merchant->id;

        $preparedData = [];

        // @todo: better validation for uploaded image and remove old image
        $amazon = new UploadFile();
        $file = $data['iconPreview'];
        if ($data['iconPreview']) {
            $icon_url = $amazon->upload($merchant, $file, $action->id);
            if (isset($icon_url) && $icon_url) {
                $preparedData['action_icon'] = $icon_url;
            }
        }

        $preparedData['action_name']             = $data['program']['name'];
        $preparedData['active_flag']             = $data['program']['status'];
        $preparedData['action_icon_name']        = $data['program']['icon_name'];
        $preparedData['reward_text']             = $data['program']['rewardText'];
        $preparedData['reward_default_text']     = $data['program']['rewardTextDefault'];
        $preparedData['reward_email_text']       = $data['program']['emailDefaultText'];
        $preparedData['point_value']             = $data['earning']['value'];
        $preparedData['fb_page_url']             = isset($data['facebook']['url']) ? $data['facebook']['url'] : null;
        $preparedData['share_message']           = isset($data['share']['message']) ? $data['share']['message'] : null;
        $preparedData['share_url']               = isset($data['share']['url']) ? $data['share']['url'] : null;
        $preparedData['twitter_username']        = isset($data['twitter']['username']) ? $data['twitter']['username'] : null;
        $preparedData['instagram_username']      = $data['instagram']['username'] ?? null;
        $preparedData['content_url']             = isset($data['content']['url']) ? $data['content']['url'] : null;
        $preparedData['review_status']           = isset($data['review']['status']) ? $data['review']['status'] : null;
        $preparedData['review_type']             = isset($data['review']['type']) ? $data['review']['type'] : null;
        $preparedData['goal']                    = isset($data['earning']['goal']) ? $data['earning']['goal'] : null;
        $preparedData['goal_unit']               = isset($data['earning']['goal_type']) && in_array($data['earning']['goal_type'],
            MerchantAction::GOAL_UNITS) ? $data['earning']['goal_type'] : null;
        $preparedData['is_fixed']                = isset($data['earning']['type']) ? $data['earning']['type'] : null;
        $preparedData['send_email_notification'] = $data['emailNotification'];
        $preparedData['restrictions_enabled']    = isset($data['restrictions']['status']) && $data['restrictions']['status'] ? 1 : 0;
        $preparedData['earning_limit']           = isset($data['earning']['limit']['status']) ? $data['earning']['limit']['status'] : null;
        $preparedData['earning_limit_value']     = isset($data['earning']['limit']['value']) ? $data['earning']['limit']['value'] : null;
        $preparedData['earning_limit_type']      = isset($data['earning']['limit']['type']) && in_array($data['earning']['limit']['type'],
            MerchantAction::EARNING_LIMIT_TYPES) ? $data['earning']['limit']['type'] : null;
        $preparedData['earning_limit_period']    = isset($data['earning']['limit']['period']) && in_array($data['earning']['limit']['period'],
            MerchantAction::EARNING_LIMIT_PERIODS) ? $data['earning']['limit']['period'] : null;

        switch ( $action->url ) {

            case 'custom-earning':
                if (!empty($data['edit']) && !empty($data['merchant_action_id'])) {
                    $this->merchantActions->update($data['merchant_action_id'], $preparedData);
                    $merchantAction = $this->merchantActions->find($data['merchant_action_id']);
                } else {
                    $merchantAction = $this->merchantActions->create(array_merge([
                        'action_id' => $action->id,
                        'merchant_id' => $merchant->id,
                    ], $preparedData));
                }
                break;

            case 'trustspot-review':

                // Edit action mode
                if (!empty($data['merchant_action_id'])) {
                    $this->merchantActions->update($data['merchant_action_id'], $preparedData);
                    $merchantAction = $this->merchantActions->find($data['merchant_action_id']);
                }

                // Add action mode
                else {
                    $merchantAction = $this->merchantActions->create(array_merge([
                        'action_id' => $action->id,
                        'merchant_id' => $merchant->id,
                    ], $preparedData));
                }
                break;

            default:
                $merchantAction = $this->merchantActions->updateOrCreate([
                    'merchant_id' => $merchant->id,
                    'action_id' => $action->id,
                ], $preparedData);
                break;
        }


        Validator::make($request->all(), [
            'zap' => 'nullable|string|max:191|unique:merchant_actions,zap_name,' . $merchantAction->id,
        ])->validate();

        $this->merchantActions->update($merchantAction->id, [
            'zap_name' => $request->input('zap', null)
        ]);



        $customerRestrictions = [
            'merchant_id'        => $merchant->id,
            'merchant_action_id' => $merchantAction->id,
            'type'               => 'customer',
            'restrictions'       => [],
        ];
        $productRestrictions = [
            'merchant_id'        => $merchant->id,
            'merchant_action_id' => $merchantAction->id,
            'type'               => 'product',
            'restrictions'       => [],
        ];
        $activityRestrictions = [
            'merchant_id'        => $merchant->id,
            'merchant_action_id' => $merchantAction->id,
            'type'               => 'activity',
            'restrictions'       => [],
        ];
        // Validating customer restrictions
        if (isset($data['restrictions']['customer']) && count($data['restrictions']['customer'])) {
            $customer_restrictions = [];

            // Get Merchant Tags list
            $merchantTags = $this->tags->withCriteria([
                new ByMerchant($merchant->id),
            ])->all();
            $this->tags->clearEntity();
            // Get Merchant Tiers list
            $merchantTiers = $this->tiers->withCriteria([
                new ByMerchant($merchant->id),
            ])->all();
            foreach ($data['restrictions']['customer'] as $restriction) {
                if (! in_array(strtolower($restriction['conditional']), [
                    'has',
                    'has-any-of',
                    'is',
                ])) {
                    continue;
                }
                $count_values = count( $restriction['values'] );
                if( strtolower($restriction['conditional']) == 'equals' ) {
                    $count_values = 1;
                }
                if ($restriction['type'] == 'customer-tags') {
                    $values = [];
                    // Check for new tags and create
                    for ($j = 0; $j < $count_values; $j++) {
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
                } else {
                    if ($restriction['type'] == 'vip-tier') {
                        $values = [];
                        // Check VIPs on existence and cleanup
                        for ($j = 0; $j < $count_values; $j++) {
                            for ($i = 0; $i < count($merchantTiers); $i++) {
                                if (strtolower($merchantTiers[$i]->name) == strtolower($restriction['values'][$j])) {
                                    $values[] = $merchantTiers[$i]->id;
                                    break;
                                }
                            }
                        }
                        $customer_restrictions[] = [
                            'type'      => 'vip-tier',
                            'condition' => strtolower($restriction['conditional']),
                            'value'     => $values,
                        ];
                    }
                }
            }
            $customerRestrictions['restrictions'] = $customer_restrictions;
        }

        // Validating product restrictions
        if (isset($data['restrictions']['product']) && count($data['restrictions']['product'])) {
            $product_restrictions = [];

            foreach ($data['restrictions']['product'] as $restriction) {
                if (! in_array(strtolower($restriction['conditional']), [
                    'has',
                    'has-any-of',
                    'is',
                ])) {
                    continue;
                }

                if (! in_array(strtolower($restriction['type']), [
                    'product-id',
                    'collection',
                ])) {
                    continue;
                }

                $values = $restriction['values'];

                if( $restriction['conditional'] == 'equals' ) {
                    $new_values = [];
                    $new_values[] = $values[0];
                    $values = $new_values;
                }

                $product_restrictions[] = [
                    'type'      => strtolower($restriction['type']),
                    'condition' => strtolower($restriction['conditional']),
                    'value'     => $values,
                ];
            }
            $productRestrictions['restrictions'] = $product_restrictions;
        }

        // Validating activity restrictions
        if (isset($data['restrictions']['activity']) && count($data['restrictions']['activity'])) {
            $activity_restrictions = [];

            foreach ($data['restrictions']['activity'] as $restriction) {
                if (! in_array(strtolower($restriction['conditional']), [
                    'has',
                    'has-any-of',
                    'is',
                ])) {
                    continue;
                }

                if (! in_array(strtolower($restriction['type']), [
                    'currency',
                    'total-discounts',
                ])) {
                    continue;
                }

                $values = $restriction['values'];

                if( $restriction['conditional'] == 'equals' ) {
                    $new_values = [];
                    $new_values[] = $values[0];
                    $values = $new_values;
                }

                $activity_restrictions[] = [
                    'type'      => strtolower($restriction['type']),
                    'condition' => strtolower($restriction['conditional']),
                    'value'     => $values,
                ];
            }
            $activityRestrictions['restrictions'] = $activity_restrictions;
        }
        $this->merchantActionRestrictions->updateOrCreate([
            'merchant_id'        => $merchant->id,
            'merchant_action_id' => $merchantAction->id,
            'type'               => 'customer',
        ], $customerRestrictions);
        $this->merchantActionRestrictions->clearEntity();
        $this->merchantActionRestrictions->updateOrCreate([
            'merchant_id'        => $merchant->id,
            'merchant_action_id' => $merchantAction->id,
            'type'               => 'product',
        ], $productRestrictions);
        $this->merchantActionRestrictions->clearEntity();
        $this->merchantActionRestrictions->updateOrCreate([
            'merchant_id'        => $merchant->id,
            'merchant_action_id' => $merchantAction->id,
            'type'               => 'activity',
        ], $activityRestrictions);
        $this->merchantActionRestrictions->clearEntity();

        return fractal($merchantAction)->transformWith(new MerchantActionTransformer)->toArray();
    }
}
