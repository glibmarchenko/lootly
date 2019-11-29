<?php

namespace App\Repositories;

use App\Merchant;
use App\Models\MerchantAction;
use App\Contracts\Repositories\MerchantActionRepository as MerchantActionRepositoryContract;
use App\Services\Amazon\UploadFile;

class MerchantActionRepository implements MerchantActionRepositoryContract
{
    protected $baseQuery;

    public function __construct()
    {
        $this->baseQuery = MerchantAction::query();
    }

    /**
     * @param \Object $merchantObj
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|mixed|null
     */
    public function get($merchantObj)
    {
        if (! $merchantObj) {
            return null;
        }
        $action = $this->baseQuery->where('merchant_id', '=', $merchantObj->id)
            ->with('action')
            ->orderBy('created_at', 'desc')
            ->get();

        return $action;
    }

    /**
     * @param string $code
     */
    public function find()
    {
        // TODO: Implement find() method.
    }

    public function create($merchantObj, $actionObj, array $data)
    {
        $amazon = new UploadFile();
        $file = $data['iconPreview'];
        if ($data['iconPreview']) {
            $icone_url = $amazon->upload($merchantObj, $file, $actionObj->id);
        }
        //        return $this->baseQuery->updateOrCreate(
        //            ['merchant_id' => $merchantObj->id, 'action_id' => $actionObj->id,],
        //            [
        //                'action_name' => $data['program']['name'],
        //                'active_flag' => $data['program']['status'],
        //                'action_icon_name' => $data['program']['icon_name'],
        //                'reward_text' => $data['program']['rewardText'],
        //                'reward_email_text' => $data['program']['emailText'],
        //                'point_value' => $data['earning']['value'],
        //                'fb_page_url' => isset($data['facebook']['url']) ? $data['facebook']['url'] : null,
        //                'share_message' => isset($data['share']['message']) ? $data['share']['message'] : null,
        //                'share_url' => isset($data['share']['url']) ? $data['share']['url'] : null,
        //                'twitter_username' => isset($data['twitter']['username']) ? $data['twitter']['username'] : null,
        //                'content_url' => isset($data['content']['url']) ? $data['content']['url'] : null,
        //                'review_status' => isset($data['review']['status']) ? $data['review']['status'] : null,
        //                'review_type' => isset($data['review']['type']) ? $data['review']['type'] : null,
        //                'goal' => isset($data['earning']['goal']) ? $data['earning']['goal'] : null,
        //                'action_icon' => isset($icone_url) ? $icone_url : null,
        //                'earning_limit' => isset($data['earning']['limit']['status']) ? $data['earning']['limit']['status'] : null,
        //                'is_fixed' => isset($data['earning']['type']) ? $data['earning']['type'] : null,
        //                'send_email_notification' => $data['emailNotification'],
        //                'earning_limit_time' => isset($data['earning']['limit']) ? $data['earning']['limit']['value'] . ' ' . $data['earning']['limit']['duration']
        //                    : null,
        //            ]);

        $merchant_action = $this->baseQuery->where('merchant_id', '=', $merchantObj->id)
            ->where('action_id', '=', $actionObj->id)
            ->first();

        if (! $merchant_action) {
            $merchant_action = new MerchantAction();
            $merchant_action->action_id = $actionObj->id;
            $merchant_action->merchant_id = $merchantObj->id;
            $merchant_action->save();
        }
        $merchant_action->action_name = $data['program']['name'];
        $merchant_action->active_flag = $data['program']['status'];
        $merchant_action->action_icon_name = $data['program']['icon_name'];
        $merchant_action->reward_text = $data['program']['rewardText'];
        $merchant_action->reward_default_text = $data['program']['rewardTextDefault'];
        $merchant_action->reward_email_text = $data['program']['emailText'];
        $merchant_action->default_email_text = $data['program']['emailDefaultText'];
        $merchant_action->point_value = $data['earning']['value'];
        $merchant_action->fb_page_url = isset($data['facebook']['url']) ? $data['facebook']['url'] : null;
        $merchant_action->share_message = isset($data['share']['message']) ? $data['share']['message'] : null;
        $merchant_action->share_url = isset($data['share']['url']) ? $data['share']['url'] : null;
        $merchant_action->twitter_username = isset($data['twitter']['username']) ? $data['twitter']['username'] : null;
        $merchant_action->content_url = isset($data['content']['url']) ? $data['content']['url'] : null;
        $merchant_action->review_status = isset($data['review']['status']) ? $data['review']['status'] : null;
        $merchant_action->review_type = isset($data['review']['type']) ? $data['review']['type'] : null;
        $merchant_action->goal = isset($data['earning']['goal']) ? $data['earning']['goal'] : null;
        $merchant_action->goal_unit = isset($data['earning']['goal_type']) && in_array($data['earning']['goal_type'], MerchantAction::GOAL_UNITS) ? $data['earning']['goal_type'] : null;
        if (isset($icone_url)) {
            $merchant_action->action_icon = $icone_url;
        }
        $merchant_action->is_fixed = isset($data['earning']['type']) ? $data['earning']['type'] : null;
        $merchant_action->send_email_notification = $data['emailNotification'];

        $merchant_action->earning_limit = isset($data['earning']['limit']['status']) ? $data['earning']['limit']['status'] : null;
        $merchant_action->earning_limit_value = isset($data['earning']['limit']['value']) ? $data['earning']['limit']['value'] : null;
        $merchant_action->earning_limit_type = isset($data['earning']['limit']['type']) && in_array($data['earning']['limit']['type'], MerchantAction::EARNING_LIMIT_TYPES) ? $data['earning']['limit']['type'] : null;
        $merchant_action->earning_limit_period = isset($data['earning']['limit']['period']) && in_array($data['earning']['limit']['period'], MerchantAction::EARNING_LIMIT_PERIODS) ? $data['earning']['limit']['period'] : null;
        $merchant_action->save();
        $merchant_action->fresh();

        return $merchant_action;
    }

