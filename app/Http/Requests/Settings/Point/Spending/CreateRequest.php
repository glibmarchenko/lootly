<?php

namespace App\Http\Requests\Settings\Point\Spending;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Reward;

class CreateRequest extends FormRequest
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
            'reward.values'          => 'required_if:program.rewardType,'.Reward::TYPE_FIXED_AMOUNT.','.Reward::TYPE_PERCENT_OFF.'|integer|not_in:0',
            'reward.points'          => 'required_if:program.rewardType,'.Reward::TYPE_FIXED_AMOUNT.','.Reward::TYPE_PERCENT_OFF.','.Reward::TYPE_FREE_PRODUCT.','.Reward::TYPE_FREE_SHIPPING.'|integer|not_in:0',
            'reward.product'         => 'required_if:program.rewardType,'.Reward::TYPE_FREE_PRODUCT,
            'reward.variable.values' => 'required_if:program.rewardType,'.Reward::TYPE_VARIABLE_AMOUNT.'|integer|not_in:0',
            'reward.variable.points' => 'required_if:program.rewardType,'.Reward::TYPE_VARIABLE_AMOUNT.'|integer|not_in:0',
            'reward.minPoints'       => 'required_if:program.rewardType,'.Reward::TYPE_VARIABLE_AMOUNT.'|integer|not_in:0',
            'reward.maxPoints'       => 'integer|not_in:0|nullable',
            'reward.minOrder'        => 'integer|nullable',
            'reward.maxShipping'     => $this->get('isWooCommerce') ? '' : 'required_if:program.rewardType,'.Reward::TYPE_FREE_SHIPPING.'|integer|not_in:0',
        ];
    }

    public function messages()
    {
        return [
            'reward.maxShipping.required_if' => 'You will need to define the value of this coupon before saving',
        ];
    }
}
