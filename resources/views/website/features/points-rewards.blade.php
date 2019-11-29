@extends('website.layout')

@section('title', 'Points & Rewards')

@section('page-name', 'full-page points-rewards')

@section('meta')
    <meta name="title" content="Points & Rewards | Lootly">
    <meta name="description" content="Launch your own Points & Rewards program in just a few minutes with our easy to use tools.">
    <meta name="keywords" content="reward points, rewards, customer points program, points">
    <meta property="og:title" content="Points & Rewards | Lootly">
    <meta property="og:image" content="{{ url('images/assets/main/features/points-rewards.png') }}">
    <meta property="og:url" content="{{ url('features/points-rewards') }}">
    <meta property="og:description" content="Launch your own Points & Rewards program in just a few minutes with our easy to use tools.">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('content')
    <section class="page-overview">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content text-left">
                        <h1>Loyalty & Rewards built for your business</h1>
                        <p class="description">Launch your own program in less than 5 minutes, and begin to grow a loyal following.</p>
                        <div class="btn-wrapper">
                            <a href="{{ url('pricing') }}" class="btn btn-signup f-s-16">Get Started</a>
                            <button onclick="requestDemo()" class="btn btn-req-demo f-s-16">Request a Demo</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12 img-right-col" style="z-index: 99">
                    <span class="main-image" style="height: 640px;max-height: none;margin: 0 0 0 35px;">
                        <img src="{{url('images/assets/main/features/points-rewards.png')}}" style="width: 880px;max-width: none;">
                    </span>
                </div>
            </div>
        </div>
    </section>
    <section class="wavy-white skew-top-right">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 class="f-s-38">
                                Incentivize Actions with Points 
                            </h2>
                            <p class="f-s-19">Create earning actions that allow customers to heavily engage with your brand.</p>
                            <p class="f-s-19">Increase your social media presence, reviews and blog viewership by encouraging users to interact with you.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12 incentivize-actions">
                    <div class="sec-content text-center">
                        <img class="main-image" src="{{ url('images/assets/main/features/points-overview-w.png') }}">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="purple-sec skew-top-right">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12 sm-order-2">
                    <div class="sec-content text-center">
                        <img class="main-image" src="{{ url('images/assets/main/features/points-expiration.png') }}">
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 class="f-s-38">
                                Point Expiration & Reminders
                            </h2>
                            <p class="white f-s-19">Set automated reminders to let previous customers know they have points in your store that are expiring soon. This level of engagement ensures dormant customers come back and redeem their points.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="wavy-white skew-top-right p-b-0">
        <div class="container p-t-10" style="padding-bottom: 90px;">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div style="margin-top: 50px;">
                            <h2 class="f-s-38">
                                Reward your Customers 
                            </h2>
                            <p class="f-s-19">Offer a variety of ways to reward your loyal customers including:</p>
                            <div class="features-list blue">
                                <ul>
                                    <li>
                                        <i class="fa fa-check"></i> $ Off Coupon
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> % Off Coupon
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Free Product
                                    </li>
                                </ul>
                                <ul>
                                    <li>
                                        <i class="fa fa-check"></i> Free Shipping
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Variable $ Off Coupon
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Custom Actions via API
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content text-center xs-iphone" style="max-height: 500px;">
                        <img class="main-image" src="{{ url('images/assets/main/features/reward-you-customers.png') }}">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="purple-sec skew-top-right">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12 sm-order-2">
                    <div class="sec-content text-center" style="padding: 15px 10px;">
                        <img class="main-image" src="{{ url('images/assets/main/features/brand-customization.png') }}">
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 class="f-s-38">
                                Complete Brand Customization   
                            </h2>
                            <p class="white f-s-19">Customize all aspects of your Loyalty Program with our easy to use tools or roll up your sleeves and use our HTML Editors for complete brand control.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="wavy-white skew-top-right">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 style="font-size: 37px;">
                                Customer & Product Segmentation   
                            </h2>
                            <p class="f-s-19">Create unique earning & spending rules for unlimited program possibilities. Lootly includes the following segmentation options:</p>
                            <div class="features-list blue">
                                <ul>
                                    <li>
                                        <i class="fa fa-check"></i> Customer Tags
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Product ID
                                    </li>
                                </ul>
                                <ul>
                                    <li>
                                        <i class="fa fa-check"></i> VIP Tier
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Collection
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content text-center">
                        <img class="main-image" src="{{ url('images/assets/main/features/restrictions.png') }}">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="platform-features wavy-gray-sec skew-top-right ">
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