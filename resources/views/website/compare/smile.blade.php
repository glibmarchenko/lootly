@extends('website.layout')

@section('page-name', 'full-page compare-page')

@section('navbar', 'white-nav')

@section('head')
    <title>Smile.io Alternative</title>
    <style type="text/css">
        @media(min-width: 880px) {
            .compare-main-sec.smile {
                background-image: url('/images/assets/main/compare/lootly-smile-lg.png');
            }
        }
        @media(max-width: 880px) {
            .compare-main-sec.smile {
                background-image: url('/images/assets/main/compare/lootly-smile.png');
            }
        }
    </style>
@endsection

@section('content')
    <section class="compare-main-sec smile"></section>

    <section class="skew-top-right">
        <div class="container p-t-50">
        	<div class="row">
        		<div class="col-12">
        			<div class="text-center m-auto" style="max-width: 710px;">
	        			<h2>Don't let Expensive Pricing, Customer Limits & Lack of Features hold you down</h2>
	        			<p class="m-t-30" style="font-size: 22px;">
	        				<span style="color: #444">Smile.io is very expensive. Lootly provides a comparable loyalty & rewards platform for a fraction of the cost.</span>
	        				<span style="background: #fff89e; font-style: italic; font-weight: 500">Starting at just $49 per month for Unlimited Orders & Unlimited Customers.</span>
	        			</p>
        			</div>
        		</div>
        	</div>
            <div class="row m-t-40">
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h2 class="text-left m-b-10">
                                Unlimited Growth 
                            </h2>
                            <p style="max-width: 500px; font-size: 18px;">Loolty was built to empower eCommerce brands to grow without limitations.</p>
                            <ul class="lootly-features-list">
                            	<li>
                            		<img width="50" src="{{ url('images/assets/main/compare/unlimited-orders.png') }}"> Unlimited Orders
                            	</li>
                            	<li>
                            		<img width="50" src="{{ url('images/assets/main/compare/unlimited-customers.png') }}"> Unlimited Customers
                            	</li>
                            	<li>
                            		<img width="50" src="{{ url('images/assets/main/compare/integration.png') }}"> Unlimited Integrations
                            	</li>
                            	<li>
                            		<img width="50" src="{{ url('images/assets/main/compare/affordable-pricing.png') }}"> Affordable & Transparent Pricing
                            	</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="sec-content text-center" style="padding: 10px;">
                        <img class="main-image" src="{{ url('images/assets/main/features/insights-overview.png') }}" style="box-shadow: 0px 0px 20px 10px #f6f6f666;">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="blue-sec skew-both-right p-b-0">
        <div class="container p-b-15 p-t-20">
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
    <section class="wavy-white">
        <div class="container" style="padding-top: 30px;">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content flex-center">
                        <div>
                            <h3 class="m-b-20"> 
                                Incentivize Actions with Points
                            </h3>
                            <p class="f-s-18">Create earning actions that allow customers to heavily engage with your store.</p>
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
                                        <span class="fa fa-check-circle" style="font-size: 21px;margin-right: 5px;"></span> $ Off Coupon
                                    </li>
                                    <li>
                                        <span class="fa fa-check-circle" style="font-size: 21px;margin-right: 5px;"></span> % Off Coupon
                                    </li>
                                    <li>
                                        <span class="fa fa-check-circle" style="font-size: 21px;margin-right: 5px;"></span> Free Product
                                    </li>
                                </ul>
                                <ul>
                                    <li>
                                        <span class="fa fa-check-circle" style="font-size: 21px;margin-right: 5px;"></span> Free Shipping
                                    </li>
                                    <li>
                                        <span class="fa fa-check-circle" style="font-size: 21px;margin-right: 5px;"></span> Variable $ Off Coupon
                                    </li>
                                    <li>
                                        <span class="fa fa-check-circle" style="font-size: 21px;margin-right: 5px;"></span> Custom Actions via API
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="skew-top-right p-b-20">
		<div class="pricing-page">
			<div class="pricing-wrapper">
				<section class="pricing-header">
					<div class="container p-t-0 p-b-0">
						<h1>
							Lootly is more affordable for growing businesses
						</h1>
						<p>
							We believe that our services should be affordable for everybody, regardless of business size.
						</p>
					</div>
				</section>
                @include('website.compare._pricing-plans')
			</div>
		</div>
    </section>
    <section>
    	<div class="container p-t-0">
    		<div class="row">
    			<div class="col-12">
			    	<h3 class="text-center">See how much you can save by making the switch</h3>
			    	<div class="table-responsive">
			            <table class="compare-table">
			        		<thead>
			        			<tr>
			        				<th></th>
			        				<th><img src="{{ url('images/logos/logo.png') }}" alt=""><span>$249 Plan</span></th>
			        				<th><img src="{{ url('images/assets/main/compare/smile-io.png') }}" alt=""><span>$599 Plan</span></th>
			        			</tr>
			        		</thead>
			        		<tbody>
			        			<tr>
			        				<td>Number of orders & customers monthly</td>
			        				<td><i class="fa fa-check-circle" aria-hidden="true"></i> Unlimited</td>
			        				<td><i class="fa fa-check-circle" aria-hidden="true"></i> Unlimited</td>
			        			</tr>
			        			<tr>
			        				<td>Number of people you can reward monthly</td>
			        				<td><i class="fa fa-check-circle" aria-hidden="true"></i> Unlimited</td>
			        				<td class="flex-center">
			        					<span><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
			        					<span class="desc">2k included, <br>but $5/mo for every 50 people extra.</span>
			        				</td>
			        			</tr>
			        			<tr>
			        				<td>10 + Ways to Earn Points</td>
			        				<td><i class="fa fa-check-circle" aria-hidden="true"></i> Yes</td>
			        				<td><i class="fa fa-check-circle" aria-hidden="true"></i> Yes</td>
			        			</tr>
			        			<tr>
			        				<td>VIP Program</td>
			        				<td><i class="fa fa-check-circle" aria-hidden="true"></i> Yes</td>
			        				<td><i class="fa fa-check-circle" aria-hidden="true"></i> Yes</td>
			        			</tr>
			        			<tr>
			        				<td>Integrations</td>
			        				<td><i class="fa fa-check-circle" aria-hidden="true"></i> Unlimited</td>
			        				<td><i class="fa fa-check-circle" aria-hidden="true"></i> Unlimited</td>
			        			</tr>
			        			<tr>
			        				<td>Smart Insights</td>
			        				<td><i class="fa fa-check-circle" aria-hidden="true"></i> Yes</td>
			        				<td><i class="fa fa-exclamation-circle" aria-hidden="true"></i> No</td>
			        			</tr>
			        		</tbody>
			        	</table>
			    	</div>

        			<div class="text-center m-auto" style="max-width: 900px;">
	        			<p class="m-b-50" style="font-size: 24px;">
	        				<span style="color: #444">With Lootly if you have 3,000 customers redeeming points for rewards, your $249 plan is still $249 per month. But with Smile your price goes up to $699 per month.</span>
	        				<span style="background: #fff89e; font-style: italic; font-weight: 500">Don't be punished for growing your business, make the switch to Lootly today.</span>
	        			</p>
        			</div>
    			</div>
    		</div>
    	</div>
    </section>
    <section class="blue-sec skew-both-right">
        <div class="container p-t-40 p-b-0">
            <div class="row">
                <div class="col-12">
                    <div class="sec-content text-center">
                        <div>
                            <h2>Switching is Seamless</h2>
                            <p class="white m-b-10" style="font-size: 20px">Import your customers in just a few minutes.</p>
                        </div>
                    </div>
                    <ul class="switch-to-lootly m-t-0">
                    	<li class="">
                    		<img class="m-t-15" height="80" src="{{ url('images/assets/main/compare/export.png') }}">
                    		<p class="bold">Export from Smile.io</p>
                    	</li>
                    	<li class="arrow">
                    		<span>
	                    		<img src="{{ url('images/assets/main/arrow-icon.png') }}" style="transform: rotateX(180deg);padding-bottom: 20px;">
                    		</span>
                    	</li>
                    	<li class="">
                    		<img height="80" src="{{ url('images/assets/main/compare/import.png') }}">
                    		<p class="bold">Import into Lootly</p>
                            <p style="font-weight: 600">Starting on $49 Plan</p>
                    	</li>
                    	<li class="arrow">
                    		<span>
	                    		<img src="{{ url('images/assets/main/arrow-icon.png') }}" style="padding-bottom: 20px">
                    		</span>
                    	</li>
                    	<li class="">
                    		<img height="80" src="{{ url('images/assets/main/compare/celebration.png') }}">
                    		<p class="bold">Success!</p>
                    		<p style="font-weight: 600">That was easy :)</p>
                    	</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
	<section class="request-demo-sec">
	    <div class="container" style="padding-top: 65px;">
	        <div class="row">
	            <div class="col-md-12 col-12">
	                <div class="sec-content">
	                    <h2 class="text-center" style="max-width: 500px; margin: 15px auto;">Discover why others are switching to Lootly from Smile</h2>
	                    <div class="request-demo-block m-b-20" style="max-width: 488px;">
	                        <div class="row">
	                            <div class="col-12 text-center">
	                            	<a href="{{ url('pricing') }}" class="btn pricing-btn">See Plans & Pricing </a>
	                                <button onclick="requestDemo()" class="btn btn-primary">Request a Demo</button>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</section>
@endsection