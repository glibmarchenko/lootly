<?php

namespace App\Http\Requests\Api\Merchant\Earning;

use Illuminate\Foundation\Http\FormRequest;

class ActionStoreRequest extends FormRequest
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
            'facebook.url'       => ['required_if:action_slug,facebook-like|url'],
            'earning.goal'       => 'required_if:action_slug,goal-spend|integer|not_in:0',
            'share.url'          => ['required_if:action_slug,facebook-share|url'],
            'twitter_share.url'  => ['required_if:action_slug,twitter-share|url'],
            'twitter.username'   => 'required_if:action_slug,twitter-follow|min:2|max:100',
            'instagram.username' => 'required_if:action_slug,instagram-follow|min:2|max:100',
            'earning.value'      => 'required|integer|not_in:0',
            'content.url'        => ['required_if:action_slug,read-content|url'],
            //'zap'                => "required_if:action_slug,custom-earning|unique:merchant_actions,zap_name,{$this->get('merchant_action_id')}",
        ];
    }

    public function messages()
    {
        return [
            'instagram.username.required_if' => 'The Instagram username field is required.',
        ];
    }
}
