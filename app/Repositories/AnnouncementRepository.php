<?php

namespace App\Repositories;

use Laravel\Spark\Announcement;
use Laravel\Spark\Repositories\AnnouncementRepository as SparkAnnouncementRepository;

class AnnouncementRepository extends SparkAnnouncementRepository
{
    public function get()
    {
        return Announcement::query()
            ->where('user_id')
            ->get();
    }

    public function delete($id)
    {
        Announcement::query()
            ->where('id', '=', $id)
            ->delete();
    }
}
