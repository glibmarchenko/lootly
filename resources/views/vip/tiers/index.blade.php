@extends('layouts.app')

@section('title', 'VIP Tiers')

@section('content')
    <div id="tiers-page" class="m-t-20 m-b-10">
        <div class="row m-t-20 p-b-10 section-border-bottom">
            <div class="col-md-6 col-6">
                <h3 class="page-title m-t-0 color-dark">VIP Tiers</h3>
            </div>
        </div>
        @foreach($tiers as $tier)
            <div class="row p-t-25 p-b-25 section-border-bottom">
                <div class="col-md-5 col-12">
                    <h5 class="bolder m-b-15">{{$tier->name}}</h5>
                    <p class="m-b-0">{{$tier->requirement_text}}</p>
                </div>
                <div class="col-md-7 col-12">
                    <div class="well bg-white p-l-0 p-r-0">
                        <div class="border-bottom p-b-10">
                            <div class="col-md-12">
                                <div class="form-group m-b-0">
                                    @if(!$tier->image_url)
                                    <span style="min-width: 45px; color: {{$tier->default_icon_color}}" class="icon-vip f-s-25 m-r-10"></span>
                                    @else
                                    <img style="max-width: 45px;" src="{{$tier->image_url}}" alt="" class="m-r-10">
                                    @endif
                                    <label class="bolder m-b-0 m-t-0">
                                        {{ $tier->customer->count() }} members
                                    </label>
                                    <a href="{{ route('vip.tiers.edit', ['id'=>$tier->id])}}"
                                        class="bolder f-s-14 color-blue pull-right">Edit Tier</a>
                                </div>
                            </div>
                        </div>
                        <div class="row p-l-25 p-r-25">
                            <div class="col-md-12">
                                <label class="bolder m-t-10">Benefits</label>
                                <div class="benefits-list-reward">
                                    <ul class="benefits-list">
                                        <li>
                                            {{ str_replace(['{points}', '{points-name}', '{currency}'], [$tier->multiplier * $makePurchasePoints, $merchant->points_settings->plural_name ?? 'Points', $tier->currency], $tier->multiplier_text_default) }}
                                        </li>
                                    </ul>
                                </div>
                                <?php
                                    $entryRewards = $tier->tierBenefits->filter(function ($tierReward, $key){
                                        return ($tierReward->benefits_type == 'entry');
                                    });
                                    $lifetimeRewards = $tier->tierBenefits->filter(function ($tierReward, $key){
                                        return ($tierReward->benefits_type == 'lifetime');
                                    });
                                    $customRewards = $tier->tierBenefits->filter(function ($tierReward, $key){
                                        return ($tierReward->benefits_type == 'custom');
                                    });
                                ?>
                                    @if($entryRewards->count())
                                        <strong>Entry Rewards</strong>
                                        <div class="benefits-list-reward">
                                        @foreach($entryRewards as $reward)
                                            <ul class="benefits-list">
                                                @if($reward->benefits_reward == 'Free Shipping')
                                                    <li>
                                                        {{$reward->benefits_reward .'  '.$reward->benefits_discount}} Amount
                                                    </li>
                                                @elseif($reward->benefits_reward == 'Free Product')
                                                    <li>
                                                        {{$reward->benefits_reward.' '.$reward->benefits_discount}}
                                                    </li>
                                                @elseif($reward->benefits_reward == 'points')
                                                    <li>
                                                        {{$reward->benefits_discount}} Free Points
                                                    </li>
                                                @else
                                                    <li>
                                                        {{$reward->getRewardName()}}
                                                    </li>
                                                @endif
                                            </ul>
                                        @endforeach
                                        </div>
                                    @endif

                                    @if($lifetimeRewards->count())
                                        <strong>Lifetime Rewards</strong>
                                        <div class="benefits-list-reward">
                                        @foreach($lifetimeRewards as $reward)
                                            <ul class="benefits-list">
                                                @if($reward->benefits_reward == 'Free Shipping')
                                                    <li>
                                                        {{$reward->benefits_reward .'  '.$reward->benefits_discount}} Amount
                                                    </li>
                                                @elseif($reward->benefits_reward == 'Free Product')
                                                    <li>
                                                        {{$reward->benefits_reward.' '.$reward->benefits_discount}}
                                                    </li>
                                                @else
                                                    <li>
                                                        {{$reward->getRewardName()}}                                                        
                                                    </li>
                                                @endif
                                            </ul>
                                        @endforeach
                                        </div>
                                    @endif

                                    @if($customRewards->count())
                                        <strong >Custom Rewards</strong>
                                        <div class="benefits-list-reward">
                                        @foreach($customRewards as $reward)
                                            <ul class="benefits-list">
                                                @if($reward->benefits_reward == 'Free Shipping')
                                                    <li>
                                                        {{$reward->benefits_reward .'  '.$reward->benefits_discount}} Amount
                                                    </li>
                                                @elseif($reward->benefits_reward == 'Free Product')
                                                    <li>
                                                        {{$reward->benefits_reward.' '.$reward->benefits_discount}}
                                                    </li>
                                                @else
                                                    <li>
                                                        {{$reward->currency.' '.$reward->benefits_discount}}
                                                    </li>
                                                @endif
                                            </ul>
                                        @endforeach
                                        </div>
                                    @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="row p-t-25 p-b-25">
            <div class="col-md-5 col-12">
                <h5 class="bolder m-b-15">New VIP Tier</h5>
                <p class="m-b-0"> Create a new VIP Tier to reward your loyal customers with additional perks and
                    discounts.</p>

            </div>
            <div class="col-md-7 col-12">
                <div class="well bg-white">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group m-b-0">
                                <i class="icon-vip f-s-25 m-r-10"></i>
                                <a href="{{ route('vip.tiers.add')}}" class="bolder f-s-14 color-blue pull-right">Add
                                    Tier</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        var page = new Vue({
            el: "#tiers-page"
        })
    </script>
@endsection