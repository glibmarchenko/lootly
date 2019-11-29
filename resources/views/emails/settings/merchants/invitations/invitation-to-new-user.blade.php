{{isset($invitation->name) && trim($invitation->name) ? __('Hi, :Name!', ['name' => $invitation->name]) : __('Hi!')}}

<br><br>

{{__('teams.user_invited_to_join_team', ['userName' => $invitation->team->owner->name])}}
{{__('If you do not already have an account, you may click the following link to get started:')}}

<br><br>

<a href="{{ url('signup?invitation='.$invitation->token) }}">{{ url('signup?invitation='.$invitation->token) }}</a>

<br><br>

{{__('See you soon!')}}
