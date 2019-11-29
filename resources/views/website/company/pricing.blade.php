@extends('website.layout')

@section('title', 'Pricing')

@section('navbar', 'white-nav')

@section('head')
	<style type="text/css">
		.container-fluid.contents {min-height: 1000px;}
	</style>
@endsection

@section('meta')
    <meta name="title" content="Pricing | Lootly">
    <meta name="description" content="Lootly's pricing plans are built to scale with your business, which is why we offer Unlimited Customers & Unlimited Orders on every plan.">
    <meta name="keywords" content="lootly pricing, loyalty program pricing, ecommerce, rewards program, ecommerce loyalty, lootly plans">
    <meta property="og:title" content="Pricing | Lootly">
    <meta property="og:image" content="https://s3.amazonaws.com/lootly-website-assets/img/logo-black.png">
    <meta property="og:url" content="{{ url('pricing') }}">
    <meta property="og:description" content="Lootly's pricing plans are built to scale with your business, which is why we offer Unlimited Customers & Unlimited Orders on every plan.">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('content')
	<div id="pricing-page" class="pricing-page loader" v-cloak>
		<div class="pricing-wrapper">
			<section class="pricing-header">
				<div class="container">
					<h1>
						Simple and <span>Transparent</span> pricing
					</h1>
					<p>
						Unlimited Customers & Unlimited Orders
					</p>
					<div class="switch-input-wrap">
						<strong class="month" :class="{'active': !yearly}">Monthly</strong>
						<label class="switch">
							<input type="checkbox" v-model="yearly">
							<span class="switch-slider round"></span> 
						</label>
						<strong class="year" :class="{'active': yearly}">Yearly <span>(Save 10%)</span></strong>
					</div>
				</div>
			</section>
			<section class="pricing-plans">
				<div class="container">
					<div class="row">
						<div class="col-12">
							<ul class="different-pricing">
		                        <li v-for="plan in plans" :class="plan.type+'-plan'">
									<div v-if="plan.type == 'ultimate'" class="ultimate-top">Most Popular</div>
									<div class="price-wrap" :class="plan.type">
										<h2 v-text="plan.title"></h2>
										<span v-if="plan.type != 'custom'">
											<i class="i-treasure" :class="plan.type"></i>
										</span>
										<span v-else>
											<img src="{{ url('images/icons/custom-cog.png') }}" height="100">
										</span>
										<p class="plan-price">
											<sup v-if="plan.type != 'custom'">$</sup>
											<span v-if="plan.type != 'custom'">
												@{{calcPrice(plan.price) | format-number}}
											</span>
											<span v-else>Custom Quote</span>
										</p>
										<p class="plan-duration">
											<span v-if="plan.type != 'custom'">
												per <span v-if="yearly">Year</span><span v-else>Month</span>
											</span>
											<span v-else>Starting at $1,000</span>
										</p>
										<p class="plan-desc">
											<span>
												Unlimited Orders <br>
												Unlimited Customers
											</span>
										</p>
										<a v-if="plan.type != 'custom'" 
										   :href="yearly ? urlSignUp.yearly[plan.type] : urlSignUp.monthly[plan.type]"
										   :d-href="plan.href" 
										   class="btn plan-btn">
											{{ __('Start 7-Day Free Trial') }}
										</a>
										<a v-else 
										   href="{{ url('request-demo') }}" 
										   class="btn btn-primary">
											Request a Demo
										</a>

										<div class="plan-featured">
											<h3 v-text="plan.features.title"></h3>
											<ul>
												<li v-for="(item, index) in plan.features.items" :id="plan.type+'Popover-'+index" data-container="body" data-toggle="popover" :data-content="item.tooltip">
													<i v-if="item.title" class="fa fa-check" aria-hidden="true"></i> 
													<span v-text="item.title"></span>
												</li>
											</ul>
										</div>
									</div>
								</li>
							</ul>
							<div class="free-plan">
								<img src="{{ url('images/icons/plans/free.png') }}" height="65">
								<p>Are you a small business? Sign up for our Free Plan</p>
								<span class="free-signup">
									<a class="btn btn-success" href="{{ route('signup') }}">
										Sign Up Now
									</a>
								</span>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
		<section class="faq-section">
			<div class="container">
				<h2>Frequently Asked <span>Questions</span></h2>
				<div class="row">
					<div class="col-md-6 col-12" v-for="faq in faqs">
						<div class="faq">
							<h4 v-text="faq.question"></h4>
							<p v-html="faq.answer"></p>
						</div>
					</div>
				</div>
			</div>
		</section>
		<section class="request-demo-section">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<div class="">
							<h2>Not sure which Lootly plan is right for your store?</h2>
							<p>One of our Loyalty Experts can easily walk you through the platform, <br>
								and help to answer any questions you have along the way. 
							</p>
							<div class="">
								<button type="button" class="btn btn-primary btn-lg" @click="requestDemo()">
									Request a Demo
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
@endsection

