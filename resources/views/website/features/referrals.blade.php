@extends('website.layout')

@section('title', 'Referrals Program')

@section('page-name', 'full-page')

@section('meta')
    <meta name="title" content="Referrals Program | Lootly">
    <meta name="description" content="Reward your existing customers for referring their friends & family to your store.">
    <meta name="keywords" content="referrals program, referrals">
    <meta property="og:title" content="Referrals Program | Lootly">
    <meta property="og:image" content="{{ url('images/assets/main/features/referrals-overview.png') }}">
    <meta property="og:url" content="{{ url('features/referrals') }}">
    <meta property="og:description" content="Reward your existing customers for referring their friends & family to your store.">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('content')
    <section class="page-overview">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content text-left">
                        <h1 style="max-width: 500px;">Increase your Revenue in 1-click</h1>
                        <p class="description">Give your existing customers a great way to be rewarded for referring their friends to order from your store.</p>
                        <div class="btn-wrapper">
                            <a href="{{ url('pricing') }}" class="btn btn-signup f-s-16">Get Started</a>
                            <button onclick="requestDemo()" class="btn btn-req-demo f-s-16">Request a Demo</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12 img-right-col">
                    <span class="main-image text-center m-auto">
                        <img src="{{url('images/assets/main/features/referrals-overview.png')}}">
                    </span>
                </div>
            </div>
        </div>
    </section>
    <section class="skew-top-right">
        <div class="container xs-p-b-0">
            <div class="row">
                <div class="col-md-6 col-12 sm-order-2">
                    <div class="sec-content text-center">
                        <img class="main-image" src="{{ url('images/assets/main/features/multiple-ways-to-share.png') }}">
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 class="f-s-38">
                                Multiple Ways to Share 
                            </h2>
                            <p class="f-s-19">Make it easy for your customers to share their referral discount on Social Media and Email with our built-in sharing system.</p>
                            <p class="f-s-19">Customize the referral link to match your domain to maintain branding consistency.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="purple-sec skew-top-right p-b-0">
        <div class="container p-t-10" style="padding-bottom: 90px;">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div style="margin-top: 80px;">
                            <h2 class="f-s-38">
                                Reward both the <br> Sender and Receiver  
                            </h2>
                            <p class="white f-s-19">Create unique rewards for customers who share their referral link, and for those who click on the link to make a purchase at your store.</p>
                            <div class="features-list">
                                <ul>
                                    <li>
                                        <i class="fa fa-check"></i> $ Off Coupon
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> % Off Coupon
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Free Shipping
                                    </li>
                                </ul>
                                <ul>
                                    <li>
                                        <i class="fa fa-check"></i> Free Product
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Free Points
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content xs-iphone text-center" style="max-height: 500px;">
                        <img class="main-image" src="{{ url('images/assets/main/features/reward-iphone.png') }}" width="">
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
                        <img class="main-image" src="{{ url('images/assets/main/features/referrals-settings.png') }}">
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 class="f-s-38">
                                Deliver a great First Impression 
                            </h2>
                            <p class="f-s-19">Once a referred user lands on your site, they are immediately greeted with our reward area to generate their coupon.</p>
                            <p class="f-s-19">From here they can easily click to copy their code and start shopping.</p>
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
                                Referral Insights   
                            </h2>
                            <p class="white f-s-19">Discover how customers are interacting with your referral program, in order to continue driving new revenue for your business.</p>
                            <p class="white f-s-19">Lootly also gives you complete visibility into:</p>
                            <div class="features-list">
                                <ul>
                                    <li>
                                        <i class="fa fa-check"></i> Referral Revenue
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Avg Order Value
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Top Referrers
                                    </li>
                                </ul>
                                <ul>
                                    <li>
                                        <i class="fa fa-check"></i> Referral Orders
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Shares & Clicks breakdown
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content text-center">
                        <img class="main-image" src="{{ url('images/assets/main/features/referral-insights.png') }}">
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
                                    <img class="feature-icon" src="{{url('images/assets/main/platform-features/vip-program-icon.png')}}" width="70">
                                    <h5>VIP Program</h5>
                                    <div class="feature-overview">
                                        <p>Increase repeat purchases while offering unique perks for your best customers.</p>
                                    </div>
                                    <div class="learn-more">
                                        <a href="{{ url('features/vip') }}">Learn more</a>
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