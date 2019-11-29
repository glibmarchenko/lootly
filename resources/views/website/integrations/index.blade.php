@extends('website.layout')

@section('title', 'Integrations')

@section('page-name', 'full-page')

@section('meta')
    <meta name="title" content="Integrations | Lootly">
    <meta name="description" content="Expand your loyalty & rewards program with 1-click integrations from Lootly.">
    <meta name="keywords" content="loyalty, rewards, referrals, ecommerce, rewards program, lootly">
    <meta property="og:title" content="Integrations | Lootly">
    <meta property="og:image" content="{{ url('images/assets/main/integrations/integrations-banner.png') }}">
    <meta property="og:url" content="{{ url('/apps') }}">
    <meta property="og:description" content="Expand your loyalty & rewards program with 1-click integrations from Lootly.">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('content')
    <section class="page-overview">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content text-left">
                        <h1 style="max-width: 500px;">Connect with Lootly</h1>
                        <p class="description">Lootly apps allow you to integrate with your existing platform & marketing services in 1-click.</p>
                        <div class="btn-wrapper">
                            <a href="{{ url('pricing') }}" class="btn btn-signup f-s-16">Get Started</a>
                            <button onclick="requestDemo()" class="btn btn-req-demo f-s-16">Request a Demo</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <span class="main-image text-center m-auto">
                        <img src="{{url('images/assets/main/integrations/integrations-banner.png')}}">
                    </span>
                </div>
            </div>
        </div>
    </section>

	<div id="integrationsSection" class="skew-top-right">
		<div class="integrations-navbar">
			<ul class="integrations-nav">
				<li>
					<a :class="{'active': displayCategory == 'all'}" @click="displayCategory = 'all'" title="All">All</a>
				</li>
				<li>
					<a :class="{'active': displayCategory == 'eCommerce'}" @click="displayCategory = 'eCommerce'" title="eCommerce">eCommerce</a>
				</li>
				<li>
					<a :class="{'active': displayCategory == 'email-providers'}" @click="displayCategory = 'email-providers'" title="Email Providers">Email Providers</a>
				</li>
				<li>
					<a :class="{'active': displayCategory == 'social'}" @click="displayCategory = 'social'" title="Social Media">Social Media</a>
				</li>
				<li>
					<a :class="{'active': displayCategory == 'marketing'}" @click="displayCategory = 'marketing'" title="Marketing">Marketing</a>
				</li>
			</ul>
		</div>
		<section class="md-sec loader" v-cloak>
			<div class="container">
				<div class="row">
					<div class="col-12 col-sm-6 col-md-3 integration-block" v-for="integration in integrations" :category="integration.category" v-show="integration.category == displayCategory || displayCategory == 'all'">
						<div class="card" :class="integration.title">
							<button class="btn btn-status" v-if="integration.status == 'Coming Soon'">
								<span v-text="integration.status"></span>
							</button>
							<div class="card-body">
								<div class="card-img">
									<img :src="integration.img">
								</div>
								<h5 class="card-title" v-text="integration.title"></h5>
								<p class="platform-type" v-text="integration.type"></p>
								<p class="card-text" v-html="integration.desc"></p>
							</div>
							<div class="card-footer">
								<span v-if="integration.status" class="btn" href="javascript::void(0)">
									@{{ integration.status }}
								</span>
								<a v-else :href="integration.url" class="btn btn-link">Learn more</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>

    @include('website._partials._request-demo-section')
@endsection

