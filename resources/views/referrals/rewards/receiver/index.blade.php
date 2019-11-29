@extends('layouts.app')

@section('title', 'Receiver Reward')

@section('content')
    <div id="rewards-page" class="">

        <div class="row m-t-20 p-b-10 section-border-bottom">
            <div class="col-md-12 m-b-15">
                <a href="{{ route('referrals.reward') }}" class="bold f-s-15 color-blue">
                    <i class="arrow left blue"></i>
                    <span class="m-l-5">Rewards</span>
                </a>
            </div>
            <div class="col-md-12">
                <h3 class="page-title m-t-0 color-dark">Receiver Reward</h3>
            </div>
        </div>
        <div class="row p-t-25 m-b-30">
            <div class="col-md-5 col-12">
                <h5 class="bolder m-b-15">Shopify Rewards</h5>
                <p class="m-b-0">These rewards are given to your referral receiver once their referred person makes a
                    purchase.</p>
            </div>
            <div class="col-md-7 col-12">
                @foreach($rewards as $reward)
                    @if($reward->type!='Variable amount' && $reward->type!='points')

                        <div class="well bg-white m-t-15 p-t-20 p-b-20 p-l-20 p-r-20">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group m-b-0">
                                        <label class="light-font m-b-0 m-t-5">
                                            <i class="{{$reward->icon}} m-r-10"></i> {{$reward->name}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 text-right m-t-5">
                                    <a class="bold color-blue f-s-15" href="{{ route('referrals.rewards.receiver.'.$reward->url) }}">
                                        <i class="icon-add f-s-19 m-r-5"></i> Add Reward
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

    </div>
@endsection
