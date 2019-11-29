<?php

namespace App\Repositories;


use App\Models\PointSetting;
use App\Contracts\Repositories\PointSettingsRepository as PointSettingRepositoryContract;


class PointSettingsRepository implements PointSettingRepositoryContract
{
    public $baseQuery;

    public function __construct()
    {
        $this->baseQuery = PointSetting::query();
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }


    /**
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(array $data, $merchantObj)
    {


        $pointSettings = $this->baseQuery->updateOrCreate(
            ['merchant_id' => $merchantObj->id],
            [
                'name' => $data['name'],
                'plural_name'=>$data['plural_name'],
                'currency' => $data['currency'],
                'status' => $data['status'],
                'experient_status' => $data['experient_status'],
                'experient_after' => $data['experient_after'],
                'reminder_status' => $data['reminder_status'],
                'final_reminder_status' => $data['final_reminder_status'],


            ]);
        return $pointSettings;
    }

    public function get($merchantObj)
    {
        return $this->baseQuery->where('merchant_id', '=', $merchantObj->id)->get();
    }

    public function updateReminder(array $data, $merchantObj)
    {
        $pointSettings = $this->baseQuery->updateOrCreate(
            ['merchant_id' => $merchantObj->id],
            [
                'reminder_day' => $data['reminder_day'],
                'final_reminder_day' => $data['final_reminder_day'],
                'status' => $data['status'],
                'experient_status' => $data['experient_status'],
                'reminder_status' => $data['reminder_status'],
                'final_reminder_status' => $data['final_reminder_status'],
            ]);
        return $pointSettings;
    }

    public function updateFinalReminder(array $data, $merchantObj)
    {

        $pointSettings = $this->baseQuery->updateOrCreate(
            ['merchant_id' => $merchantObj->id],
            [
                'final_reminder_day' => $data['final_reminder_day'],
                'reminder_day' => $data['reminder_day'],
                'status' => $data['status'],
                'experient_status' => $data['experient_status'],
                'reminder_status' => $data['reminder_status'],
                'final_reminder_status' => $data['final_reminder_status'],


            ]);
        return $pointSettings;
    }
}
