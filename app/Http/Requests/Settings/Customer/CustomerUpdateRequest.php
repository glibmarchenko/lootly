<?php

namespace App\Http\Requests\Settings\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CustomerUpdateRequest extends FormRequest
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
            'birthday' => 'date|date_format:m/d/Y',
        ];

    }

}