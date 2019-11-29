@extends('website.auth.layout')

@section('title', 'Login')

@section('content')
<div class="card">
	<div class="">
		<img src="{{ url('images/logos/logo-black.png') }}" width="200">

        <h4>Login to your account</h4>

		<form role="form" method="POST" action="{{ url('login') }}">
			{{csrf_field()}} 
			<input name="email" value="" class="form-control" type="email" placeholder="Email">
            @if ($errors->has('email'))
                <div class="alert alert-danger">
                    {{ $errors->first('email') }}
                </div>
            @endif

			<input name="password" class="form-control" type="password" placeholder="Password">
            @if ($errors->has('password'))
                <div class="alert alert-danger">
                    {{ $errors->first('password') }}
                </div>
            @endif

			<div class="text-right">
				<a href="{{ url('password/reset') }}" class="forgot-password">Forgot Password?</a>
			</div>
			<button type="submit" class="btn btn-block btn-primary">Log In</button>
            <p>Don't have a Lootly account? <a href="{{ url('pricing') }}"><b>Sign Up</b></a></p>
        </form>
	</div>
</div>
@endsection
