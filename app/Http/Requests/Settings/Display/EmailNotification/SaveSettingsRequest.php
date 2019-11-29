<?php

namespace App\Http\Requests\Settings\Display\EmailNotification;

use Illuminate\Foundation\Http\FormRequest;

class SaveSettingsRequest extends FormRequest
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
            'name' => 'max:191',
            'replyEmail' => 'email|max:191',
            'replyName' => 'max:191',
            'emailBranding' => 'boolean',
            'customDomain' => 'max:191',
            'new_icon' => 'base64image:jpeg,gif,png,bmp',
            'icon_name' => 'nullable|string|max:191',
        ];

    }

}