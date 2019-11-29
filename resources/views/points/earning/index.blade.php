@extends('layouts.app')

@section('title', 'Points Earning')

@section('content')
    <div id="points-earning" class="points-earning p-b-40">
        <div class="row m-t-20 p-b-10 section-border-bottom">
            <div class="col-md-6 col-12">
                <h3 class="page-title m-t-0 color-dark">Earning Actions</h3>
            </div>
            <div class="col-md-6 col-12 text-right">
                <a href="{{route('points.earning.actions')}}" class="btn btn-success btn-glow p-l-20 p-r-20">Add Action</a>
            </div>
        </div>

        <div class="row m-t-25">
            <div class="col-md-12">
                <div class="well well-table table-responsive">
                    <table class="table">
                        <thead>
                        <tr class="f-s-15">
                            <th class="bold  color-dark-grey">Name</th>
                            <th class="bold  color-dark-grey">Action Type</th>
                            <th class="bold  color-dark-grey">Reward</th>
                            <th class="bold  color-dark-grey">Reward Count</th>
                            <th class="bold  color-dark-grey">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($earningActions))
                            @foreach($earningActions as $earningAction)
                                <tr>
                                    <td>
                                        <a 
                                            href="{{ route('points.earning.actions.'.$earningAction->action->url) }}?edit=1{{ ( $earningAction->action->url == 'custom-earning' || $earningAction->action->url == 'trustspot-review' ) ? '&id='.$earningAction->id : '' }}"
                                            class="bold color-blue
                                            @if(($earningAction->action->url == 'trustspot-review' && !$merchant->checkPermitionByTypeCode('TrustSpotReview'))
                                                || ($earningAction->action->url == 'read-content' && !$merchant->checkPermitionByTypeCode('ReadContent')))
                                                
                                                disabled"
                                                @if($earningAction->action->url == 'read-content')
                                                    @click="showReadContentUpsell"
                                                @else
                                                    @click="showTrustSpotUpsell"
                                                @endif
                                            @else
                                                "
                                            @endif
                                            >
                                            @if($earningAction->action_icon)
                                                <img class="m-r-10" src="{{$earningAction->action_icon}}" style="max-width: 30px;">
                                            @else
                                                @if($earningAction->action->icon == 'spend')
                                                    <span class="icon-goal-spend m-r-10"></span>
                                                @else
                                                    <span class="{{$earningAction->action->icon}} m-r-10"></span>
                                                @endif
                                            @endif
                                            {{$earningAction->action_name}}
                                        </a>
                                    </td>
                                    <td> {{$earningAction->action->name}}</td>
                                    <td>
                                        @if($points_settings)
                                            @if($earningAction->point_value > 1)
                                                {{$earningAction->point_value . " " . $points_settings->plural_name}}
                                            @else
                                                {{$earningAction->point_value . " " . $points_settings->name}}
                                            @endif
                                        @else
                                            {{$earningAction->reward_text}}
                                        @endif
                                    </td>
                                    <td class="bolder"> {{ $earningAction->point->count() }} </td>
                                    <td class="">
                                        @if($earningAction->active_flag == 0)
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
@endsection

@section('scripts')
    <script>

        var page = new Vue({
            el:"#points-earning",
            data: {

            },
            created: function() {
                // $("a.disabled").click((event) => {
                //     event.preventDefault();
                //     this.showUpsell();
                // });

                if("{{session('show_read_content_upsell')}}" === '1'){
                    this.showReadContentUpsell();
                }
                if("{{session('show_trust_spot_upsell')}}" === '1'){
                    this.showTrustSpotUpsell();
                }
            },
            methods: {
                showReadContentUpsell: function(e = null) {
                    if(e != null){
                        e.preventDefault();
                    }
                    swal({
                        className: "upgrade-swal",
                        title: "{!!$readContentUpsell->upsell_title!!}",
                        text: '{!!$readContentUpsell->upsell_text!!}',
                        icon: "/images/permissions/{!!$readContentUpsell->upsell_image!!}",                        
                        buttons: {
                            catch: {
                                text: "Upgrade to {!!$readContentUpsell->getMinPlan()->name!!}",
                                value: "upgrade",
                            }
                        },
                    })
                    .then((response) => {
                        if(response === 'upgrade') {
                            window.location.replace('/account/upgrade');
                        }
                    });
                },
                showTrustSpotUpsell: function(e = null) {
                    if(e != null){
                        e.preventDefault();
                    }
                    swal({
                        className: "upgrade-swal",
                        title: "{!!$trustSpotUpsell->upsell_title!!}",
                        text: "{!!$trustSpotUpsell->upsell_text!!}",
                        icon: "/images/permissions/{!!$trustSpotUpsell->upsell_image!!}",
                        buttons: {
                            catch: {
                                text: "Upgrade to {!!$trustSpotUpsell->getMinPlan()->name!!}",
                                value: "upgrade",
                            }
                        },
                    }).then((response) => {
                        if(response === 'upgrade') {
                            window.location.replace('/account/upgrade');
                        }
                    });
                    // swal({
                    //     title: "{!!$trustSpotUpsell->upsell_title!!}",
                    //     html: '<div class="m-r-15 m-l-15">{!!$trustSpotUpsell->upsell_text!!}</div>',
                    //     imageUrl: "/images/permissions/{!!$trustSpotUpsell->upsell_image!!}",
                    //     imageHeight: "80",
                    //     confirmButtonText: "Upgrade to {!!$trustSpotUpsell->getMinPlan()->name!!}",
                    //     confirmButtonColor: "#15aa3e",
                    //     showCloseButton: true,
                    // })
                    // .then((response) => {
                    //     if(response.value === true) {
                    //         window.location.href = '/account/upgrade';
                    //     }
                    // });
                },
            }
        });
    </script>
@endsection
<?php 
    session(['show_read_content_upsell' => false]);
    session(['show_trust_spot_upsell' => false]);
?>
