<?php

namespace App\Repositories;


use App\Models\NotificationSettings;
use App\Contracts\Repositories\NotificationSettingsRepository as NotificationSettingsRepositoryContract;


class NotificationSettingsRepository implements NotificationSettingsRepositoryContract
{
    public $baseQuery;

    public function __construct()
    {
        $this->baseQuery = NotificationSettings::query();
    }

    public function findByType($merchant, $type)
    {
        $item = $this->baseQuery
            ->where('merchant_id', '=', $merchant->id)
            ->where('notification_type', '=', $type)
            ->first();

        return $item;
    }

    public function updateOrCreate($merchant, $data)
    {
        $item = $this->baseQuery
            ->where('merchant_id', '=', $merchant->id)
            ->where('notification_type', '=', $data['notification_type'])
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $item) {
            $item = new NotificationSettings();
            $item->merchant_id = $merchant->id;
            $item->save();
        }

        $item->status = isset($data['status']) ? trim($data['status']) : 1;
        $item->subject = isset($data['subjectLine']) ? trim($data['subjectLine']) : null;
        $item->body = isset($data['body']) ? trim($data['body']) : null;
        $item->button_text = isset($data['button']['text']) ? trim($data['button']['text']) : null;
        $item->button_color = isset($data['button']['color']) ? trim($data['button']['color']) : null;
        $item->notification_type = isset($data['notification_type']) ? trim($data['notification_type']) : null;
        $item->icons = isset($data['icons']) && is_array($data['icons']) ? $data['icons'] : [];
        $item->save();
        $item->fresh();

        return $item;
    }
}
