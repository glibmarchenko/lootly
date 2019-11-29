{{isset($new_user_data['first_name']) && trim($new_user_data['first_name']) ? __('Congratulations, :Name!', ['name' => $new_user_data['first_name']]) : __('Congratulations!')}}

<br><br>

{{__('You have been successfully registered at '.config('app.name').'.')}}

<br><br>
{{__('Your account details:')}}
<br><br>
{{__('E-mail: '.$new_user_data['email'])}}
<br/>
{{__('Password: '.$new_user_data['password'])}}
<br/>
<br/>

{{__('You can change your password for security reasons or reset it if you forget it')}}

<br><br>
