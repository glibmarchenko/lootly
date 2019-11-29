@extends('website.layout')

@section('title', 'WooCommerce Integration')

@section('page-name', 'full-page')

@section('meta')
    <meta name="title" content="WooCommerce Integration | Lootly">
    <meta name="description" content="Grow your WooCommerce store with a Loyalty, Rewards, and Referrals platform by Lootly.">
    <meta name="keywords" content="woocommerce, woocommerce referrals, woocommerce loyalty, woocommerce rewards, woocommerce lootly">
    <meta property="og:title" content="WooCommerce Integration | Lootly">
    <meta property="og:image" content="{{ url('images/assets/main/integrations/woocommerce-banner.png') }}">
    <meta property="og:url" content="{{ url('/apps/woocommerce') }}">
    <meta property="og:description" content="Grow your WooCommerce store with a Loyalty, Rewards, and Referrals platform by Lootly.">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('content')
    <section class="page-overview" style="min-height: 495px;">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content text-left">
                        <h1>WooCommerce Integration</h1>
                        <p class="description">Lootly empowers WooCommerce owners by integrating a feature-rich Loyalty & Referrals program into their shop in 1-click.</p>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <span class="flex-center">
                        <img class="m-auto" src="{{url('images/assets/main/integrations/woocommerce-banner.png')}}">
                    </span>
                </div>
            </div>
        </div>
    </section>
    <section class="lootly-int skew-both-right sec-gray">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 class="f-s-38">Lootly & WooCommerce </h2>
                            <p class="f-s-18">Once Lootly is installed to your WooCommerce store, visitors can begin to understand how your loyalty & referrals program works in only a few clicks. Customize the design of everything to best match your brand and deliver an exciting new experience to visitors & customers</p>
                            <p class="f-s-18">Introduce over 10 ways to earn & spend points, along with referral and VIP programs to drive new revenue to your store. Expand your program by connecting to leading marketing platforms such as TrustSpot for reviews, Klaviyo for email marketing, Facebook & Twitter for social presence and more.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content text-center">
                        <div class="int-panel">
                            <div class="panel-head">
                                <button onclick="requestDemo()" class="btn btn-req-demo">Request a Demo</button>
                            </div>
                            <ul>
                                <li>
                                    <i class="fa fa-server "></i> <b>Category</b>
                                    <span class="sublist">eCommerce Platform</span>
                                </li>
                                <li>
                                    <i class="fa fa-file-text-o"></i> <b>Resources</b>
                                    <span class="sublist">
                                        <a href="http://support.lootly.io/getting-started/">Getting Started</a>
                                        <a href="http://support.lootly.io/getting-started/best-practices-for-your-loyalty-program">Best Practices</a>
                                        <a href="http://support.lootly.io/">Support Center</a>
                                    </span>
                                </li>
                                <!-- <li>
                                    <i class="fa fa-folder-o"></i> <b>Case Studies</b>
                                    <span class="sublist">
                                    </span>
                                </li> -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="wavy-white">
        <div class="container" style="padding-top: 30px;">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h3 class="m-b-20"> 
                                Incentivize Actions with Points
                            </h3>
                            <p class="f-s-18">With over a dozen ways to Earn & Spend Points, you can build exciting ways for customers to interact with your WooCommerce store.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12 incentivize-actions">
                    <div class="sec-content text-center">
                        <img class="main-image" src="{{ url('images/assets/main/features/incentivize-actions-2.png') }}" style="">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="blue-sec skew-top-right p-b-0">
        <div class="container p-t-10" style="padding-bottom: 100px;">
            <div class="row">
                <div class="col-md-6 col-12 sm-order-2">
                    <div class="sec-content text-center xs-iphone" style="max-height: 500px;">
                        <img class="main-image" src="{{ url('images/assets/main/features/reward-you-customers.png') }}">
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div style="margin-top: 50px;">
                            <h2 class="f-s-38">
                                Reward Your Customers 
                            </h2>
                            <p class="white f-s-19">Offer a variety of ways to reward your loyal customers such as:</p>
                            <div class="features-list white">
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
            </div>
        </div>
    </section>
    <section class="skew-top-right wavy-white">
        <div class="container p-t-40">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 class="f-s-38">
                                Referral Program 
                            </h2>
                            <p class="f-s-18">Give your existing customers a great way to be reward for sharing your store with their friends and family.</p>
                            <p class="f-s-18">When a referred user visits your site, they are immediately shown a coupon to use on their first purchase. Lootly makes it easy for everybody to obtain their discount and start shopping on your store.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content text-center">
                        <img class="main-image" src="{{ url('images/assets/main/features/referrals-settings.png') }}">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="blue-sec skew-top-right p-b-0">
        <div class="container p-t-10" style="padding-bottom: 100px;">
            <div class="row">
                <div class="col-md-6 col-12 sm-order-2">
                    <div class="sec-content text-center xs-iphone" style="max-height: 430px;">
                        <img class="main-image m-t-20" src="{{ url('images/assets/main/features/vip-phone.png') }}">
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div style="margin-top: 90px;">
                            <h2 class="f-s-38">
                                VIP Program 
                            </h2>
                            <p class="white f-s-19 m-b-20">Drive repeat purchases, customer loyalty and happiness by introducing a VIP Program.</p>
                            <p class="white f-s-19">Reward your best customers with exclusive discounts and special promotions.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="skew-top-right wavy-white">
        <div class="container p-b-0 p-t-40">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 class="f-s-38" style="max-width: 500px">
                                Discover what your Customers Love 
                            </h2>
                            <p class="f-s-18">Optimize your loyalty program overtime by understanding which actions & rewards are being utilized the most and adjust your items in just a few clicks.</p>
                            <p class="f-s-18">Lootly also allows you to drill down into individual customers to get a complete understanding of how a customer is using your program.</p>
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

    @include('website._partials._request-demo-section')
    
@endsection