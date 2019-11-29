<?php

namespace App\Contracts\Repositories;


interface InvitationRepository
{


    /**
     * @param $merchantObj
     * @return mixed
     */
    public function getByToken($token);

    /**
     * @param $merchantObj
     * @return mixed
     */
    public function get($merchantObj);

    /**
     * @param $user
     * @param $points
     * @return mixed
     */
    public function create($userObj, $merchantObj);


    /**
     * @param $user
     * @param $points
     * @return mixed
     */
    public function update(array $data);


    /**
     * @param $user
     * @return mixed
     */
    public function delete($id);

    public function filter(array $data);
}
