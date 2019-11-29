<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules for the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user.first_name' => 'required|max:191',
            'user.last_name' => 'required|max:191',
            'user.email' => 'required|email|unique:users,email,'.Auth::id(),
            'user.billing_email' => 'nullable|email',
            'user.password' => 'confirmed|nullable',
        ];
    }
}
