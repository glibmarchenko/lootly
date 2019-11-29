<?php

namespace App\Repositories;

use App\Models\MerchantReward;
use App\Contracts\Repositories\MerchantRewardRepository as MerchantRewardRepositoryContract;
use App\Models\Reward;
use App\Models\RewardCoupon;
use App\Services\Amazon\UploadFile;
use App\Repositories\TierRepository;
use App\Repositories\MerchantRepository;
use Illuminate\Support\Facades\Log;

class MerchantRewardRepository implements MerchantRewardRepositoryContract
{
    use \App\Traits\CurrencyFormatTrait;

    protected $baseQuery;

    public function __construct()
    {
        $this->baseQuery = MerchantReward::query();
        $this->tierRepository = new TierRepository();
    }

    /**
     * @param string $code
     */
    public function get($merchantObj, $rewardTypeId = [])
    {
        if (! $merchantObj) {
            return null;
        }

        $action = $this->baseQuery->where('merchant_id', '=', $merchantObj->id)
            ->whereIn('type_id', $rewardTypeId)
            ->with('reward')
            ->orderBy('created_at', 'desc')
            ->get();

        return $action;
    }

    /**
     * @param string $code
     */
    public function all($merchant)
    {
        if (! $merchant) {
            return null;
        }

        $items = $this->baseQuery->where('merchant_id', '=', $merchant->id)
            ->with('reward')
            ->orderBy('created_at', 'desc')
            ->get();

        return $items;
    }

    public function getType($merchantObj)
    {
        if (! $merchantObj) {
            return null;
        }
        $action = $this->baseQuery->where('merchant_id', '=', $merchantObj->id)
            ->with('reward')
            ->orderBy('created_at', 'desc')
            ->pluck('reward_type');

        return $action;
    }

    /**
     * @param string $code
     */
    public function find()
    {
        // TODO: Implement find() method.
    }

    public function getByTypeId($typeId, $merchantId = null)
    {
        if($merchantId == null){
            $merchantRepo = new MerchantRepository();
            $merchantId = $merchantRepo->getCurrent()->id;
        }
        return MerchantReward::query()->where('type_id', '=', $typeId)->where('merchant_id', $merchantId)->first();
    }

