@extends('layouts.app')

@section('title', 'Spending Actions')

@section('content')
    <div id="points-settings" class="">

        <div class="row m-t-20 p-b-10 section-border-bottom">
            <div class="col-md-12 m-b-15">
                <a href="{{ route('points.spending') }}" class="bold f-s-15 color-blue">
                    <i class="arrow left blue"></i>
                    <span class="m-l-5">Spending Rewards</span>
                </a>
            </div>
            <div class="col-md-12">
                <h3 class="page-title m-t-0 color-dark">Add Spending Reward</h3>
            </div>
        </div>
        <div class="row p-t-25 m-b-20">
            <div class="col-md-5 col-12">
                <h5 class="bolder m-b-15">Shopify Rewards</h5>
                <p class="m-b-0">These rewards are given to your customers after they meet the point requirement for
                    redemption</p>
            </div>
            <div class="col-md-7 col-12">
                @foreach($rewards as $reward)
                    @if(in_array($reward->type, $spendingRewardArr) && ($reward->type == 'Variable amount' && $has_discount_permissions))
                        @elseif($reward->type !== 'points')
                        <div class="well bg-white p-t-20 p-b-20 p-l-20 p-r-20 m-t-15">
                            <div class="row">
                            @if(!$has_discount_permissions && $reward->slug == "variable-amount")
                                <div class="col-sm-6">
                                    <div class="form-group m-b-0">
                                            <label class="light-font m-b-0 m-t-10">
                            @else
                                <div class="col-sm-8">
                                        <div class="form-group m-b-0">
                                            <label class="light-font m-b-0 m-t-5">
                            @endif
                                            <i class="{{$reward->icon}} m-r-10"></i> {{$reward->name}}
                                        </label>
                                    </div>
                                </div>
                                @if(!$has_discount_permissions && $reward->slug == "variable-amount")
                                <div class="col-sm-6 text-right">
                                    <a href="/account/upgrade" class="btn upgrade-plan-btn rewards">Upgrade to {{$discount_upsell->getMinPlan()->name}}</a>
                                </div>
                                @else
                                    <div class="col-sm-4 text-right m-t-5">
                                        <a class="bold color-blue f-s-15 add-discount"
                                        href="{{ route('points.spending.actions.'.$reward->url) }}">
                                            <i class="icon-add f-s-19 m-r-5"></i> Add Reward
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('.add-discount').on('click', function (e) {

            let name = $(this).find('.discount_name').val();
            window.sessionStorage.removeItem('discount_name');
            window.sessionStorage.setItem('discount_name', 'all');

        })
    </script>
@endsection