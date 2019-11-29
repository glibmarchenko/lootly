<?php

namespace App\Contracts\Repositories;

interface UserNotificationTypeRepository
{
    public function all();

    public function get();

    public function getSlugList();

    public function find($id);
}
