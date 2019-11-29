@extends('website.layout')

@section('title', 'Press')

@section('meta')
    <meta name="title" content="Press | Lootly">
    <meta name="keywords" content="lootly, contact lootly, lootly phone number, lootly email">
    <meta property="og:title" content="Press | Lootly">
    <meta property="og:image" content="https://s3.amazonaws.com/lootly-website-assets/img/logo-black.png">
    <meta property="og:url" content="{{ url('/press') }}">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('nav-items')
	<li class="{{ Request::is('press') ? 'active': '' }}">
		<a title="Press" href="/press">Press</a>
	</li>
@endsection

@section('content')
	<section class="head-section">
		<h1>Press</h1>
	</section>

    @include('website.company._nav')

	<section class="article sec-border-bottom md-sec">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<h4>Media Inquiries</h4>
					<p class="m-b-0">Interested in learning more about Lootly? <a href="{{ url('/request-demo') }}">Click Here</a> to Request a Demo or click the bottom right Live Chat button to ask a question.</p>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<h4 class="m-b-15">Keep in Touch </h4>
					<a href="{{ url('resources') }}" class="d-block">Lootly Resource Center</a>
					<a href="https://www.facebook.com/lootly.io" class="d-block">Lootly Facebook</a>
					<a href="https://www.linkedin.com/company/lootly/about/" class="d-block">Lootly LinkedIn</a>
					<a href="https://www.crunchbase.com/organization/lootly-io" class="d-block">Lootly CrunchBase</a>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<h4 class="m-b-15">Compare Lootly</h4>
					<a href="{{ url('compare/smile') }}" class="d-block">Lootly vs Smile.io</a>
					<a href="{{ url('compare/swell-rewards') }}" class="d-block">Lootly vs Swell Rewards</a>
					<a href="{{ url('compare/loyalty-lion') }}" class="d-block">Lootly vs Loyalty Lion</a>
				</div>
			</div>
		</div>
	</section>
@endsection