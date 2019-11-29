<?php

namespace App\Http\Requests\Settings\Display\Widget;

use App\Models\WidgetSettings;
use Illuminate\Foundation\Http\FormRequest;

class TabCreateRequest extends FormRequest
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
        //$this->sanitize();

        return [
            'status' => 'required|boolean',
            'position' => 'required|in:'.implode(',', WidgetSettings::TAB_POSITIONS),
            'side_spacing' => 'required|integer',
            'bottom_spacing' => 'required|integer',
            'text' => 'required|max:300',
            'display_on' => 'required',
            'desktop_layout' => 'required',
            'tabColor' => ['required', 'regex:/^\#(?:[0-9abcdefABCDEF]{3}|[0-9abcdefABCDEF]{6})$/'],
            'tabFontColor' => ['required', 'regex:/^\#(?:[0-9abcdefABCDEF]{3}|[0-9abcdefABCDEF]{6})$/'],
            'new_icon' => 'base64image:jpeg,gif,png,bmp',
            'icon_name' => 'nullable|string|max:191',
        ];

    }


    protected function sanitize()
    {

        $this->merge([
            'text' => e($this->get('text')),
        ]);
    }

}