<?php

namespace App\Http\Requests\Api\Merchant;

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
            'name' => 'required',
            'selectedCountry' => 'required',
            'website' => 'required',
            'logo' => 'base64image:jpeg,gif,png,bmp',
            'logo_name' => 'nullable|string|max:191',
        ];
    }
}
