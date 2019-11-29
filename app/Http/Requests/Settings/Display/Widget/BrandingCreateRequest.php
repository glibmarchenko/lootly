<?php

namespace App\Http\Requests\Settings\Display\Widget;

use App\Models\WidgetSettings;
use Illuminate\Foundation\Http\FormRequest;

class BrandingCreateRequest extends FormRequest
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
            'primaryColor' => ['required', 'regex:/^\#(?:[0-9abcdefABCDEF]{3}|[0-9abcdefABCDEF]{6})$/'],
            'secondaryColor' => ['required', 'regex:/^\#(?:[0-9abcdefABCDEF]{3}|[0-9abcdefABCDEF]{6})$/'],
            'headerBackground' => ['required', 'regex:/^\#(?:[0-9abcdefABCDEF]{3}|[0-9abcdefABCDEF]{6})$/'],
            'headerBackgroundFontColor' => ['required', 'regex:/^\#(?:[0-9abcdefABCDEF]{3}|[0-9abcdefABCDEF]{6})$/'],
            'buttonColor' => ['required', 'regex:/^\#(?:[0-9abcdefABCDEF]{3}|[0-9abcdefABCDEF]{6})$/'],
            'buttonFontColor' => ['required', 'regex:/^\#(?:[0-9abcdefABCDEF]{3}|[0-9abcdefABCDEF]{6})$/'],
            'tabColor' => ['required', 'regex:/^\#(?:[0-9abcdefABCDEF]{3}|[0-9abcdefABCDEF]{6})$/'],
            'tabFontColor' => ['required', 'regex:/^\#(?:[0-9abcdefABCDEF]{3}|[0-9abcdefABCDEF]{6})$/'],
            'linkColor' => ['required', 'regex:/^\#(?:[0-9abcdefABCDEF]{3}|[0-9abcdefABCDEF]{6})$/'],
            'font' => 'required|in:'.implode(',', WidgetSettings::BRANDING_FONTS),
            'hideLootlyLogo' => 'required|boolean',
        ];

    }

}