@section('scripts')
	<script type="text/javascript" src="{{ url('js/plugins/vue.min.js') }}"></script>
	<script>
		var page = new Vue({
			el: '#pricing-page',
			data: {
				yearly: false,
				yearlyDiscount: '0.1',
				urlSignUp: {
					monthly: {
						growth: '{{ route('signup', ['plan' => 1]) }}',
						ultimate: '{{ route('signup', ['plan' => 2]) }}',
						enterprise: '{{ route('signup', ['plan' => 3]) }}',
					},
					yearly: {
						growth: '{{ route('signup', ['plan' => 1, 'yearly']) }}',
						ultimate: '{{ route('signup', ['plan' => 2, 'yearly']) }}',
						enterprise: '{{ route('signup', ['plan' => 3, 'yearly']) }}',
					},
				},
				plans: [
					{
						type: 'growth', 
						title: 'Growth', 
						price: '49',
						href: '/signup/1',
						features: {
							title: 'Everything in Free and...',
							items: [
								{title: 'Referral Program', tooltip: 'Drive new revenue to your store by allowing customers to refer their friends to your business.'}, 
								{title: 'Email Customization', tooltip: 'Customize all aspects of your emails including the logo, text and colors.'}, 
								{title: 'Earning Limits', tooltip: 'Set limits on how many points a customer can receive in a set period of time.'}, 
								{title: 'Customer Segmentation', tooltip: 'Create unique earning & spending rewards for specific customers, products, or categories.'},
								{title: 'Import Existing Customers', tooltip: 'Upload all of your customers into Lootly so they can retroactively get points for their past purchases.'}, 
								{title: 'Integrations', tooltip: 'Connect other great apps to expand functionality for your reward program, such as giving points for Writing a Review.'}, 
								{title: 'Remove Lootly Branding', tooltip: 'Remove all mentions of Lootly on your rewards program including the Widget and Emails. '}, 
							]
						}
					},
					{
						type: 'ultimate', 
						title: 'Ultimate', 
						href: '/signup/2',
						price: '249',
						features: {
							title: 'Everything in Growth and...',
							items: [
								{title: 'VIP Program', tooltip: 'Create special tiers with unique perks to reward your most loyal customers.'}, 
								{title: 'Points Expiration', tooltip: 'Set an expiration time for all points earned in your store and send automatic reminders to dormant customers to spend their points.'}, 
								{title: 'Advanced Customization', tooltip: 'Upload background images, icon images and more to fully customize all aspects of your loyalty program.'}, 
								{title: 'Variable Discount Coupons', tooltip: 'Variable Coupons allow customers to redeem any amount of points they have for a discount at your store. For example: “Get $1 Off per 100 points redeemed”.'}, 
								{title: 'HTML Editor Access', tooltip: 'Customize every aspect of your emails and reward page with our HTML Editor.'}, 
								{title: 'Insights & Reports', tooltip: 'Get an in-depth understanding of how well Lootly is working for your business, including an overview of New Revenue, Orders, Referrals, Investments and more.'},
								{title: 'Rewards Page', tooltip: 'Display an overview of your program on a dedicated page on your site including a How it Works and FAQ section.'}, 
								{title: 'Employee Access', tooltip: 'Invite other users to access your account.'}, 
								{title: 'Priority Support', tooltip: 'Extended support hours and priority queue support.'},
							]
						}
					},
					{
						type: 'enterprise', 
						title: 'Enterprise', 
						price: '599',
						href: '/signup/3',
						features: {
							title: 'Everything in Ultimate and...',
							items: [
								{title: 'Custom Sender Domain', tooltip: 'Send emails using your own domain and customize your referral link to match your site.'}, 
								{title: 'SMS Alerts (coming soon)', tooltip: ''}, 
								{title: 'API Access', tooltip: 'Build custom rules and integrations with access to our API.'},
								{title: 'Dedicated Account Manager', tooltip: 'Gain access to a dedicated loyalty expert who is responsible for making optimizations, customizing the design and to ensure everything is running smoothly.'},
								{title: 'Fully Managed Implementation', tooltip: 'Our enterprise account managers can help you fully setup your entire program including reward components, design customization, customer imports, and more.'},
							]
						}
					},
					{
						type: 'custom', 
						title: 'Custom Solutions', 
						price: 'Custom Quote',
						features: {
							title: 'Includes these features:',
							items: [
								{title: 'Ongoing Program Management', tooltip: 'Our enterprise account managers are responsible for working with you on a daily basis to make any adjustments to your program as needed, including custom changes and special requests.'},
								{title: 'Launch Assistance', tooltip: 'Your enterprise account manager will setup a plan on how to present your new loyalty program to buyers, including email marketing and social media awareness. They will also work with you on setting up everything prior to launch.'},
								{title: 'Strategy Planning', tooltip: 'Besides having access to an enterprise account manager, you will also gain access to our success team who can work with you on planning new feature deployments, design roll outs and integrations to work seamlessly with your loyalty program.'},
								{title: 'Custom Development', tooltip: 'Need a special feature, design item or use case built out? Our engineering team can work with your brand to build something from the ground up to work great for your needs.'},
								{title: 'Custom Apps', tooltip: 'Similar to Custom Development, we also offer the ability to build custom integrations into apps such as eCommerce platforms, email, reviews and more. Leverage our expertise in the space, to build something that connects perfectly to your Lootly program.'},
							]
						}
					}
				],
				faqs: [
					{question: 'Are your Unlimited Plans REALLY Unlimited?', answer: 'Yes! There are no tricks or hidden fees here. Our plans include Unlimited Orders and Unlimited Customers. While other loyalty platforms limit your growth, we built Lootly to scale with your business without charging you more.'},

					{question: 'Can I import existing customers into Lootly?', answer: 'Absolutely! With Lootly, you simply need to upload a file with your current customers into your account and you’re done. There are no limits on how many customers you can import.<br> &nbsp;'},

					{question: 'What currency are plans charged in?', answer: 'All plans are charged in USD regardless of your location.<br> &nbsp;'},

					{question: 'Do I need to sign a Contract?', answer: 'Nope! At Lootly, we’re all about simplicity and transparency. Our plans are month-to-month, with no contracts or setup fees.'},

					{question: 'How easy is this to setup?', answer: 'Lootly is incredibly simple to install to your store, as it only takes a few clicks and less than 1 minute of your time. Once installed, our team is always available via Live Chat, Email or Phone for helping answer any questions you have.'},

					{question: 'Can I change plans at any time?', answer: 'Sure! Since Lootly does not use contracts, you can easily upgrade your plan at any time from your account. This only takes a few seconds, and then you’re all set. <br><br>'},
				]
			},
			methods: {
				calcPrice: function (price) {
					var formatter = new Intl.NumberFormat('en-US', {
					    style: 'currency',
					    currency: 'USD',
					    minimumFractionDigits: 0,
					    maximumFractionDigits: 0
					});

					if(!this.yearly){
						return formatter.format(price).toString().replace('$', '');
					} else {
						return formatter.format(price*12*(1-this.yearlyDiscount)).toString().replace('$', '');
					}
				}
			}
		})
		$("[data-toggle=popover]").each(function(i, obj) {
			$(this).popover({
				trigger: 'hover',
				placement: 'bottom'
			});
		});
	</script>
@endsection

