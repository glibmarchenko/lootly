@extends('website.layout')

@section('title', 'Status')

@section('meta')
    <meta name="title" content="Status | Lootly">
    <meta name="description" content="Welcome to Lootly's real-time and historical data for our system performance.">
    <meta name="keywords" content="Lootly performance">
    <meta property="og:title" content="Status | Lootly">
    <meta property="og:image" content="https://s3.amazonaws.com/lootly-website-assets/img/logo-black.png">
    <meta property="og:url" content="{{ url('/status') }}">
    <meta property="og:description" content="Welcome to Lootly's real-time and historical data for our system performance.">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('content')
	<section class="head-section">
		<h1>Performance & Uptime</h1>
	</section>

	<section class="status sec-border-bottom md-sec">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="status-box overall">
						<p>All Systems Operational</p>
					</div>
					<div class="status-box">
						<p>Lootly Admin</p>
						<span class="success">Operational</span>
					</div>
					<div class="status-box">
						<p>Lootly App</p>
						<span class="success">Operational</span>
					</div>
					<div class="status-box">
						<p>Lootly Database</p>
						<span class="success">Operational</span>
					</div>
					<div class="status-box">
						<p>Notifications</p>
						<span class="success">Operational</span>
					</div>
					<div class="status-box">
						<p>Webhooks</p>
						<span class="success">Operational</span>
					</div>
					<div class="past-incidents">
						<h5>Past Incidents</h5>
						<p>No incidents from the past 30 days</p>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection