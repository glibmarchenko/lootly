@extends('website.auth.layout')

@section('title', 'Reset your Password')

@section('content')
<div class="card">
	<div class="">
		<img src="{{ url('images/logos/logo-black.png') }}" width="200">
		<h4>Reset your Password</h4>

        @if (session('status'))
            <div class="alert alert-success m-b-15">
                {{ session('status') }}
            </div>
        @endif

		<form role="form" method="POST" action="{{ url('password/email') }}">
			{{csrf_field()}} 

			<input name="email" value="" autofocus="autofocus" class="form-control" type="email" placeholder="Email">
            @if ($errors->has('email'))
                <div class="alert alert-danger">
                    {{ $errors->first('email') }}
                </div>
            @endif

			<button type="submit" class="btn btn-block btn-primary">Reset</button>
		</form>
	</div>
</div>
@endsection
