<?php

namespace App\Repositories;

use App\Models\EmailBlacklist;
use App\Contracts\Repositories\EmailBlacklistRepository as EmailBlacklistRepositoryContract;

class EmailBlacklistRepository implements EmailBlacklistRepositoryContract
{
    public $baseQuery;

    public function __construct()
    {
        $this->baseQuery = EmailBlacklist::query();
    }

    public function create($data, $merchantObj)
    {
        $this->baseQuery->create([
            'merchant_id' => $merchantObj->id,
            'email' => $data['email']
        ]);
    }

    public function check($merchantObj, $email)
    {
        $item = $this->baseQuery
            ->where('merchant_id', '=', $merchantObj->id)
            ->where('email', '=', $email)
            ->first();

        return $item;
    }
}
