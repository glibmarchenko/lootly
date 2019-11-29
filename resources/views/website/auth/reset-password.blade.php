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

            <form role="form" method="POST" action="{{ url('password/reset') }}">
                {{csrf_field()}}

                <input type="hidden" name="token" value="{{ $token }}">

                <!-- E-Mail Address -->
                <input type="email" class="form-control" name="email" placeholder="Email" value="{{ $email or old('email') }}" autofocus>

                @if ($errors->has('email'))
                    <div class="alert alert-danger">
                        {{ $errors->first('email') }}
                    </div>
                @endif

            <!-- Password -->
                <input type="password" class="form-control" name="password" placeholder="Password">

                @if ($errors->has('password'))
                    <div class="alert alert-danger">
                        {{ $errors->first('password') }}
                    </div>
                @endif

            <!-- Password Confirmation -->
                <input type="password" class="form-control" name="password_confirmation"
                       placeholder="{{__('Confirm Password')}}">
                @if ($errors->has('password_confirmation'))
                    <div class="alert alert-danger">
                        {{ $errors->first('password_confirmation') }}
                    </div>
                @endif

                <button type="submit" class="btn btn-block btn-primary">Reset</button>
            </form>
        </div>
    </div>
@endsection