    public function create($merchantObj, $rewardObj, array $data, $rewardTypeId = MerchantReward::REWARD_TYPE_POINT)
    {
        $rewardId = isset($data['reward_id']) ? $data['reward_id'] : null;
        if ($rewardId) {
            $merchant_reward = $this->baseQuery->where('merchant_id', '=', $merchantObj->id)->where(function ($query
            ) use (
                $rewardId,
                $data,
                $rewardTypeId
            ) {
                $query->where('id', '=', $rewardId);
                /*if ($rewardTypeId == MerchantReward::REWARD_TYPE_POINT) {
                    $query->orWhere('reward_name', '=', $data['program']['name']);
                }*/
            })->first();
        }

        if (! isset($merchant_reward) || ! $merchant_reward) {
            $merchant_reward = new MerchantReward();
            $merchant_reward->reward_id = $rewardObj->id;
            $merchant_reward->merchant_id = $merchantObj->id;
            $merchant_reward->type_id = $rewardTypeId;
            $merchant_reward->save();
        }

        $file = $data['iconPreview'];
        if ($file) {
            // Saving icon and deleting old from amazon
            $amazon = new UploadFile();
            try {
                $amazon->delete($this->getIconNameById($merchant_reward->id));
            } catch (\Exception $e) {
                dd($e);
            }
            $icone_url = $amazon->upload($merchantObj, $file, $rewardObj->id);
        }
        $merchant_reward->reward_icon = isset($icone_url) ? $icone_url : $data['program']['reward_icon'];

        $merchant_reward->reward_name       = $data['program']['name'];
        $merchant_reward->rewardDefaultName = $data['program']['nameDefault'];
        $merchant_reward->active_flag       = isset($data['program']['status']) ? $data['program']['status'] : null;
        $merchant_reward->reward_icon_name  = isset($data['program']['icon_name']) ? $data['program']['icon_name'] : null;
        $merchant_reward->product           = isset($data['reward']['product'], $data['reward']['product']['id']) ? $data['reward']['product']['id'] : null;
        $merchant_reward->product_title     = isset($data['reward']['product'], $data['reward']['product']['text']) ? $data['reward']['product']['text'] : null;
        $merchant_reward->max_shipping      = isset($data['reward']['maxShipping']) ? $data['reward']['maxShipping'] : null;
        $merchant_reward->points_required   = isset($data['reward']['points']) ? $data['reward']['points'] : (isset($data['reward']['variable']) ? $data['reward']['variable']['points'] : null);
        if ($data['program']['rewardType'] === Reward::TYPE_FREE_PRODUCT) {
            $merchant_reward->reward_value = isset($data['reward']['product'], $data['reward']['product']['price']) ? floatval($data['reward']['product']['price']) : null;
        } else {
            $merchant_reward->reward_value = isset($data['reward']['values']) ? $data['reward']['values'] : (isset($data['reward']['variable']) ? $data['reward']['variable']['values'] : null);
        }
        $merchant_reward->variable_point_min      = isset($data['reward']['minPoints']) ? $data['reward']['minPoints'] : null;
        $merchant_reward->variable_point_max      = (isset($data['reward']['maxPoints']) && $data['reward']['maxPoints'] != '') ? $data['reward']['maxPoints'] : null;
        $merchant_reward->coupon_prefix           = isset($data['coupon']['prefix']) ? $data['coupon']['prefix'] : null;
        $merchant_reward->order_minimum           = (isset($data['reward']['minOrder']) && ! empty($data['reward']['minOrder'])) ? $data['reward']['minOrder'] : null;
        $merchant_reward->send_email_notification = isset($data['emailNotification']) ? $data['emailNotification'] : null;
        $merchant_reward->reward_type             = $data['program']['rewardType'];
        $merchant_reward->reward_text             = $data['program']['rewardText'];
        $merchant_reward->rewardDefaultText       = $data['program']['rewardTextDefault'];
        $merchant_reward->reward_email_text       = $data['program']['emailTextDefault'];
        $merchant_reward->coupon_expiration       = $data['coupon']['status'];
        $merchant_reward->coupon_expiration_time  = $data['coupon']['limit']['value'].' '.$data['coupon']['limit']['duration'];
        $merchant_reward->zap_status              = $data['zapier']['status'] ?? 0;
        $merchant_reward->zap_key                 = $data['zapier']['name'] ?? '';
        $merchant_reward->restrictions_enabled    = isset($data['restrictions']['status']) && $data['restrictions']['status'] ? 1 : 0;
        $merchant_reward->spending_limit          = $data['spending']['limit']['status'] ?? false;
        $merchant_reward->spending_limit_type     = $data['spending']['limit']['type'] ?? null;
        $merchant_reward->spending_limit_value    = $data['spending']['limit']['value'] ?? null;
        $merchant_reward->spending_limit_period   = $data['spending']['limit']['period'] ?? null;
        $merchant_reward->save();
        $merchant_reward->fresh();

        return $merchant_reward;
    }

    public function getTopSpendingReward($merchantObj)
    {
        if (! $merchantObj) {
            return null;
        }
        $points = $this->baseQuery->leftJoin('points', 'merchant_rewards.id', '=', 'points.merchant_reward_id')
            ->select('merchant_rewards.reward_name', 'merchant_rewards.reward_icon_name', \DB::raw('COUNT(points.id) as actions_num'))
            ->groupBy('merchant_rewards.id')
            ->where('points.merchant_id', $merchantObj->id)
            ->limit(5)
            ->get();

        return $points;
    }

