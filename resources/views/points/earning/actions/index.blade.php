@extends('layouts.app')

@section('title', 'Earning Actions')

@section('content')
    <div id="points-settings" class="">

        <div class="row m-t-20 p-b-10 section-border-bottom">
            <div class="col-md-12 m-b-15">
                <a href="{{ route('points.earning') }}" class="bold f-s-15 color-blue">
                    <i class="arrow left blue"></i>
                    <span class="m-l-5">Earning Actions</span>
                </a>
            </div>
            <div class="col-md-12">
                <h3 class="page-title m-t-0 color-dark">Add Earning Action</h3>
            </div>
        </div>
        @foreach($actionTypes as $actionType)
            {{--@if(!count($actionType->merchantAction))--}}
            <div class="row p-t-25 p-b-25 section-border-bottom">
                <div class="col-md-5 col-12">
                    <h5 class="bolder m-b-15">{{$actionType->type}}</h5>
                    <p class="m-b-0">{{$actionType->description}}</p>
                </div>
                <div class="col-md-7 col-12">

                    @foreach($actions as $action)
                        @if($action->type == $actionType->type)
                            <div class="well bg-white m-t-15 p-t-20 p-b-20 p-l-20 p-r-20">
                                <div class="row">
                                @if($actionType->url == 'read-content' && !$merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.ReadContent')))
                                    <div class="col-sm-6">
                                        <div class="form-group m-b-0">
                                            <label class="light-font m-b-0 m-t-10">
                                                @if($action->icon=='spend')
                                                    <i class="icon-goal-spend m-r-10" style="font-size: 27px;"></i>{{$action->name}}
                                                @else
                                                    <i class="{{$action->icon}} m-r-10" style="font-size: 27px;"></i>{{$action->name}}
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 text-right">
                                        <a href="/account/upgrade" class="btn upgrade-plan-btn">Upgrade to {!! $readContentUpsell->getMinPlan()->name !!}</a>
                                    </div>

                                @elseif($actionType->url == 'trustspot-review' && !$merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.TrustSpotReview')))
                                    <div class="col-sm-6">
                                        <div class="form-group m-b-0">
                                            <label class="light-font m-b-0 m-t-10">
                                                @if($action->icon=='spend')
                                                    <i class="icon-goal-spend m-r-10" style="font-size: 27px;"></i>{{$action->name}}
                                                @else
                                                    <i class="{{$action->icon}} m-r-10" style="font-size: 27px;"></i>{{$action->name}}
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 text-right">
                                        <a href="/account/upgrade" class="btn upgrade-plan-btn">Upgrade to {!! $trustSpotUpsell->getMinPlan()->name !!}</a>
                                    </div>
                                @else
                                    <div class="col-sm-8">
                                        <div class="form-group m-b-0">
                                            <label class="light-font m-b-0 m-t-5">
                                                @if($action->icon=='spend')
                                                    <i class="icon-goal-spend m-r-10" style="font-size: 27px;"></i>{{$action->name}}
                                                @else
                                                    <i class="{{$action->icon}} m-r-10" style="font-size: 27px;"></i>{{$action->name}}
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 text-right m-t-5">
                                        <a class="bold color-blue f-s-15"
                                        href="{{ route('points.earning.actions.'.$action->url) }}">
                                            <i class="icon-add f-s-19 m-r-5"></i>Add Action
                                        </a>
                                    </div>
                                @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            {{--@endif--}}
        @endforeach
    </div>
@endsection
