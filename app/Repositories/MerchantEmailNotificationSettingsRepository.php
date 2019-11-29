<?php

namespace App\Repositories;


use App\Models\MerchantEmailNotificationSettings;
use App\Contracts\Repositories\MerchantEmailNotificationSettingsRepository as MerchantEmailNotificationSettingsRepositoryContract;
use App\Services\Amazon\UploadFile;

class MerchantEmailNotificationSettingsRepository implements MerchantEmailNotificationSettingsRepositoryContract
{
    public $baseQuery;

    public function __construct()
    {
        $this->baseQuery = MerchantEmailNotificationSettings::query();
    }

    public function find($merchant)
    {
        $item = $this->baseQuery
            ->where('merchant_id', '=', $merchant->id)
            ->first();

        return $item;
    }

    public function updateOrCreate($merchant, $data)
    {
        $item = $this->baseQuery
            ->where('merchant_id', '=', $merchant->id)
            ->first();

        if (! $item) {
            $item = new MerchantEmailNotificationSettings();
            $item->merchant_id = $merchant->id;
            $item->save();
        }

        $new_icon_url = null;
        $amazon = new UploadFile();
        $file = isset($data['new_icon']) && $data['new_icon'] ? $data['new_icon'] : null;
        if ($file) {
            $new_icon_url = $amazon->upload($merchant, $file, 'ns_'.$item->id);
        }

        $item->from_name = isset($data['name']) ? trim($data['name']) : null;
        $item->reply_to_email = isset($data['replyEmail']) ? trim($data['replyEmail']) : null;
        $item->reply_to_name = isset($data['replyName']) ? trim($data['replyName']) : null;
        $item->custom_domain = isset($data['customDomain']) ? trim($data['customDomain']) : null;
        $item->remove_branding = isset($data['emailBranding']) && $data['emailBranding'] ? true : false;
        if($new_icon_url) {
            $this->deleteNotificationSettingsImage($item->company_logo);
            $item->company_logo = $new_icon_url;
            $item->company_logo_name = isset($data['icon_name']) ? $data['icon_name'] : null;
        }else{
            if(!isset($data['icon']) || !trim($data['icon'])){
                $this->deleteNotificationSettingsImage($item->company_logo);
                $item->company_logo = null;
                $item->company_logo_name = null;
            }
        }
        $item->save();
        $item->fresh();

        return $item;
    }

    private function deleteNotificationSettingsImage($image = null)
    {
        if($image) {
            $amazon = new UploadFile();

            $split_path = explode('/', $image);
            $index = count($split_path);
            $path = $split_path[$index - 1];

            $amazon->delete($path);
        }
    }
}
