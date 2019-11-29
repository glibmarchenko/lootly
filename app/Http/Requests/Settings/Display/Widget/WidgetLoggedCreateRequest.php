<?php

namespace App\Http\Requests\Settings\Display\Widget;

use App\Models\WidgetSettings;
use Illuminate\Foundation\Http\FormRequest;

class WidgetLoggedCreateRequest extends FormRequest
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
            'welcome.text' => 'required|max:300',
            'welcome.position' => 'required|in:'.implode(',', WidgetSettings::WELCOME_POSITIONS),
            'welcome.new_background' => 'base64image:jpeg,gif,png,bmp',
            'welcome.background_name' => 'nullable|string|max:191',
            'welcome.background_opacity' => ['required','regex:/^([0-9]|([1-9][0-9])|100)\%$/'],
            'welcome.new_icon' => 'base64image:jpeg,gif,png,bmp',
            'welcome.icon_name' => 'nullable|string|max:191',

            'points.balanceText' => 'required|max:300',
            'points.availableText' => 'required|max:300',
            'points.earnButtonText' => 'required|max:191',
            'points.spendButtonText' => 'required|max:191',
            'points.rewardsButtonText' => 'required|max:191',

            'vip.buttonText' => 'required|max:191',
            'vip.new_background' => 'base64image:jpeg,gif,png,bmp',
            'vip.background_name' => 'nullable|string|max:191',
            'vip.background_opacity' => ['required','regex:/^([0-9]|([1-9][0-9])|100)\%$/'],

            'referral.mainText' => 'required|max:300',
            'referral.receiverText' => 'required|max:300',
            'referral.senderText' => 'required|max:300',
            'referral.LinkText' => 'required|max:300',
            'referral.new_background' => 'base64image:jpeg,gif,png,bmp',
            'referral.background_name' => 'nullable|string|max:191',
            'referral.background_opacity' => ['required','regex:/^([0-9]|([1-9][0-9])|100)\%$/'],

            'howItWorks.title' => 'required|max:300',
            'howItWorks.text' => 'required|max:300',
            'howItWorks.position' => 'required|in:'.implode(',', WidgetSettings::WELCOME_POSITIONS),
        ];
    }
}