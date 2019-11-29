<?php

namespace App\Http\Requests\Settings\Point;

use Illuminate\Foundation\Http\FormRequest;

class EditRequest extends FormRequest
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
            'plural_name'=>'required',
//            'programStatus' => 'required',
            'experient_status' => 'required',
//            'experient_after' => 'required',
            'reminder_status' => 'required',
            'final_reminder_status' => 'required',
        ];

    }
}
