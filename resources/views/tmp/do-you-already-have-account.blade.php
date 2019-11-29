@extends('tmp.app')

@section('title', 'Select Account')

@section('content')
    <div class="">

        <div class="row m-t-20 p-b-10 section-border-bottom">
            <div class="col-md-12">
                <h3 class="page-title m-t-0 color-dark">Do you already have Lootly account?</h3>
            </div>
        </div>
        <div class="row p-t-25 m-b-20 p-b-25 section-border-bottom">
            <div class="col-md-12">
                <a href="{{ route('login') }}" class="btn btn-primary btn-large">Yes</a>
                <a href="{{ (session('redirect_queue') && count(session('redirect_queue'))) ? (session('redirect_queue')[0].(strpos(session('redirect_queue')[0], '?') === false ? '?' : '&')."new-user=1") : route('register')}}"
                   class="btn btn-default btn-large">No</a>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

@endsection