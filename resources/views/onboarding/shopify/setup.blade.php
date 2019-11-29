@extends('onboarding.layout')

@section('title', 'Shopify')

@section('content')
    <div class="shopify-main">
        <div class="well p-l-0 p-r-0 p-b-20 text-center">
            <div class="loader" v-cloak>
                <div class="wizard-wrapper">
                    <span v-if="stepIndex == 1" >
                        <img class="m-t-10" src="{{ asset('images/icons/treasure-chest.png') }}" width="120">
                        <h1 class="bold m-t-40">
                            Welcome to <span style="color: #0279b7">Lootly</span>
                        </h1>

                        <p class="m-t-20 f-s-17">We're glad to have you with us. Setup should take less than 1 minute, let's get started.</p>

                        <button class="btn btn-primary m-t-40 m-b-20" @click="nextStep">Get Started</button>
                    </span>
                    <span v-if="stepIndex == 2">
                        <h3 class="bold m-t-5 m-b-20">What actions can customers complete to get points?</h3>

                        <p class="tagline">Having more actions has been shown to increase engagement. We've pre-selected the most common ones for you.</p>

                        <p class="bolder text-left m-t-30">My Selected Actions:</p>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="action-item">
                                    <i class="icon-gift m-r-15 pull-left"></i>
                                    <p class="bold pull-left">Make a Purchase</p>
                                </div>
                                <div class="action-item">
                                    <i class="icon-facebook m-r-15 pull-left"></i>
                                    <p class="bold pull-left">Facebook Share</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="action-item">
                                    <i class="icon-create-account m-r-15 pull-left"></i>
                                    <p class="bold pull-left">Create an Account</p>
                                </div>
                                <div class="action-item">
                                    <i class="icon-birthday m-r-15 pull-left"></i>
                                    <p class="bold pull-left">Celebrate a Birthday</p>
                                </div>
                            </div>
                        </div>

                        <p class="bolder border-top text-left p-t-20 m-t-40">Other Great Actions:</p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="action-item">
                                    <i class="icon-cart m-r-15 pull-left"></i>
                                    <p class="bold pull-left">Goal Spend</p>
                                </div>
                                <div class="action-item">
                                    <i class="icon-facebook m-r-15 pull-left"></i>
                                    <p class="bold pull-left">Facebook Share</p>
                                </div>
                                <div class="action-item">
                                    <i class="icon-twitter m-r-15 pull-left"></i>
                                    <p class="bold pull-left">Twitter Share</p>
                                </div>
                                <div class="action-item">
                                    <i class="icon-star m-r-15 pull-left"></i>
                                    <p class="bold pull-left">Product Review</p>
                                    <span class="badge pull-right">Premium</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="action-item">
                                    <i class="icon-goal-orders m-r-15 pull-left"></i>
                                    <p class="bold pull-left">Goal Orders</p>
                                </div>
                                <div class="action-item">
                                    <i class="icon-twitter m-r-15 pull-left"></i>
                                    <p class="bold pull-left">Twitter Follow</p> 
                                </div>
                                <div class="action-item">
                                    <i class="icon-content m-r-15 pull-left"></i>
                                    <p class="bold pull-left">Read Content</p> 
                                </div>
                                <div class="action-item">
                                    <i class="icon-gear m-r-15 pull-left"></i>
                                    <p class="bold pull-left">Custom Actions</p> 
                                    <span class="badge pull-right">Premium</span>
                                </div>
                            </div>
                        </div>
                    </span>
                    <span v-if="stepIndex == 3">
                        <h3 class="bold m-t-5 m-b-20">What type of rewards can customers redeem points for?</h3>

                        <p class="tagline">Offering a variety of rewards has been shown to help increase spending, engagement and retention. We've pre-selected the most common ones for you.</p>

                        <p class="bolder text-left m-t-30">My Selected Rewards:</p>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="detailed-action-item">
                                    <i class="icon-gift m-r-15 pull-left"></i>
                                    <div class="pull-left">
                                        <p class="bold">$5 off discount</p>
                                        <p class="">500 Points</p>
                                    </div>
                                </div>
                                <div class="detailed-action-item">
                                    <i class="icon-gift m-r-15 pull-left"></i>
                                    <div class="pull-left">
                                        <p class="bold">$15 off discount</p>
                                        <p class="">1500 Points</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detailed-action-item">
                                    <i class="icon-gift m-r-15 pull-left"></i>
                                    <div class="pull-left">
                                        <p class="bold">$10 off discount</p>
                                        <p class="">1000 Points</p>
                                    </div>
                                </div>
                                <div class="detailed-action-item">
                                    <i class="icon-gift m-r-15 pull-left"></i>
                                    <div class="pull-left">
                                        <p class="bold">$25 off discount</p>
                                        <p class="">2500 Points</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="bolder border-top text-left p-t-20 m-t-40">Other Reward Options:</p>
                        <p class="text-left m-t-5 m-b-5">Lootly supports 5 different types of rewards, below are the options you can add after setup is done.</p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="action-item">
                                    <i class="icon-coin m-r-15 pull-left"></i>
                                    <p class="bold pull-left">Fixed Amount Discount</p>
                                </div>
                                <div class="action-item">
                                    <i class="icon-percentage m-r-15 pull-left"></i>
                                    <p class="bold pull-left">Percentage Off Discount</p>
                                </div>
                                <div class="action-item">
                                    <i class="icon-gift m-r-15 pull-left"></i>
                                    <p class="bold pull-left">Free Product</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="action-item">
                                    <i class="icon-coin m-r-15 pull-left"></i>
                                    <p class="bold pull-left">Variable Amount Discount</p>
                                    <span class="badge pull-right">Premium</span>
                                </div>
                                <div class="action-item">
                                    <i class="icon-package m-r-15 pull-left"></i>
                                    <p class="bold pull-left">Free Shipping</p> 
                                </div>
                            </div>
                        </div>
                    </span>
                    <span v-if="stepIndex == 4">
                        <h3 class="bold m-t-5 m-b-20">How Lootly will appear to your customers</h3>
                        <p class="tagline">Loolty has been successfully added to your store, below is an example of the rewards widget.</p>
                        <div class="text-left border-bottom p-b-20 m-t-30">
                            <button class="btn btn-tab">Rewards Program</button>
                        </div>

                        <div class="widget-preview">
                           <button type="button" class="close preview-close">×</button> 
                           <div class="">
                              <div class="widget-preview-content">
                                 <h5 class="bold pull-left m-b-20 f-s-16 text-left"><span>Create a store account to start earning rewards.</span></h5>
                                 <button class="btn btn-tab btn-block f-s-15"><span>Create a Store Account</span></button> 
                                 <div class="row m-t-20 m-b-20">
                                    <div class="col-6 text-left">
                                        <a href="javascript::void(0)" class="bold">Login</a>
                                    </div>
                                    <div class="col-6 text-right">
                                        <a href="javascript::void(0)" class="bold">Learn more</a>
                                    </div>
                                 </div>
                              </div>
                              <div class="m-t-15 text-center">
                                 <div class="light-border-top"><a href=""><img src="/images/logos/logo-inner.png" style="width: 100px; margin: auto; padding-top: 15px;"></a></div>
                              </div>
                           </div>
                        </div>

                    </span>
                    <span v-if="stepIndex == 5">

                        <h1 class="bold m-t-10">
                            Lootly Setup is now Complete
                        </h1>

                        <p class="m-t-20 f-s-17">Be sure to check out our paid plans starting at $10/mo to see all the great features available.</p>

                        <img class="m-t-40" src="{{ asset('images/icons/treasure-chest.png') }}" width="120">

                        <div class="overflow m-t-40 m-b-20">
                            <a class="btn btn-success m-t-5 m-b-5 m-l-5 m-r-5" href="">View Plans</a>
                            <a class="btn btn-primary m-t-5 m-b-5 m-l-5 m-r-5" href="{{ url('/onboarding/shopify?page=login') }}">View Account</a>
                        </div>
                    </span>
                </div>
                <div class="onboarding-wizard-footer" v-if="stepIndex != 1 && stepIndex != 5 " >
                    <p class="pull-left m-t-10 m-b-10">Once setup is done you'll be able to add, remove or customize any of the rewards above.</p>
                    <button class="btn btn-primary m-t-10 pull-right" @click="nextStep">Next</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">
        var app = new Vue({
            el: "#app",
            data: {
                stepIndex: 1
            },
            methods: {
                nextStep: function () {
                    this.stepIndex++;
                    $("html,body").animate({scrollTop: 0 }, 500);
                }
            }
        })
    </script>
@endsection