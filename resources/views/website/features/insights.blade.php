@extends('website.layout')

@section('title', 'Insights & Reports')

@section('page-name', 'full-page')

@section('meta')
    <meta name="title" content="Insights & Reports | Lootly">
    <meta name="description" content="Discover how Lootly is driving more revenue to your store, including complete Program, Customer and Referral insights.">
    <meta name="keywords" content="loyalty insights, customer insights, reports, analytics, insights">
    <meta property="og:title" content="Insights & Reports | Lootly">
    <meta property="og:image" content="{{ url('images/assets/main/features/insights-overview.png') }}">
    <meta property="og:url" content="{{ url('features/insights') }}">
    <meta property="og:description" content="Discover how Lootly is driving more revenue to your store, including complete Program, Customer and Referral insights.">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('content')
    <section class="page-overview">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content text-left">
                        <h1>Insights at <br> your Fingertips</h1>
                        <p class="description">Get a complete understanding of how well your Loyalty & Referral program is performing.</p>
                        <div class="btn-wrapper">
                            <a href="{{ url('pricing') }}" class="btn btn-signup f-s-16">Get Started</a>
                            <button onclick="requestDemo()" class="btn btn-req-demo f-s-16">Request a Demo</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12 img-right-col">
                    <span class="main-image text-center m-auto">
                        <img src="{{url('images/assets/main/features/insights-overview.png')}}">
                    </span>
                </div>
            </div>
        </div>
    </section>
    <section class="skew-top-right">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12 sm-order-2">
                    <div class="sec-content text-center">
                        <img class="main-image" src="{{ url('images/assets/main/features/loyalty-investment.png') }}">
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 class="f-s-38">
                                Loyalty Investment 
                            </h2>
                            <p class="f-s-19">See all costs involved with running a loyalty program and compare that against new revenue to determine Value Generated from Lootly.</p>
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
                        <div style="margin-top: 120px;">
                            <h2 class="f-s-38" style="max-width: 500px;">
                                Discover what your Customers Love  
                            </h2>
                            <p class="white f-s-19">Optimize your loyalty program overtime by introducing new earning actions & spending rewards based on current usage.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content text-center">
                        <img class="main-image p-b-10" src="{{ url('images/assets/main/features/customers-discover.png') }}">
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
                        <img class="main-image" src="{{ url('images/assets/main/features/customer-vision.png') }}">
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 class="f-s-38">
                                Customer Vision 
                            </h2>
                            <p class="f-s-19">Drill down into individual customer profiles to gain better insight into their activity with your loyalty program, including:</p>
                            <div class="features-list blue">
                                <ul>
                                    <li>
                                        <i class="fa fa-check"></i> Activity History
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Coupons Used
                                    </li>
                                </ul>
                                <ul>
                                    <li>
                                        <i class="fa fa-check"></i> Order & Referral History
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Points Earned / Spent
                                    </li>
                                </ul>
                            </div>
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
                                See your Top Referrers   
                            </h2>
                            <p class="white f-s-19">Introducing a Referral Program is a great way to drive new customers to your store without large upfront costs. Lootly makes it easy to gain visibility into:</p>
                            <div class="features-list m-t-0">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('website._partials._request-demo-section')
    
@endsection