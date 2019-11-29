<?php

namespace App\Http\Requests\Settings\Display\RewardPage;

use Illuminate\Foundation\Http\FormRequest;

class SavePageBrangingRequest extends FormRequest
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
            'font' => 'max:190',
            'widgetBranding' => 'boolean',
        ];

    }

}