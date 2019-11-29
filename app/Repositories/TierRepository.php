<?php

namespace App\Repositories;

use App\Merchant;
use App\Models\Tier;
use App\Services\Amazon\UploadFile;
use DB;

class TierRepository
{
    private $baseQuery;

    /**
     * TierRepository constructor.
     */
    public function __construct()
    {
        $this->baseQuery = Tier::query();
    }

    private function restrictByMerchant(?int $merchant_id = null)
    {
        return $merchant_id ? Tier::where('merchant_id', $merchant_id) : Tier::query();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get(?Merchant $merchant = null)
    {
        return $this->restrictByMerchant($merchant->id ?? null)
            ->with('tierBenefits')
            ->orderBy('spend_value', 'ASC')
            ->orderBy('created_at', 'ASC')
            ->get();
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getById($id, $merchant_id)
    {
        return $this->restrictByMerchant($merchant_id)->where('id', '=', $id)->with('tierBenefits')->firstOrFail();
    }

    /**
     * @param       $merchantObj
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function edit($merchantObj, array $data)
    {
        $icone_url = $this->uploadIcon($merchantObj, $data);
        $tier = $this->baseQuery->updateOrCreate(['merchant_id' => $merchantObj->id], [
                    'name'         => $data['name'],
                    'spend_value'  => $data['spend_value'],
                    'multiplier'   => $data['multiplier'],
                    'rolling_days' => $data['rolling_days'],
                    'image_url'    => isset($icone_url) ? $icone_url : '',
                ]);

        return $tier;
    }

    public function add($merchantObj, $data)
    {

        $icone_url = $this->uploadIcon($merchantObj, $data);
        $tier = new Tier();
        $tier->merchant_id = $merchantObj->id;
        $tier->name = $data['program']['name'];
        $tier->text_email = $data['program']['emailText'];
        $tier->text_email_default = $data['program']['emailDefaultText'];
        $tier->status = $data['program']['status'];
        $tier->spend_value = $data['spend']['value'];
        $tier->requirement_text = $data['spend']['text'];
        $tier->requirement_text_default = $data['spend']['defaultText'];
        $tier->multiplier_text = $data['points']['text'];
        $tier->multiplier_text_default = $data['points']['defaultText'];
        $tier->multiplier = $data['points']['value'];
        $tier->email_notification = $data['emailNotification'];
        $tier->rolling_days = isset($data['rolling_days']) ? $data['rolling_days'] : null;
        $tier->currency = $data['currency'];
        $tier->image_url = isset($icone_url) ? $icone_url : '';
        $tier->image_name = $data['program']['icon_name'];
        $tier->default_icon_color = $data['program']['defaultIconColor'];
        $tier->save();
        $tierBenefits = new TierBenefitRepository();
        $tierBenefits->add($tier->id, $data['benefits']);
    }

    public function update($merchantObj, $data)
    {
        $icone_url = $this->uploadIcon($merchantObj, $data);
        $tier = $this->baseQuery->find($data['tier_id']);

        $tier->name = $data['program']['name'];
        $tier->text_email = $data['program']['emailText'];
        $tier->text_email_default = $data['program']['emailDefaultText'];
        $tier->status = $data['program']['status'];
        $tier->spend_value = $data['spend']['value'];
        $tier->requirement_text = $data['spend']['text'];
        $tier->requirement_text_default = $data['spend']['defaultText'];
        $tier->multiplier_text = $data['points']['text'];
        $tier->multiplier_text_default = $data['points']['defaultText'];
        $tier->multiplier = $data['points']['value'];
        $tier->email_notification = $data['emailNotification'];
        $tier->rolling_days = isset($data['rolling_days']) ? $data['rolling_days'] : null;
        $tier->currency = $data['currency'];
        $tier->default_icon_color = $data['program']['defaultIconColor'];
        
        if( isset($icone_url)){
            $tier->image_url = $icone_url;
        }

        $tier->image_name = $data['program']['icon_name'];

        $tier->save();
        $tierBenefits = new TierBenefitRepository();
        $tierBenefits->removeAll($data['tier_id']);
        $tierBenefits->add($tier->id, $data['benefits']);
    }

    public function uploadIcon($merchantObj, $data)
    {
        $amazon = new UploadFile();
        $file = $data['iconPreview'];
        if ($data['iconPreview']) {
            return $amazon->upload($merchantObj, $file, $id = null);
        }
    }

    public function deleteCustomIcon($rewardId)
    {
        $amazon = new UploadFile();
        $path = $this->getIconNameById($rewardId);
        $amazon->delete($path);

        return $this->baseQuery->where('id', '=', $rewardId)->update([
                'image_url'  => null,
                'image_name' => null,
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

    public function rewenRewardsNames($merchantObj) {
        $tiers = $this->get($merchantObj);
        foreach ($tiers as $tier => $tierModel) {
            foreach ($tierModel->tierBenefits as $tierBenefit){
                if(isset($tierBenefit->merchant_reward_id)) {
                    $tierBenefit->benefits_discount = $tierBenefit->getRewardName();
                    $tierBenefit->save();
                }
            }
        }
    }
    /*
    public function getActiveMyMerchantId($merchant_id, $with = [])
    {
        return Tier::where([
            'merchant_id' => $merchant_id,
            'status'      => 1,
        ])->orderBy('spend_value', 'asc')->get();
    }
    */
}
