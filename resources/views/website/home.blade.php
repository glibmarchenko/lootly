@extends('website.layout')

@section('page-name', 'full-page home')

@section('meta')
    <title>eCommerce Loyalty, Rewards and Referrals by Lootly</title>
    <meta name="title" content="eCommerce Loyalty, Rewards and Referrals by Lootly">
    <meta name="description" content="Build customer loyalty, increase retention, and scale your brand. It is all possible with Lootly.">
    <meta name="keywords" content="loyalty, referrals, rewards, ecommerce, rewards program">
    <meta property="og:title" content="eCommerce Loyalty, Rewards and Referrals by Lootly">
    <meta property="og:image" content="{{ url('images/assets/main/features/overview.png') }}">
    <meta property="og:url" content="https://www.lootly.io">
    <meta property="og:description" content="Build customer loyalty, increase retention, and scale your brand. It is all possible with Lootly.">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('content')
    <section class="page-overview">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content text-left">
                        <h1>Great brands are built on loyal customers.</h1>
                        <p class="description">Lootly helps you build relationships with customers by rewarding them for interacting with your store.</p>
                        <form class="request-demo-block" action="{{ url('/request-demo') }}" method="get">
                            <div class="row">
                                <div class="col-md-8 col-12">
                                    <input name="email" type="email" placeholder="Enter your email" class="form-control">
                                </div>
                                <div class="col-md-4 col-12">
                                    <button type="submit" class="btn btn-primary btn-block">Request a Demo</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-6 col-12 img-right-col">
                    <span class="main-image">
                        <img src="{{url('images/assets/main/features/overview.png')}}">
                    </span>
                </div>
            </div>
        </div>
    </section>
    <section class="integrations-bar">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-12">
                    <div class="sec-content text-center">
                        <ul>
                            <li>
                                <img src="{{ url('images/assets/main/integrations/shopify-grey.png') }}" height="" alt="Shopify">
                            </li>
                            <li>
                                <img src="{{ url('images/assets/main/integrations/shopify-plus-grey.png') }}" alt="Shopify Plus">
                            </li>
                            <li>
                                <img src="{{ url('images/assets/main/integrations/bigcommerce-grey.png') }}" alt="BigCommerce">
                            </li>
                            <li>
                                <img src="{{ url('images/assets/main/integrations/magento-grey.png') }}" alt="Magento">
                            </li>
                            <li>
                                <img src="{{ url('images/assets/main/integrations/woocommerce-grey.png') }}" alt="WooCommerce">
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="platform-features">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-12">
                    <div class="sec-content">
                        <h2 class="text-center">
                            Complete Loyalty Marketing Platform
                        </h2>
                        <div class="row">
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
    <section class="brand-experience">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2>
                                Deliver a consistent brand experience
                            </h2>
                            <p class="f-s-18"> Lootly makes it easy to create a seamless and stunning experience for your customers in just a few clicks.</p>
                            <p class="f-s-18">From Emails to On-Site displays and everything in-between, you can customize every aspect of your program without limitations.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content text-center" style="max-height: 510px;">
                        <img class="main-image" src="{{ url('images/assets/main/features/launcher-overview.png') }}">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="points-overview">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content text-center">
                        <img class="main-image" src="{{ url('images/assets/main/features/points-overview.png') }}" style="max-width: 500px;width: 100%;">
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2>
                                Engage & Reward your customers
                            </h2>
                            <p class="f-s-18">With over a dozen ways to Earn & Spend Points, you can build exciting ways for customers to interact with your store.</p>
                            <p class="f-s-18">Connect to your favorite apps to further tailor Lootly to your brand’s needs.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="customers-overview">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2>
                                Understand your Customers
                            </h2>
                            <p class="f-s-18">Drill down into specific customers to get a detailed understanding of how users are interacting with your brand.</p>
                            <div class="features-list blue m-t-0">
                                <ul>
                                    <li>
                                        <i class="fa fa-check"></i> Import existing customers
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Customer segmentation
                                    </li>
                                </ul>
                                <ul>
                                    <li>
                                        <i class="fa fa-check"></i> Program usage insights
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Order & Referral history
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content text-center">
                        <img class="main-image" src="{{ url('images/assets/main/features/customers-overview.png') }}">
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('website._partials._request-demo-section')
@endsection