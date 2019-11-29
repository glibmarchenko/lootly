@extends('website.layout')

@section('title', 'FAQ')

@section('meta')
    <meta name="title" content="FAQ | Lootly">
    <meta name="description" content="Learn common questions and answers at Lootly. Our team is standing by to help launch your rewards program.">
    <meta name="keywords" content="Lootly rewards, lootly questions, lootly answers, lootly Q&A, lootly referrals, lootly demo, lootly">
    <meta property="og:title" content="FAQ | Lootly">
    <meta property="og:image" content="{{ url('images/assets/main/company/our-branding.svg') }}">
    <meta property="og:url" content="{{ url('/faq') }}">
    <meta property="og:description" content="Learn common questions and answers at Lootly. Our team is standing by to help launch your rewards program.">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('content')
	<section class="head-section">
		<h1>Common Questions and Answers</h1>
	</section>

    @include('website.company._nav')

	<section class="article sec-border-bottom md-sec">
		<div class="container">
			<div class="row">
				<div class="col-12 text-center">
					<p class="dark-color f-s-18" style="margin: 0 auto; max-width: 500px;">
						Have a question outside our FAQ? Feel free to click the bottom right button to chat with one of our Loyalty Experts 
					</p>
				</div>
			</div>
		</div>
	</section>

	<section class="faq-sec sec-border-bottom md-sec">
	    <div class="container">
	        <div class="faq">
	            <h3 class="text-center">Frequently Asked Questions</h3>
	            <div class="row">
	                <div class="col-sm-6 col-12">
	                    <ul>
	                        <li class="open">
	                           <h4>How does Lootly help my store?</h4>
	                           <p>Lootly is a Loyalty Rewards & Referrals platform that makes it easy for your customers to be reward for purchasing from your store or interacting with your brand. We offer over 10 ways for customers to participate in your loyalty program, such as: Sharing your brand on Facebook, reading your blog, writing a review or celebrating a birthday.</p>
	                           <p>Customers performing these actions can receive points, which helps to build loyalty with your brand, especially if you have a number of great rewards available to them.</p>
	                           <p>Lootly also helps you build new sources of revenue by establishing a Referrals Program, to help turn happy customers into Brand Ambassadors.</p>
	                           <p>Lastly, we also offer a VIP Program to help you reward your most loyal customers with special rewards and offers.</p>
	                        </li>
	                        <li class="open">
	                            <h4>How easy is this to setup?</h4>
	                            <p>
	                                Lootly is incredibly simple to install to your store, as it only takes a few clicks and less than 1 minute of your time. Once installed, our team is always available via Live Chat, Email or Phone for helping answer any questions you have.
	                            </p>
	                        </li>
	                        <li class="open" style="min-height: 170px;">
	                            <h4>Do I need to sign a Contract?</h4>
	                            <p>
	                                Nope! At Lootly, we’re all about simplicity and transparency. Our plans are month-to-month, with no contracts or setup fees.
	                            </p>
	                        </li>
	                    </ul>
	                </div>
	                <div class="col-sm-6 col-12">
	                    <ul>
	                        <li class="open">
	                            <h4>Can I import my existing customers?</h4>
	                            <p>
	                                Absolutely! With Lootly, you simply need to upload a file with your current customers into your account and you’re done. There are no limits on how many customers you can import.
	                            </p>
	                        </li>
	                        <li class="open">
	                            <h4>Can I test Loolty?</h4>
	                            <p>
	                               Sure, we have a demo store available from <a href="https://surforlando.com">here</a>. You can also use our app completely free on your store to get a better feel for how Lootly works.
	                            </p>
	                        </li>
	                        <li class="open">
	                            <h4>What currency are plans charged in?</h4>
	                            <p>
	                               All plans are charged in USD regardless of your location.
	                            </p>
	                        </li>
	                        <li class="open">
	                            <h4>Can I change plans at any time?</h4>
	                            <p>
	                               Sure! Since Lootly does not use contracts, you can easily upgrade your plan at any time from your account. This only takes a few seconds, and then you’re all set. 
	                            </p>
	                        </li>
	                        <li class="open">
	                            <h4>Are your Unlimited Plans REALLY Unlimited?</h4>
	                            <p>
	                                Yes! There are no tricks or hidden fees here. Our plans include Unlimited Orders and Unlimited Customers. While other loyalty platforms limit your growth, we built Lootly to scale with your business without charging you more.
	                            </p>
	                        </li>
	                    </ul>
	                </div>
	            </div>
	        </div>
	    </div>
	</section>
@endsection
