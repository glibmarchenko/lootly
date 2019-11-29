<?php

namespace App\Http\Requests\Api\Merchant;

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInvitationRequest extends FormRequest
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
            //'id' => 'required',
            'email' => 'required|email|max:191',
            'name' => 'required|max:191',
        ]);

        return $validator;

        /*return $validator->after(function ($validator) {
            return $this->verifyEmailNotAlreadyOnTeam($validator, $this->merchant)
                        ->verifyEmailNotAlreadyInvited($validator, $this->merchant);
        });*/
    }

    /**
     * Verify that the given e-mail is not already on the team.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @param  \Laravel\Spark\Team  $team
     * @return $this
     */
    protected function verifyEmailNotAlreadyOnTeam($validator, $team)
    {
        if($this->status && $this->status == 'accepted') {
            if ($team->users()->where('email', $this->email)->where('user_id', '!=', $this->id)->exists()) {
                $validator->errors()->add('email', __('teams.user_already_on_team'));
            }
        }

        return $this;
    }

    /**
     * Verify that the given e-mail is not already invited.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @param  \Laravel\Spark\Team  $team
     * @return $this
     */
    protected function verifyEmailNotAlreadyInvited($validator, $team)
    {
        if(!$this->status || $this->status != 'accepted') {
            if ($team->invitations()->where('email', $this->email)->where('id', '!=', $this->id)->exists()) {
                $validator->errors()->add('email', __('teams.user_already_invited_to_team'));
            }
        }

        return $this;
    }
}
