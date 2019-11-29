@extends('website.layout')

@section('title', 'How to grow your store with a VIP Program')

@section('page-name', 'blog')

@section('content')
	<section class="head-section has-feature-image">
		<div class="back-to-articles">
			<a class="" href="{{ url('resources') }}">
				<i class="fa fa-arrow-left"></i> Back to articles
			</a>
		</div>
		<h1>How to grow your store with a VIP Program</h1>
		<p class="article-credit">Ryan Haidinger on November 4, 2018</p>
	</section>

	<img class="feature-image" src="{{ url('images/assets/main/blog/feature-image-1.png') }}">

	<section class="">
		<div class="container sm-sec">
			<div class="row">
				<div class="col-12">
					<h3 class="m-b-30">About TrustSpot</h3>
					<p>Dr. David Darmanin, Founder and CEO of <a target="_blank" href="https://www.hotjar.com/">HotJar</a>, recently spoke with us on our Founder Chats podcast. During his interview, it was crystal clear that David`s generalist personality was vital to <a target="_blank" href="https://www.hotjar.com/">HotJar`s</a> success. With all of his diverse work experience, David was able to grow <a target="_blank" href="https://www.hotjar.com/">HotJar</a> from zero to $10 million in yearly revenue. </p>

					<h3 class="m-t-50 m-b-30">About Ryan & Larry</h3>
					<p>Ryan Haidinger, Co-Founder and CEO of <a target="_blank" href="https://trustspot.io/">TrustSpot</a>, recently spoke with us on our Founder Chats podcast. During his interview, it was crystal clear that Ryan`s generalist personality was vital to <a target="_blank" href="https://trustspot.io/">TrustSpot`s</a> success. With all of his diverse work experience, Ryan was able to grow <a target="_blank" href="https://trustspot.io/">TrustSpot</a> from zero to $10 million in yearly revenue.</p>

					<div class="text-center">
						<img class="m-t-20 b-r-5" src="{{ url('images/assets/main/blog/larry-landscape.png') }}">
					</div>

					<p class="m-t-30">Ryan wore plenty of hats during his trek to starting TrustSpot. He arrived at law school with a background in design. He learned Coding while building a website for a client. He lead optimization and design at hardware performance company. </p>
				</div>
			</div>
		</div>
	</section>
	<section class="about-ryan">
		<div class="container">
			<div class="row">
				<div class="col-12">
                     <img src="{{ url('images/assets/main/company/ryan.png') }}" class="img-responsive" alt="lootly-ryan">
					<h4>Ryan Haidinger</h4>
					<p>Ryan is the Co-Founder & CEO of Lootly. When he's not creating new features or marketing campaigns, you can find Ryan at the beach.</p>
				</div>
			</div>
		</div>
	</section>
@endsection