@section('scripts')
	<script type="text/javascript" src="{{ url('js/plugins/vue.min.js') }}"></script>
	<script>
		var page = new Vue({
			el: '#integrationsSection',
			data: {
				displayCategory: 'all',
				integrations: [
					{
						category: 'eCommerce',
						img: '{{ url("images/assets/main/integrations/shopify.png") }}',
						title: 'Shopify',
						type: 'eCommerce Platform',
						desc: 'Shopify is a leading cloud-based eCommerce platform utilized by more than 600k stores.',
						url: '{{ url("apps/shopify") }}'
					},
					{
						category: 'eCommerce',
						img: '{{ url("images/assets/main/integrations/bigCommerce.png") }}',
						title: 'BigCommerce',
						type: 'eCommerce Platform',
						desc: 'BigCommerce is a feature rich and robust eCommerce platform for brands.',
						url: '{{ url("apps/bigcommerce") }}'
					},					
					{
						category: 'eCommerce',
						img: '{{ url("images/assets/main/integrations/magento.png") }}',
						title: 'Magento 1',
						type: 'eCommerce Platform',
						desc: 'Magento Commerce is one of the largest enterprise platforms for growing brands.',
						url: '{{ url("apps/magento") }}'
					},
					{
						category: 'eCommerce',
						img: '{{ url("images/assets/main/integrations/wooCommerce.png") }}',
						title: 'WooCommerce',
						type: 'eCommerce Platform',
						desc: 'WooCommerce is an open-source platform helping stores expand their business.',
						url: '{{ url("apps/woocommerce") }}'
					},
					{
						category: 'eCommerce',
						img: '{{ url("images/assets/main/integrations/volusion.svg") }}',
						title: 'Volusion',
						type: 'eCommerce Platform',
						desc: 'Ecommerce for everyone. Everything you need to sell online.',
						url: '{{ url("apps/volusion") }}'
					},
					{
						status: ' ',
						category: 'marketing',
						img: '{{ url("images/assets/main/integrations/zapier.svg") }}',
						title: 'Zapier',
						type: 'Marketing',
						desc: 'Zapier adds powerful automation to Lootly by connecting you to more than 1,500 web apps.',
						url: ''
					},
					{
						category: 'marketing',
						img: '{{ url("images/assets/main/integrations/trustspot.png") }}',
						title: 'TrustSpot',
						type: 'Marketing',
						desc: 'TrustSpot provides brands with a complete solution to grow their customer reviews, photos, and product Q&A.',
						url: '{{ url("apps/trustspot") }}'
					},
					{
						status: ' ',
						category: 'social',
						img: '{{ url("images/assets/main/integrations/instagram.png") }}',
						title: 'Instagram',
						type: 'Social Media',
						desc: 'Encourage customers to follow your brand on Instagram and reward them with points.',
						url: '{{ url("apps/instagram") }}'
					},
					{
						status: ' ',
						category: 'social',
						img: '{{ url("images/assets/main/integrations/facebook.png") }}',
						title: 'Facebook',
						type: 'Social Media',
						desc: 'Lootly offers the ability to Like your brand or Share on Facebook to receive points.',
						url: '{{ url("apps/facebook") }}'
					},
					{
						status: ' ',
						category: 'social',
						img: '{{ url("images/assets/main/integrations/twitter.png") }}',
						title: 'Twitter',
						type: 'Social Media',
						desc: 'Reward your customers with points for following or sharing your brand on Twitter.',
						url: '{{ url("apps/twitter") }}'
					},
					{
						status: 'Coming Soon',
						category: 'email-providers',
						img: '{{ url("images/assets/main/integrations/klaviyo.png") }}',
						title: 'Klaviyo',
						type: 'Email Providers',
						desc: 'Level-up email and social campaigns with data-driven marketing.',
						url: '{{ url("apps/klaviyo") }}'
					},
					{
						status: 'Coming Soon',
						category: 'email-providers',
						img: '{{ url("images/assets/main/integrations/mailchimp.png") }}',
						title: 'Mailchimp',
						type: 'Email Providers',
						desc: 'Mailchimp is an email marketing platform aimed at helping small businesses grow.',
						url: '{{ url("apps/mailchimp") }}'
					},
					{
						status: 'Coming Soon',
						category: 'email-providers',
						img: '{{ url("images/assets/main/integrations/bronto.png") }}',
						title: 'Bronto',
						type: 'Email Providers',
						desc: 'Bronto is a sophisticated marketing automation tool to help grow store revenue.',
						url: '{{ url("apps/bronto") }}'
					},
				]
			}
		})
	</script>
@endsection