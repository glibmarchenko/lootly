<?php

namespace App\Http\Requests\Api\Merchant;

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class DeleteInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->ownsTeam($this->merchant) || $this->user()->roleOn($this->merchant) === 'owner';
    }

    /**
     * Get the validator for the request.
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validator()
    {
        $validator = Validator::make($this->all(), [
            'id' => 'required',
        ]);

        return $validator;
    }
}