    public function storeCoupons($rewardId, array $data): void
    {
        foreach ($data as $key => $row) {
            $couponData = [
                'merchant_reward_id' => $rewardId,
                'code' => $row['code'],
                'status' => RewardCoupon::STATUS_AVAILABLE
            ];

            // @todo move to repo
            RewardCoupon::query()->firstOrCreate([
                'merchant_reward_id' => $rewardId,
                'code' => $row['code'],
            ], $couponData);
        }
    }

    public function getCoupons($rewardId, $type = ''): array
    {
        // @todo move to repo
        $coupons = RewardCoupon::query()->where('merchant_reward_id', $rewardId);

        return [
            'all' => $coupons->get(),
            'available' => $coupons->where('status', '=', RewardCoupon::STATUS_AVAILABLE)->get()
        ];
    }

    public function deleteCustomIcon($rewardId)
    {
        $amazon = new UploadFile();
        $path = $this->getIconNameById($rewardId);
        $amazon->delete($path);

        return $this->baseQuery->where('id', '=', $rewardId)->update([
            'reward_icon'      => null,
            'reward_icon_name' => null,
        ]);
    }

    public function getIconNameById($id)
    {
        $icon = $this->baseQuery->find($id);
        if (! isset($icon)) {
            return '';
        }
        $split_path = explode('/', $icon->reward_icon);
        $index = count($split_path);
        $icon_name = $split_path[$index - 1];

        return $icon_name;
    }

    public function deleteMerchantReward($rewardId)
    {
        $amazon = new UploadFile();
        $path = $this->getIconNameById($rewardId);
        $amazon->delete($path);

        return $this->baseQuery->where('id', '=', $rewardId)->delete();
    }

    public function updateTextPatterns($data, $merchantObj)
    {
        $merchant_rewards = $this->all($merchantObj);
        $currency = '$';
        $currencySign = true;
        if (! empty($merchantObj->merchant_currency)) {
            if ($merchantObj->currency_display_sign) {
                $currency = $merchantObj->merchant_currency->currency_sign;
                $currencySign = true;
            } else {
                $currency = $merchantObj->merchant_currency->name;
                $currencySign = false;
            }
        }
        $points = [
            'name'       => 'Point',
            'namePlural' => 'Points',
        ]; //Points definition
        if (! empty($merchantObj->points_settings)) {
            $points['name'] = $merchantObj->points_settings['name'];
            $points['namePlural'] = $merchantObj->points_settings['plural_name'];
        }
        $data += [
            'currency'     => $currency,
            'currencySign' => $currencySign,
            'points'       => $points,
        ];

        foreach ($merchant_rewards as $merchant_reward) {
            switch (strtolower($merchant_reward->reward_type)) {
                case 'fixed amount':
                    $this->updateFixedAmountText($merchant_reward, $data);
                    break;

                case 'variable amount':
                    $this->updateVariableAmountText($merchant_reward, $data);
                    break;

                case 'percentage off':
                    $this->updatePercentageOffText($merchant_reward, $data);
                    break;

                case 'free shipping':
                    $this->updateFreeShippingText($merchant_reward, $data);
                    break;
            }

            $merchant_reward->save();
        }

        $this->tierRepository->rewenRewardsNames($merchantObj);
    }

    protected function updateFixedAmountText($reward, $data)
    {
        $reward_name = strtr($reward->rewardDefaultName, [
                       "{points}" => $reward->points_required,
                       "{points-name}" => $reward->points_required > 1 ? $data['points']['namePlural'] : $data['points']['name'],
                       "{currency}" => $data['currency'],
                       "{reward-value}" => $reward->reward_value,
                       "{min-value}" => $reward->order_minimum ? $data['currency'].$reward->order_minimum : '',
                   ]);

        $reward_text = strtr($reward->rewardDefaultText, [
                       "{points}" => $reward->points_required,
                       "{points-name}" => $reward->points_required > 1 ? $data['points']['namePlural'] : $data['points']['name'],
                       "{currency}" => $data['currency'],
                       "{reward-value}" => $reward->reward_value,
                       "{min-value}" => $reward->order_minimum ? $data['currency'].$reward->order_minimum : '',
                   ]);

        $reward->reward_name = $reward_name;
        $reward->reward_text = $reward_text;
    }

