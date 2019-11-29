<?php

namespace App\Http\Requests\Settings\Point\Earning;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
        //allow urls without http
        define('URL_VALIDATION',
            'regex:^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$^');
        return [
            'facebook.url' => ['required_if:defaultActionName,Facebook Like', URL_VALIDATION],
            'earning.goal' => 'required_if:defaultActionName,Goal Spend|integer|not_in:0',
            'share.url' => ['required_if:defaultActionName,Facebook Share', URL_VALIDATION], 
            'twitter_share.url' => ['required_if:defaultActionName,Twitter Share', URL_VALIDATION],
            'twitter.username' => 'required_if:defaultActionName,Twitter Follow|min:2|max:100',
            'earning.value' => 'required|integer|not_in:0',
            'content.url' => ['required_if:defaultActionName,Read Content', URL_VALIDATION],
        ];

    }
}
