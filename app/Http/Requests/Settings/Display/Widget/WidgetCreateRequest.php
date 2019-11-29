<?php

namespace App\Http\Requests\Settings\Display\Widget;

use App\Models\WidgetSettings;
use Illuminate\Foundation\Http\FormRequest;

class WidgetCreateRequest extends FormRequest
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
            'welcome.header.title' => 'required|max:300',
            'welcome.header.subtitle' => 'required|max:300',
            'welcome.title' => 'required|max:300',
            'welcome.subtitle' => 'required|max:300',
            'welcome.buttonText' => 'required|max:191',
            'welcome.login' => 'required|max:191',
            'welcome.pointsRewardsTitle' => 'required|max:300',
            'welcome.pointsRewardsSubtitle' => 'required|max:300',
            'welcome.vipTitle' => 'required|max:300',
            'welcome.vipSubtitle' => 'required|max:300',
            'welcome.referralTitle' => 'required|max:300',
            'welcome.referralSubtitle' => 'required|max:300',

            'welcome.position' => 'required|in:'.implode(',', WidgetSettings::WELCOME_POSITIONS),
            'welcome.new_background' => 'base64image:jpeg,gif,png,bmp',
            'welcome.background_name' => 'nullable|string|max:191',
            'welcome.background_opacity' => ['required','regex:/^([0-9]|([1-9][0-9])|100)\%$/'],

            'waysToEarn.title' => 'required|max:300',
            'waysToEarn.text' => 'required|max:300',
            'waysToEarn.position' => 'required|in:'.implode(',', WidgetSettings::OVERVIEW_POSITIONS),
            'waysToSpend.title' => 'required|max:300',
            'waysToSpend.text' => 'required|max:300',
            'waysToSpend.position' => 'required|in:'.implode(',', WidgetSettings::OVERVIEW_POSITIONS),

            'referral.text' => 'required|max:300',
            'referral.buttonText' => 'required|max:191',
            'referral.new_background' => 'base64image:jpeg,gif,png,bmp',
            'referral.background_name' => 'nullable|string|max:191',
            'referral.background_opacity' => ['required','regex:/^([0-9]|([1-9][0-9])|100)\%$/'],
        ];

    }


    protected function sanitize()
    {

        $this->merge([
            'welcome.text' => e($this->get('welcome.text')),
            'welcome.buttonText' => e($this->get('welcome.buttonText')),
            'overview.text' => e($this->get('overview.text')),
            'referral.text' => e($this->get('referral.text')),
            'referral.buttonText' => e($this->get('referral.buttonText')),
        ]);
    }

}