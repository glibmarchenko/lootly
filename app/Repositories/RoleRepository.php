<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository
{
    private $baseQuery;

    public function __construct()
    {
        $this->baseQuery = Role::query();
    }

    public function create()
    {
        \DB::table('role_user')->create([
            'user_id' => \Auth::user()->id,
            'role_id' => $this->getOwnerId()
        ]);
    }

    public function getOwnerId()
    {
        return $this->baseQuery->where('name', '=', 'owner')->first()->id;
    }

    public function getEmployeeId()
    {
        return $this->baseQuery->where('name', '=', 'employee')->first()->id;
    }
}
