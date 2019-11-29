@extends('website.layout')

@section('title', 'Careers')

@section('meta')
    <meta name="title" content="Careers | Lootly">
    <meta name="description" content="Love helping eCommerce brands grow? Join us at Lootly to help to redefine the Loyalty, Rewards and Referrals marketing space.">
    <meta name="keywords" content="lootly careers, lootly ecommerce, grow with lootly, lootly, lootly jobs">
    <meta property="og:title" content="Careers | Lootly">
    <meta property="og:image" content="{{ url('images/assets/main/company/culture/culture-2.png') }}">
    <meta property="og:url" content="{{ url('/careers') }}">
    <meta property="og:description" content="Love helping eCommerce brands grow? Join us at Lootly to help to redefine the Loyalty, Rewards and Referrals marketing space.">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('content')
	<section class="head-section">
		<h1>Careers</h1>
	</section>

    @include('website.company._nav')

	<section class="intro-section sec-border-bottom md-sec">
		<div class="container" style="max-width: 900px;">
			<div class="row">
				<div class="col-12">
					<h3>About Lootly</h3>
					<p>At Lootly, we believe there is a better way to handle Loyalty Marketing for eCommerce. Loyalty programs should be easily accessible, feature-rich and at an affordable price - that's our vision.</p>
					<p>Join us on this journey by helping eCommerce brands around the world grow their revenue and consumer happiness while having fun and doing what you love.</p>
				</div>
			</div>
		</div>
	</section>

	<section class="careers-benefits sec-border-bottom md-sec">
		<div class="container">
			<h3 class="text-center">Benefits & Perks</h3>
			<div class="row">
				<div class="col-12 col-sm-4">
					<div class="card">
						<div class="card-body">
							<span class="hammmocks"></span>
							<p>Unlimited PTO Vacation</p>
						</div>
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="card">
						<div class="card-body">
							<span class="money"></span>
							<p>Competetive Salaries</p>
						</div>
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="card">
						<div class="card-body">
							<span class="kitchen"></span>
							<p>Stocked Kitchen</p>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12 col-sm-4">
					<div class="card">
						<div class="card-body">
							<span class="growth"></span>
							<p>401k & Benefits</p>
						</div>
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="card">
						<div class="card-body">
							<span class="team-work"></span>
							<p>Team Activities</p>
						</div>
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="card">
						<div class="card-body">
							<span class="bike"></span>
							<p>Commuter Reimbursment</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="our-culture md-sec">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="sm-sec">
						<h3 class="text-center">Our Culture</h3>
						<p>We're looking for candidates who are mission-focused, detail oriented, and above all love the work they do. We love hiring passionate people who want to make a difference.</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="gallery">
						<div class="row">
							<div class="col-6 col-sm-3">
								<img src="{{ url('images/assets/main/company/culture/culture-1.png') }}">
							</div>
							<div class="col-6 col-sm-3">
								<img src="{{ url('images/assets/main/company/culture/culture-2.png') }}">
							</div>
							<div class="col-6 col-sm-3">
								<img src="{{ url('images/assets/main/company/culture/culture-3.png') }}">
							</div>
							<div class="col-6 col-sm-3">
								<img src="{{ url('images/assets/main/company/culture/culture-4.png') }}">
							</div>
							<div class="col-6 col-sm-3">
								<img src="{{ url('images/assets/main/company/culture/culture-5.png') }}">
							</div>
							<div class="col-6 col-sm-3">
								<img src="{{ url('images/assets/main/company/culture/culture-6.png') }}">
							</div>
							<div class="col-6 col-sm-3">
								<img src="{{ url('images/assets/main/company/culture/culture-7.png') }}">
							</div>
							<div class="col-6 col-sm-3">
								<img src="{{ url('images/assets/main/company/culture/culture-8.png') }}">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="lootly-careers">
		<div class="container md-sec">
			<div class="row">
				<div class="col-12">
					<h3>Careers at Lootly</h3>
				</div>
			</div>
			<div class="row">
				<div class="col-12 col-sm-4">
					<div class="job-opening">
						<h5>Engineering</h5>
						<p>
							<a href="https://angel.co/trustspot-inc/jobs/329766-senior-software-engineer-php">Senior Software Engineer</a>
							<span>Orlando, FL</span>
						</p>
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="job-opening">
						<h5>Design</h5>
						<p>
							No current openings
						</p>
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="job-opening">
						<h5>Marketing</h5>
						<p>
							<a href="https://angel.co/trustspot-inc/jobs/329771-director-of-marketing">Marketing Manager</a>
							<span>Orlando, FL</span>
						</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12 col-sm-4">
					<div class="job-opening">
						<h5>Tech Ops</h5>
						<p>
							No current openings
						</p>
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="job-opening">
						<h5>Human Resources</h5>
						<p>
							No current openings
						</p>
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="job-opening">
						<h5>Support</h5>
						<p>
							<a href="https://angel.co/trustspot-inc/jobs/329770-success-manager">Implementation Manager</a>
							<span>Orlando, FL</span>
						</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12 col-sm-4">
					<div class="job-opening">
						<h5>Sales</h5>
						<p>No current openings</p>
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="job-opening">
						<h5>Business Development</h5>
						<p>No current openings</p>
					</div>
				</div>
				<div class="col-12 col-sm-4">
					<div class="job-opening">
						<h5>Internships</h5>
						<p>No current openings</p>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection