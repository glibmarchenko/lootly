@extends('website.layout')

@section('title', 'Contact Us')

@section('meta')
    <meta name="title" content="Contact Us | Lootly">
    <meta name="description" content="Talk to a human at Lootly instead of bots or tickets. Contact us by Live Chat or Phone.">
    <meta name="keywords" content="lootly, contact lootly, lootly phone number, lootly email">
    <meta property="og:title" content="Contact Us | Lootly">
    <meta property="og:image" content="https://s3.amazonaws.com/lootly-website-assets/img/logo-black.png">
    <meta property="og:url" content="{{ url('/contact') }}">
    <meta property="og:description" content="Talk to a human at Lootly instead of bots or tickets. Contact us by Live Chat or Phone.">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('content')
	<section class="head-section">
		<h1>Talk to a Human</h1>
	</section>

    @include('website.company._nav')

	<section class="article sec-border-bottom md-sec">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<h4>Have a question about Lootly? </h4>
					<p>The fastest way to contact our team is by using the Live Chat button at the bottom right of our site. Our team of Loyalty Experts is standing by to help answer any question you have.</p>
					<p>If you happen to contact us after hours (9am – 7pm EST), feel free to leave your name, email or phone, and one of our team members will contact you shortly.</p>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<h4>Looking to request a demo of Lootly? </h4>
					<p>We’d love to show you how Lootly can help grow your business, <a href="{{ url('/request-demo') }}">click here</a> to schedule a demo.</p>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<h4>Lootly Office</h4>
					<p>
						Lootly Inc. <br>
						1000 Legion Pl.<br>
						Orlando, FL 32801 United States<br>
						(877) 297-5938
					</p>
					<p><b>Support:</b> <a href="mailto:support@lootly.io">support@lootly.io</a> </p>
				</div>
			</div>
		</div>
	</section>
@endsection