    protected function updateVariableAmountText($reward, $data)
    {

        $reward_name = strtr($reward->rewardDefaultName, [
                       "{points}" => $reward->points_required,
                       "{points-name}" => $reward->points_required > 1 ? $data['points']['namePlural'] : $data['points']['name'],
                       "{currency}" => $data['currency'],
                       "{reward-value}" => $reward->reward_value,
                       "{min-value}" => $reward->order_minimum ? $data['currency'].$reward->order_minimum : '',
                   ]);

        $reward_text = strtr($reward->rewardDefaultText, [
                       "{points}" => $reward->points_required,
                       "{points-name}" => $reward->points_required > 1 ? $data['points']['namePlural'] : $data['points']['name'],
                       "{currency}" => $data['currency'],
                       "{reward-value}" => $reward->reward_value,
                       "{min-value}" => $reward->order_minimum ? $data['currency'].$reward->order_minimum : '',
                   ]);

        $reward->reward_name = $reward_name;
        $reward->reward_text = $reward_text;
    }

    protected function updatePercentageOffText($reward, $data)
    {
        $reward_name = strtr($reward->rewardDefaultName, [
                       "{points}" => $reward->points_required,
                       "{points-name}" => $reward->points_required > 1 ? $data['points']['namePlural'] : $data['points']['name'],
                       "{currency}" => $data['currency'],
                       "{reward-value}" => $reward->reward_value,
                       "{min-value}" => $reward->order_minimum ? $data['currency'].$reward->order_minimum : '',
                   ]);

        $reward_text = strtr($reward->rewardDefaultText, [
                       "{points}" => $reward->points_required,
                       "{points-name}" => $reward->points_required > 1 ? $data['points']['namePlural'] : $data['points']['name'],
                       "{currency}" => $data['currency'],
                       "{reward-value}" => $reward->reward_value,
                       "{min-value}" => $reward->order_minimum ? $data['currency'].$reward->order_minimum : '',
                   ]);

        $reward->reward_text = $reward_text;
        $reward->reward_name = $reward_name;

    }

    protected function updateFreeShippingText($reward, $data)
    {
        $reward_name = strtr($reward->rewardDefaultName, [
                       "{points}" => $reward->points_required,
                       "{points-name}" => $reward->points_required > 1 ? $data['points']['namePlural'] : $data['points']['name'],
                       "{currency}" => $data['currency'],
                       "{value}" => $reward->max_shipping ?? '',
                       "{min-value}" => $reward->order_minimum ? $data['currency'].$reward->order_minimum : '',
                       "{min-shipping}" => $reward->order_minimum ? $data['currency'].$reward->order_minimum : '',
                       "{max-shipping}" => $reward->max_shipping ?? '',
                   ]);

        $reward_text = strtr($reward->rewardDefaultText, [
                       "{points}" => $reward->points_required,
                       "{points-name}" => $reward->points_required > 1 ? $data['points']['namePlural'] : $data['points']['name'],
                       "{currency}" => $data['currency'],
                       "{value}" => $reward->max_shipping ?? '',
                       "{min-value}" => $reward->order_minimum ? $data['currency'].$reward->order_minimum : '',
                       "{min-shipping}" => $reward->order_minimum ? $data['currency'].$reward->order_minimum : '',
                       "{max-shipping}" => $reward->max_shipping ?? '',
                   ]);

        $reward->reward_name = $reward_name;
        $reward->reward_text = $reward_text;
    }

    protected function updateCurrencyValue(
        $value,
        $defaultText,
        $currency,
        $displaySign = true,
        $tag = '{reward-value}'
    ) {
        $newName = preg_replace('/'.$tag.'/i', $this->formatCurrencySign(str_replace('$', '\$', $currency), $value, $displaySign), $defaultText);

        return $newName;
    }
}
