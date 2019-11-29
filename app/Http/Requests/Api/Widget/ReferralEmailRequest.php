<?php

namespace App\Http\Requests\Api\Widget;

use Illuminate\Foundation\Http\FormRequest;

class ReferralEmailRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'    => 'required|string|max:191',
            'email'   => 'required|email',
            'subject' => 'required|string',
            'body'    => 'required|max:1000',
        ];
    }
}
