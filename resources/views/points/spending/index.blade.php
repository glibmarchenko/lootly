@extends('layouts.app')

@section('title', 'Points Spending')

@section('content')
    <div class="points-earning p-b-40">
        <div class="row m-t-20 p-b-10 section-border-bottom">
            <div class="col-md-6 col-12">
                <h3 class="page-title m-t-0 color-dark">Spending Rewards</h3>
            </div>
            <div class="col-md-6 col-12 text-right">
                <a href="{{ route('points.spending.rewards') }}" class="btn btn-success btn-glow p-l-20 p-r-20">Add
                    Reward</a>
            </div>
        </div>

        <div class="row m-t-25">
            <div class="col-md-12">
                <div class="well well-table">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr class="f-s-15">
                                <th class="bold color-dark-grey">Name</th>
                                <th class="bold color-dark-grey">Reward Type</th>
                                <th class="bold color-dark-grey">Points Required</th>
                                <th class="bold color-dark-grey">Reward Count</th>
                                <th class="bold color-dark-grey">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($spendingRewards))
                                @foreach($spendingRewards as $spendingReward)
                                    <tr>
                                        <td>
                                            <a 
                                                class="bold color-blue discount
                                                    @if(!$have_rewards_permissions && $spendingReward->reward->slug == 'variable-amount')
                                                    disabled
                                                    @endif
                                                "
                                                href="{{ route('points.spending.actions.'.$spendingReward->reward->url,['id' => $spendingReward->id]) }}"
                                            >
                                                @if($spendingReward->reward_icon)
                                                    <img class="m-r-10" src="{{$spendingReward->reward_icon}}" style="max-width: 30px;">
                                                @else
                                                    @if($spendingReward->reward->icon == 'spend')
                                                        <span class="icon-goal-spend m-r-10"></span>
                                                    @else
                                                        <span class="{{$spendingReward->reward->icon}} m-r-10"></span>
                                                    @endif
                                                @endif
                                            <input type="hidden" class="discount_name"
                                                   value="{{$spendingReward->reward_name}}">
                                                {{$spendingReward->reward_name}}
                                            </a>
                                        </td>
                                        <td> {{$spendingReward->reward_type}}</td>
                                        <td> {{$spendingReward->points_required}}</td>
                                        <td> {{$spendingReward->point->count()}}</td>
                                        <td class="">
                                            @if($spendingReward->active_flag == 0)
                                                <span class="badge badge-pill badge-danger p-l-20 p-r-20">Disabled</span>
                                            @else
                                                <span class="badge badge-pill badge-success p-l-20 p-r-20">Enabled</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>

        var page = new Vue({
            data: {

            },
            created: function() {
                $('.discount').on('click', function (e) {
                    let name = $(this).find('.discount_name').val();
                    window.sessionStorage.removeItem('discount_name');
                    window.sessionStorage.setItem('discount_name',name);
                });

                $("a.disabled").click((event) => {
                    event.preventDefault();
                    this.showUpsell();
                });

                if("{{session('show_upsell')}}" === '1'){
                    this.showUpsell();
                }
            },
            methods: {
                showUpsell: function() {
                    swal({
                        className: "upgrade-swal",
                        title: "{!!$discount_upsell->upsell_title!!}",
                        text: "{!!$discount_upsell->upsell_text!!}",
                        icon: "/images/permissions/{!!$discount_upsell->upsell_image!!}",
                        buttons: {
                            catch: {
                                text: "Upgrade to {!!$discount_upsell->getMinPlan()->name!!}",
                                value: "upgrade",
                            }
                        }
                    })
                    .then((response) => {
                        if(response.value === 'upgrade') {
                            window.location.href = '/account/upgrade';
                        }
                    });
                },
                noAccessClick: function(event){
                    // ...
                }
            }
        });
    </script>
@endsection
<?php session(['show_upsell' => false]) ?>