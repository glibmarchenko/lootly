<?php

namespace App\Http\Requests\Api\Referrals\Sharing;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'facebook.status'       => 'required|boolean',
            'facebook.message'      => '',
            'facebook.icon_preview' => 'base64image:jpeg,gif,png,bmp',
            'facebook.icon_name'    => 'nullable|string|max:191',

            'twitter.status'       => 'required|boolean',
            'twitter.message'      => '',
            'twitter.icon_preview' => 'base64image:jpeg,gif,png,bmp',
            'twitter.icon_name'    => 'nullable|string|max:191',

            'google.status'       => 'required|boolean',
            'google.message'      => '',
            'google.icon_preview' => 'base64image:jpeg,gif,png,bmp',
            'google.icon_name'    => 'nullable|string|max:191',

            'email.status'  => 'required|boolean',
            'email.subject' => '',
            'email.body'    => '',

            'title'       => '',
            'description' => '',
        ];
    }
}
