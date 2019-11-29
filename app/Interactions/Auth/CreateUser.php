<?php

namespace App\Interactions\Auth;

use Laravel\Spark\Spark;

class CreateUser extends \Laravel\Spark\Interactions\Auth\CreateUser
{
    /**
     * Get the basic validation rules for creating a new user.
     *
     * @param  \Laravel\Spark\Http\Requests\Auth\RegisterRequest  $request
     * @return array
     */
    public function rules($request)
    {
        return [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:'.Spark::minimumPasswordLength(),
            'vat_id' => 'nullable|max:50|vat_id',
            'terms' => 'required|accepted',
        ];
    }
}
