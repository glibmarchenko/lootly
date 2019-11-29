<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/28/18
 * Time: 1:49 PM
 */

namespace App\Http\Requests\Settings\Referral;

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
            'reward.values' => 'required_if:program.rewardType,'.Reward::TYPE_FIXED_AMOUNT.','.Reward::TYPE_PERCENT_OFF.','.Reward::TYPE_POINTS.'|integer|not_in:0',
            'reward.product.id' => 'required_if:program.rewardType,'.Reward::TYPE_FREE_PRODUCT,
            'reward.product.text' => 'required_if:program.rewardType,'.Reward::TYPE_FREE_PRODUCT,
            // 'reward.product.price' => 'required_if:program.rewardType,'.Reward::TYPE_FREE_PRODUCT,
            'reward.variable.values' => 'required_if:program.rewardType,'.Reward::TYPE_VARIABLE_AMOUNT.'|integer|not_in:0',
            'reward.variable.points' => 'required_if:program.rewardType,'.Reward::TYPE_VARIABLE_AMOUNT.'|integer|not_in:0',
            'reward.minPoints' => 'required_if:program.rewardType,'.Reward::TYPE_VARIABLE_AMOUNT.'|integer|not_in:0',
            'reward.maxPoints' => 'integer|not_in:0|nullable',
            'reward.minOrder' => 'integer|nullable',
            'reward.maxShipping' => 'integer|not_in:0',

        ];

    }
}