@extends('website.layout')

@section('title', 'Vip Program')

@section('page-name', 'full-page')

@section('meta')
    <meta name="title" content="VIP Program | Lootly">
    <meta name="description" content="Drive loyalty while simultaneously growing your revenue, and customer happiness.">
    <meta name="keywords" content="vip program, vip rewards">
    <meta property="og:title" content="VIP Program | Lootly">
    <meta property="og:image" content="{{ url('images/assets/main/features/vip-phone.png') }}">
    <meta property="og:url" content="{{ url('features/vip') }}">
    <meta property="og:description" content="Drive loyalty while simultaneously growing your revenue, and customer happiness.">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('content')
    <section class="page-overview">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content text-left">
                        <h1>Incentivize actions with a VIP Program</h1>
                        <p class="description">Drive loyalty while simultaneously growing your revenue, and customer happiness.</p>
                        <div class="btn-wrapper">
                            <a href="{{ url('pricing') }}" class="btn btn-signup f-s-16">Get Started</a>
                            <button onclick="requestDemo()" class="btn btn-req-demo f-s-16">Request a Demo</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12 img-right-col">
                    <span class="main-image text-center m-auto">
                        <img src="{{url('images/assets/main/features/vip-phone.png')}}">
                    </span>
                </div>
            </div>
        </div>
    </section>
    <section class="skew-top-right">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12 sm-order-2">
                    <div class="sec-content text-center" style="padding: 10px;">
                        <img class="main-image" src="{{ url('images/assets/main/features/vip-tiers-options.png') }}">
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 class="f-s-38">
                                Define Tier Milestones 
                            </h2>
                            <p class="f-s-19">Specify how customers can earn access to your amazing VIP program including: Amount Spent and Points Earned. This encourages users to shop & interact more with your brand.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="purple-sec skew-top-right">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 class="f-s-38">
                                Customize Tier Rewards  
                            </h2>
                            <p class="white f-s-19">Offer entry rewards when customers first achieve VIP status, and lifetime rewards for their continued loyalty with your brand.</p>
                            <p class="white f-s-19">Lootly also allows you to offer Custom Rewards, which are unique perks centered around your brand and administered by your team.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content text-center">
                        <img class="main-image" src="{{ url('images/assets/main/features/customize-tier-rewards.png') }}">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="skew-top-right">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12 sm-order-2">
                    <div class="sec-content text-center">
                        <img class="main-image" src="{{ url('images/assets/main/features/create-tiers.png') }}">
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 class="f-s-38">
                                Create your own Tiers 
                            </h2>
                            <p class="f-s-19">Create unique names for your Tiers that best represent your brand such as: Gold Tier or Surf Legend</p>
                            <p class="f-s-19">Add your unique icon to further customize the appearance of your Tiers to customers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="purple-sec skew-top-right">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 class="f-s-38">
                                Customer Group Segmentation   
                            </h2>
                            <p class="white f-s-19">Lootly makes it easy to create special VIP programs for specific groups of customers such as Retail and Wholesale users.</p>
                            <p class="white f-s-19">This allows you to create separate rewards that make sense for those customer groups.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content text-center">
                        <img class="main-image" src="{{ url('images/assets/main/features/customer-segmentation.png') }}">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="platform-features wavy-gray-sec skew-top-right">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-12">
                    <div class="sec-content">
                        <h2 class="text-center m-b-15">
                            Complete Loyalty Marketing Platform
                        </h2>
                        <p class="f-s-18 text-center" style="margin-bottom: 40px">
                            Check out the other ways Lootly can help your business
                        </p>
                        <div class="row">
                            <div class="col-md-2" style="flex: 0 0 12.499999995%;"></div>
                            <div class="col-md-3 col-12">
                                <div class="card">
                                    <img class="feature-icon" src="{{url('images/assets/main/platform-features/points-and-rewards-icon.png')}}" width="70">
                                    <h5>Points & Rewards</h5>
                                    <div class="feature-overview">
                                        <p>Encourage actions & reward customers for interacting with your brand.</p>
                                    </div>
                                    <div class="learn-more">
                                        <a href="{{ url('features/points-rewards') }}">Learn more</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="card">
                                    <img class="feature-icon" src="{{url('images/assets/main/platform-features/referrals-icon.png')}}" width="70">
                                    <h5>Referrals</h5>
                                    <div class="feature-overview">
                                        <p>Turn customers into brand ambassadors to promote your store to their friends & family.</p>
                                    </div>
                                    <div class="learn-more">
                                        <a href="{{ url('features/referrals') }}">Learn more</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="card">
                                    <img class="feature-icon" src="{{url('images/assets/main/platform-features/customer-insights-icon.png')}}" width="70">
                                    <h5>Customer Insights</h5>
                                    <div class="feature-overview">
                                        <p>Discover how customers are interacting with your program and make adjustments in a few clicks.</p>
                                    </div>
                                    <div class="learn-more">
                                        <a href="{{ url('features/insights') }}">Learn more</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('website._partials._request-demo-section')
@endsection