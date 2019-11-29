@extends('layouts.app')

@section('title', 'Points')

@section('content')
    <div class="points-overview p-b-40" id="overview">
        <div class="row m-t-20 p-b-10 section-border-bottom">
            <div class="col-md-6 col-12">
                <h3 class="page-title m-t-0 color-dark">Points Overview</h3>
            </div>
        </div>
        <div class="row m-t-20">
            <div class="col-md-6 m-t-5">
                <div class="well well-table table-responsive h-100">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="color-dark-grey f-s-17">Top Earning Actions</th>
                            <th class="text-right">
                                <a href="/reports/overview?tabId=3" class="bolder f-s-14 color-blue">View All</a>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="f-s-15">
                        @if(isset($points))
                            @foreach($points as $point)
                                <tr>
                                    <td>
                                        <span class="{{$point->action_icon_name}} m-r-10"></span>
                                        {{$point->action_name}}
                                    </td>
                                    <td class="text-right">{{($point->actions_num)? $point->actions_num : 0}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6 m-t-5">
                <div class="well well-table">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="color-dark-grey f-s-17">Top Spending Rewards</th>
                            <th class="text-right">
                                <a href="/reports/overview?tabId=4" class="bolder f-s-14 color-blue">View All</a>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="f-s-15">
                        @if(isset($rewards))
                            @foreach($rewards as $reward)
                                <tr>
                                    <td>
                                        <span class="{{$reward->reward_icon_name}} m-r-10"></span>
                                        {!! $reward->reward_name !!}
                                    </td>
                                    <td class="text-right">{{($reward->actions_num)? $reward->actions_num : 0}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row m-t-25">
            <div class="col-md-12">
                <div class="well well-table">
                    <div class="table-header">
                        <p class="bold color-dark-grey f-s-17 pull-left">Latest Points Activity</p>
                        <p class="pull-right">
                            <a class="bolder color-blue f-s-14" href="/points/activity">View All</a>
                        </p>
                    </div>

                    <table class="table">
                        <thead>
                        <tr class="f-s-15">
                            <th class="bold color-dark-grey">Customer Name</th>
                            <th class="bold color-dark-grey">Activity</th>
                            <th class="bold color-dark-grey">Points</th>
                            <th class="bold color-dark-grey">Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($activity))
                            @foreach($activity as $point)
                                <tr>
                                    <td>
                                        <a class="bold color-blue f-s-15" href="{{route('customers.show', $point->customer->id)}}">{{$point->customer->name}}</a>
                                    </td>
                                    <td>{!! $point->getActionName() !!}</td>
                                    <td>
                                        @if($point->point_value > 0)
                                            <span class="badge badge-pill badge-success">+{{$point->point_value}}</span>
                                        @elseif($point->point_value < 0) 
                                            <span class="badge badge-pill badge-danger">{{$point->point_value}}</span>
                                        @else
                                            <span class="badge badge-pill">{{$point->point_value}}</span>
                                        @endif
                                    </td>
                                    <td>@{{"{!! $point->created_at !!}" | date-human}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
new Vue({
    el: "#overview",
});
</script>
@endsection