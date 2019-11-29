<?php

namespace App\Http\Requests\Settings\Display\EmailNotification;

use Illuminate\Foundation\Http\FormRequest;

class NotificationSettingsCreateRequest extends FormRequest
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
            'subjectLine' => 'required|max:255',
            'body' => 'required',
            'button.text' => 'required|max:191',
            'button.color' => ['required', 'regex:/^\#(?:[0-9abcdefABCDEF]{3}|[0-9abcdefABCDEF]{6})$/'],
        ];

    }

}