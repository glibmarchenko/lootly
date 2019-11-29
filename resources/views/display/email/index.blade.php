@extends('layouts.app')

@section('title', 'Email Notifications')

@section('content')
<div id="email-page" class="">
        
    <div class="row m-t-20 p-b-10 section-border-bottom">
        <div class="col-md-12">
            <h3 class="page-title m-t-0 color-dark">Email Notifications</h3>
        </div>
    </div>
    <div class="row p-t-25 m-b-20 p-b-25 section-border-bottom">
        <div class="col-md-5 col-12">
            <h5 class="bolder m-b-15">Points</h5>
            <p class="m-b-0">Customize how customers are notified when they interact with your rewards program.</p>
        </div>
        <div class="col-md-7 col-12">
            <div class="well bg-white p-t-20 p-b-20 p-l-20 p-r-20">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group m-b-0">
                            <i class="icon-points m-r-10 v-a-t m-t-15"></i> 
                            <label class="m-b-0 m-t-5 label-right">
                                <span class="bold">Points Earned</span>
                                <span class="d-block">Sent when customer completes an earn points action.</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2 text-right m-t-5">
                        <a class="bold color-blue f-s-15" href="{{ route('display.email.points.earned') }}">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
            <div class="well bg-white m-t-15 p-t-20 p-b-20 p-l-20 p-r-20">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group m-b-0">
                            <i class="icon-points m-r-10 v-a-t m-t-15"></i> 
                            <label class="m-b-0 m-t-5 label-right">
                                <span class="bold">Points Spent</span>
                                <span class="d-block">Sent when customer redeems points for a reward.</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2 text-right m-t-5">
                        <a class="bold color-blue f-s-15" href="{{ route('display.email.points.spent') }}">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
            <div class="well bg-white m-t-15 p-t-20 p-b-20 p-l-20 p-r-20">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group m-b-0">
                            <i class="icon-gift f-s-26 m-r-10 v-a-t m-t-15"></i> 
                            <label class="m-b-0 m-t-5 label-right">
                                <span class="bold">Reward Available</span>
                                <span class="d-block">Sent when customer has enough points for a reward.</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2 text-right m-t-5">
                        <a class="bold color-blue f-s-15" href="{{ route('display.email.points.reward-available') }}">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
            <div class="well bg-white m-t-15 p-t-20 p-b-20 p-l-20 p-r-20">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group m-b-0">
                            <i class="icon-points m-r-10 v-a-t m-t-15"></i> 
                            <label class="m-b-0 m-t-5 label-right">
                                <span class="bold">Point Expiration Reminder</span>
                                <span class="d-block">Sent when customer's point balance is expiring soon.</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2 text-right m-t-5">
                        <a class="bold color-blue f-s-15" href="{{ route('display.email.points.point-expiration') }}">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
            <div class="well bg-white m-t-15 p-t-20 p-b-20 p-l-20 p-r-20">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group m-b-0">
                            <i class="icon-vip f-s-20 m-r-10 v-a-t m-t-15"></i> 
                            <label class="m-b-0 m-t-5 label-right">
                                <span class="bold">VIP Tier Earned</span>
                                <span class="d-block">Sent when customer unlocks new VIP tier.</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2 text-right m-t-5">
                        <a class="bold color-blue f-s-15" href="{{ route('display.email.points.vip-tier-earned') }}">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row p-t-25 m-b-20 p-b-25 section-border-bottom">
        <div class="col-md-5 col-12">
            <h5 class="bolder m-b-15">Referral</h5>
            <p class="m-b-0">Customize how customers are notified when they interact with your referral program.</p>
        </div>
        <div class="col-md-7 col-12">
            <div class="well bg-white p-t-20 p-b-20 p-l-20 p-r-20">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group m-b-0">
                            <i class="icon-heart m-r-10 v-a-t m-t-15"></i> 
                            <label class="m-b-0 m-t-5 label-right">
                                <span class="bold">Referral Share Email</span>
                                <span class="d-block">Sent from your customers to their friends.</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2 text-right m-t-5">
                        <a class="bold color-blue f-s-15" href="{{ route('display.email.referral.share-email') }}">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
            <div class="well bg-white m-t-15 p-t-20 p-b-20 p-l-20 p-r-20">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group m-b-0">
                            <i class="icon-heart m-r-10 v-a-t m-t-15"></i> 
                            <label class="m-b-0 m-t-5 label-right">
                                <span class="bold">Referral Receiver Reward</span>
                                <span class="d-block">Sent to a customer when they claim their reward.</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2 text-right m-t-5">
                        <a class="bold color-blue f-s-15" href="{{ route('display.email.referral.receiver-reward') }}">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
            <div class="well bg-white m-t-15 p-t-20 p-b-20 p-l-20 p-r-20">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group m-b-0">
                            <i class="icon-heart m-r-10 v-a-t m-t-15"></i> 
                            <label class="m-b-0 m-t-5 label-right">
                                <span class="bold">Referral Sender Reward</span>
                                <span class="d-block">Sent to the sender of the referral with their discount..</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2 text-right m-t-5">
                        <a class="bold color-blue f-s-15" href="{{ route('display.email.referral.sender-reward') }}">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row m-b-30">
        <div class="col-md-5 col-12">
            <h5 class="bolder m-b-10">Settings</h5>
            <p class="m-b-0">Customize how your logo and company information is displayed within your emails.</p>
        </div>
        <div class="col-md-7 col-12">
            <div class="well bg-white p-t-20 p-b-20 p-l-20 p-r-20">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group m-b-0">
                            <label class="bold m-b-0 m-t-5">
                                <i class="icon-gear f-s-28 m-r-10"></i> Email Settings 
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2 text-right m-t-5">
                        <a class="bold color-blue f-s-15" href="{{ route('display.email.settings') }}">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
