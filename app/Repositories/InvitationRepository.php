<?php

namespace App\Repositories;


use App\Models\Invitation;
use App\Contracts\Repositories\InvitationRepository as InvitationContractRepository;


class InvitationRepository implements InvitationContractRepository
{
    private $baseQuery;

    public function __construct()
    {
        $this->baseQuery = Invitation::query();
    }

    public function getByToken($token)
    {
        return $this->baseQuery->where('token', '=', $token)->first();
    }

    public function create($userObj, $merchantObj)
    {
    }

    public function get($merchantObj)
    {
        if (!$merchantObj) {
            return true;
        }
        $invitationObj = $this->baseQuery
            ->where('team_id', '=', $merchantObj->id)
            ->with('user')
            ->orderBy('updated_at', 'DESC')
            ->get();
        return $invitationObj;
    }

    public function update(array $data)
    {
        $invitation = $this->baseQuery
            ->where('id', '=', $data['id'])
            ->update([
                'email' => $data['email'],
                'name' => $data['name'],
                'access' => $data['access'],
            ]);

        return $invitation;
    }

    public function delete($id)
    {
        return $this->baseQuery
            ->where('id', '=', $id)
            ->delete();


    }

    public function filter(array $data)
    {
        $result = $this->baseQuery
            ->join('users', 'users.email', '=', 'invitations.email')
            ->where('invitations.email', 'like', '%' . $data['filter'] . '%')
            ->orWhere('users.first_name', 'like', '%' . $data['filter'] . '%')
            ->orWhere('users.last_name', 'like', '%' . $data['filter'] . '%')
            ->get();
        return $result;
    }

}
