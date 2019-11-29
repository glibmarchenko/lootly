@extends('website.layout')

@section('title', 'About Us')

@section('meta')
    <meta name="title" content="About Us | Lootly">
    <meta name="description" content="Lootly helps eCommerce brands grow their revenue, retention rate, and customer happiness with a loyalty, rewards and referrals program.">
    <meta name="keywords" content="Lootly about, about lootly, lootly, lootly rewards, lootly referrals">
    <meta property="og:title" content="About Us | Lootly">
    <meta property="og:image" content="{{ url('images/assets/main/company/our-branding.svg') }}">
    <meta property="og:url" content="{{ url('/about') }}">
    <meta property="og:description" content="Lootly helps eCommerce brands grow their revenue, retention rate, and customer happiness with a loyalty, rewards and referrals program.">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('content')
	<section class="head-section">
		<h1>About Us</h1>
	</section>

    @include('website.company._nav')

	<section class="intro-section sec-border-bottom md-sec">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<h3>We're on a mission to redefine the Loyalty industry</h3>
					<p>Loyalty programs are a great revenue driver because they empower consumers to be excited about buying and sharing their experience with friends & family. However, we feel our industry is broken - High Prices, Contracts, and Lack of Features are holding eCommerce brands from reaching their true potential.</p>
					<p>At Lootly, we believe there is a better way to handle Loyalty Marketing for eCommerce. Loyalty programs should be easily accessible, feature-rich and at an affordable price - that's our vision.</p>
				</div>
			</div>
		</div>
	</section>

	<section class="article sec-border-bottom md-sec">
		<div class="container">
			<div class="row">
				<div class="col-12 col-sm-6">
					<img src="{{ url('images/assets/main/company/our-founding.png') }}" style="border-radius: 8px;">
				</div>
				<div class="col-12 col-sm-6">
					<h3>What is Lootly?</h3>
					<p>Lootly, a TrustSpot company, is an incentive marketing platform helping eCommerce brands grow their revenue & customer happiness through a Loyalty & Rewards program.</p>
					<p>While other platforms are plagued by limitations and high prices, we built Lootly to be an easy to use solution for any business size.</p>
					<p>Lootly is headquartered in Orlando, FL and was founded in 2018.</p>
				</div>
			</div>
			<div class="row" style="margin-top: 60px">
				<div class="col-12 col-sm-6">
					<h3>Our Branding</h3>
					<p>Lootly is derived from the word "Loot", which is commonly used to describe treasure, money, rewards and gold.</p>
					<p>Since Lootly is based in Florida, which is home to some of the largest Pirate museums in the world, we wanted to utilize some type of word indicating treasure for our next project. After discussing with the team, we agreed upon Lootly and the company was founded.</p>
					<p>Early on we wanted our branding to go far beyond just a logo, and decided to design special graphics, icons and illustrations showcasing the concept throughout the website & platform.</p>
				</div>
				<div class="col-12 col-sm-6">
					<img class="m-t-30" src="{{ url('images/assets/main/company/our-branding.svg') }}">
				</div>
			</div>
		</div>
	</section>

	<section class="leadership-team md-sec">
	   <div class="container">
	      <h3>Leadership Team</h3>
	      <div class="row">
	         <div class="col-12">
	            <ul class="team-member clearfix">
	               <li>
	                  <figure>
	                     <img src="{{ url('images/assets/main/company/ryan.png') }}" class="img-responsive" alt="lootly-ryan">
	                  </figure>
	                  <strong>Ryan</strong>
	                  <span>Co-Founder / CEO</span>
	               </li>
	               <li>
	                  <figure>
	                     <img src="{{ url('images/assets/main/company/larry.png') }}" class="img-responsive" alt="lootly-larry">
	                  </figure>
	                  <strong>Larry</strong>
	                  <span>Co-Founder / CTO</span>
	               </li>
	               <li>
	                  <figure>
	                     <img src="{{ url('images/assets/main/company/caitlin.png') }}" class="img-responsive" alt="lootly-caitlin">
	                  </figure>
	                  <strong>Caitlin</strong>
	                  <span>CFO</span>
	               </li>
	               <li>
	                  <figure>
	                     <img src="{{ url('images/assets/main/company/chris.png') }}" class="img-responsive" alt="lootly-chris">
	                  </figure>
	                  <strong>Chris</strong>
	                  <span>Director of Growth</span>
	               </li>
	               <li>
	                  <figure>
	                     <img src="{{ url('images/assets/main/company/bonnie.png') }}" class="img-responsive" alt="lootly-bonnie">
	                  </figure>
	                  <strong>Bonnie</strong>
	                  <span>Director of Support</span>
	               </li>
	               <li>
	                  <figure>
	                     <img src="{{ url('images/assets/main/company/kelly.png') }}" class="img-responsive" alt="lootly-kelly">
	                  </figure>
	                  <strong>Kelly</strong>
	                  <span>Director of Success</span>
	               </li>
	            </ul>
	         </div>
	      </div>
	   </div>
	</section>
	<section class="join-our-team">
		<div class="container md-sec">
			<div class="card">
				<h4>Join the Lootly Team</h4>	
				<p>Help us to redefine the Loyalty & Rewards space for eCommerce Brands.</p>
				<a href="/careers" class="btn btn-block btn-lg btn-blue">View Careers</a>	
			</div>
		</div>
	</section>
@endsection