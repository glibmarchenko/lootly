@extends('website.layout')

@section('title', 'TrustSpot Integration')

@section('page-name', 'full-page')

@section('meta')
    <meta name="title" content="TrustSpot Integration | Lootly">
    <meta name="description" content="Reward customers for submitting reviews to your store with TrustSpot and Lootly.">
    <meta name="keywords" content="trustspot reviews, reviews for shopify, trustspot lootly, lootly and trustspot">
    <meta property="og:title" content="TrustSpot Integration | Lootly">
    <meta property="og:image" content="{{ url('images/assets/main/integrations/ts-banner.png') }}">
    <meta property="og:url" content="{{ url('/apps/trustspot') }}">
    <meta property="og:description" content="Reward customers for submitting reviews to your store with TrustSpot and Lootly.">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('content')
    <section class="page-overview" style="min-height: 495px;">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content text-left">
                        <h1>Lootly & TrustSpot</h1>
                        <p class="description">Increase your store conversion rate with a feature-rich Reviews Platform.</p>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <span class="flex-center">
                        <img class="m-auto" src="{{url('images/assets/main/integrations/ts-banner.png')}}">
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
                            <h2 class="f-s-38">Lootly & TrustSpot </h2>
                            <p class="f-s-18">Once Lootly is connected, visitors will be greeted by our highly customizable widget, allowing users to better understand how they can earn & spend points.</p>
                            <p class="f-s-18">Trustspot is just the begining, as Lootly includes numerous ways to connect with existing apps you already use, such as: Klaviyo & Mailchimp for Email Marketing and Facebook for social presence.</p>
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
                                Intelligent Review Requests
                            </h3>
                            <p class="f-s-18">Easily collect more product & company reviews, photos, and questions/answers with AI powered email requests.</p>
                            <div class="features-list blue">
                                <ul>
                                    <li>
                                        <i class="fa fa-check"></i> In-email review form
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Ask custom questions
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Send from your own domain
                                    </li>
                                </ul>
                                <ul>
                                    <li>
                                        <i class="fa fa-check"></i> Higher response rate
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Design customization
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content text-center">
                        <img class="main-image" src="{{ url('images/assets/main/features/review-requests.png') }}">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="blue-sec skew-both-right p-b-0">
        <div class="container p-t-25 p-b-10">
            <div class="row">
                <div class="col-md-6 col-12 sm-order-2">
                    <div class="sec-content text-center">
                        <img class="main-img" src="{{ url('images/assets/main/features/visual-marketing.png') }}">
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div style="margin-top: 50px;">
                            <h2 class="f-s-38">
                                Visual Marketing 
                            </h2>
                            <p class="white f-s-19">Your customer's photos are your biggest marketing asset. Harness the power of customer photos + Instagram to upsell products directly on your site.</p>
                            <div class="features-list white">
                                <ul>
                                    <li>
                                        <i class="fa fa-check"></i> Collect photos with reviews
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Upsell products from Instagram
                                    </li>
                                </ul>
                                <ul>
                                    <li>
                                        <i class="fa fa-check"></i> Higher response rate
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Build your brand with social proof
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="wavy-white">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 class="f-s-38">
                                Community Q&A 
                            </h2>
                            <p class="f-s-18">Give prospective buyers a voice by allowing them to ask questions on your products.</p>
                            <div class="features-list blue">
                                <ul class="w-100">
                                    <li>
                                        <i class="fa fa-check"></i> Decrease buyer hesitation
                                    </li>
                                    <li>
                                        <i class="fa fa-check"></i> Engage past buyers to help answer questions for you
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content text-center">
                        <img class="main-image p-b-40" src="{{ url('images/assets/main/features/customer-chat.png') }}">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="blue-sec skew-both-right p-b-0">
        <div class="container p-t-40">
            <div class="row">
                <div class="col-md-6 col-12 sm-order-2">
                    <div class="sec-content text-center">
                        <img class="main-image" src="{{ url('images/assets/main/features/implementation-service.png') }}">
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 class="f-s-38">
                                Implementation Service 
                            </h2>
                            <p class="white f-s-18">TrustSpot will implement the service on your site, fully customize the design to match your brand and import your existing reviews.<br><br>This is a <b>Free Service</b> and is completed the <b>Same Day</b>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="wavy-white">
        <div class="container" style="padding-top: 60px">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 class="f-s-38" style="max-width: 500px">
                                Featured Product Upsells 
                            </h2>
                            <p class="f-s-18">Increase your repeat purchases by promoting other great products to your customers on each TrustSpot email. </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content text-center">
                        <img class="main-image" src="{{ url('images/assets/main/features/product-upsells.png') }}">
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('website._partials._request-demo-section')
    
@endsection