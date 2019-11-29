<?php

namespace App\Contracts\Repositories;

use Laravel\Spark\Contracts\Repositories\UserRepository as SparkUserRepository;

interface UserRepository extends SparkUserRepository
{
    /**
     * @param $email
     * @return mixed
     */
    public function getByEmail($email);

    public function hasUser($email);

    public function update($user, array $data);
}