    public function getTopEarningAction($merchantObj)
    {
        if (! $merchantObj) {
            return null;
        }
        $points = $this->baseQuery->leftJoin('points', 'merchant_actions.id', '=', 'points.merchant_action_id')
            ->select('merchant_actions.action_name', 'merchant_actions.action_icon_name', \DB::raw('COUNT(points.id) as actions_num'))
            ->groupBy('merchant_actions.id')
            ->where('points.merchant_id', $merchantObj->id)
            ->limit(5)
            ->get();

        return $points;
    }

    public function deleteCustomIcon($actionID)
    {
        $amazon = new UploadFile();
        $path = $this->getIconNameById($actionID);
        $amazon->delete($path);

        return $this->baseQuery->where('id', '=', $actionID)->update([
            'action_icon' => null,
        ]);
    }

    public function getIconNameById($id)
    {

        $icon = $this->baseQuery->findOrFail($id);
        $split_path = explode('/', $icon->reward_icon);
        $index = count($split_path);
        $icon_name = $split_path[$index - 1];

        return $icon_name;
    }

    public function getActiveByType(Merchant $merchant, $type)
    {
        $merchant_actions = $merchant->merchant_actions()
            ->where('active_flag', 1)
            ->with(['action'])
            ->whereHas('action', function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->get();

        return $merchant_actions->sortByDesc('action.priority');
    }

    public function findByActionName(Merchant $merchant, $name)
    {
        $merchant_action = $merchant->merchant_actions()->with(['action'])->whereHas('action', function ($q) use ($name
        ) {
            $q->where('url', $name);
        })->first();

        return $merchant_action;
    }
    /*
    public function getActiveActionsByMerchantId($merchant_id)
    {
        return MerchantAction::where([
            'merchant_id' => $merchant_id,
            'active_flag' => 1,
        ])->with('action')->get();
    }
    */

    public function updateTextPatterns($merchantObj)
    {
        $merchant_actions = $this->get($merchantObj);
        $currency = '$';
        if (! empty($merchantObj->merchant_currency)) {
            $currency = $merchantObj->merchant_currency->currency_sign;
        }
        $points = [
            'name'       => 'Point',
            'namePlural' => 'Points',
        ]; //Points definition
        if (! empty($merchantObj->points_settings)) {
            $points['name'] = $merchantObj->points_settings['name'];
            $points['namePlural'] = $merchantObj->points_settings['plural_name'];
        }
        foreach ($merchant_actions as $merchant_action) {
            if($merchant_action->action_name == 'Make a Purchase') {
                $newRewardText = preg_replace('/{amount}/i', str_replace('$', '\$', $currency).'1', $merchant_action->reward_default_text);
                $newRewardText = preg_replace('/{points}/i', $merchant_action->point_value, $newRewardText);
                $newRewardText = preg_replace('/{points-name}/i', $merchant_action->point_value > 1 ? $points['namePlural'] : $points['name'], $newRewardText);
                $merchant_action->reward_text = $newRewardText;
                $merchant_action->save();
            } elseif($merchant_action->action_name == 'Goal Spend'){
                $newRewardText = preg_replace('/{amount}/i', str_replace('$', '\$', $currency) . $merchant_action->goal, $merchant_action->reward_default_text);
                $newRewardText = preg_replace('/{points}/i', $merchant_action->point_value, $newRewardText);
                $newRewardText = preg_replace('/{points-name}/i', $merchant_action->point_value > 1 ? $points['namePlural'] : $points['name'], $newRewardText);
                $merchant_action->reward_text = $newRewardText;
                $merchant_action->save();
            }
        }
    }
}
