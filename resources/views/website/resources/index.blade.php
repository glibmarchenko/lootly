@extends('website.layout')

@section('title', 'Resources')

@section('meta')
    <meta name="title" content="Resources | Lootly">
    <meta name="description" content="Build customer loyalty, increase retention, and scale your brand. It is all possible with Lootly.">
    <meta name="keywords" content="loyalty, referrals, rewards, ecommerce, rewards program">
    <meta property="og:title" content="Resources | Lootly">
    <meta property="og:image" content="https://s3.amazonaws.com/lootly-website-assets/img/logo-black.png">
    <meta property="og:url" content="{{ url('/resources') }}">
    <meta property="og:description" content="Build customer loyalty, increase retention, and scale your brand. It is all possible with Lootly.">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('content')
	<section class="head-section">
		<h1>Resources</h1>
	</section>

	<section class="inner-navbar">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<ul class="tabs-nav">

						<li class="active">
							<a section="all" title="All" href="javascript::void(0)">All</a>
						</li>

						@foreach ($categories as $category)

							<li>
								<a section="{{ $category->slug }}" title="{{ $category->name }}" href="javascript::void(0)">
									{{ $category->name }}
								</a>
							</li>

						@endforeach

					</ul>
				</div>
			</div>
		</div>
	</section>
	<div class="sections-wrapper tabs-wrapper resources">

		@foreach ($categories as $category)

			<section class="{{ $category->slug }}">
				<div class="container">
					<h3 class="sec-title">{{ $category->name }}</h3>
					<div class="row">

						@if ($category->isCaseStudies())

							<div class="col-12 col-sm-6 col-md-4">
								<div class="card card-case-studies">
									<div class="card-img" style="background-image: url('https://assets.trustspot.io/img/resource-center/Resource+Mini+Graphic.png');"></div>
									<div class="card-body">
										<h5 class="card-title">Audi Mods grows revenue with loyalty & referrals</h5>
										<div class="card-text mb-3">Discover how Audi Mods increased referral purchase rates by 21%, while achieving a 38x ROI.</div>
										<a href="{{ url('resources/audi-mods-case-study') }}" class="btn btn-block btn-outline-dark">View Story</a>
									</div>
								</div>
							</div>

						@endif

						@if ($category->isEcommerce())

							<div class="col-12 col-sm-6 col-md-4">
								<div class="card card-ecommerce">
									<div class="card-img" style="background-image: url('https://assets.trustspot.io/img/resource-center/customer-photos.png');"></div>
									<div class="card-body">
										<h5 class="card-title">Why Repeat Customers Are Cheaper to Acquire Than New Ones</h5>
										<div class="card-text mb-3">Learn why increasing repeat purchases should be a top priority for e-commerce companies.</div>
										<a href="{{ url('resources/why-repeat-customers-are-cheaper') }}" class="btn btn-block btn-outline-dark">Read More</a>
									</div>
								</div>
							</div>

						@endif

						@foreach ($resources as $resource)
							@if ($resource->isPublished() && $resource->category_id === $category->id)

								<div class="col-12 col-sm-6 col-md-4">
									<div class="card card-{{ $category->slug }}">
										<div class="card-img" style="background-image: url('{{ asset('storage/' . $resource->mini_image) }}');"></div>
										<div class="card-body">
											<h5 class="card-title">{{ $resource->title }}</h5>
											<div class="card-text mb-3">{!! $resource->description !!}</div>
											<a href="{{ route('website.resources.show', ['id' => $resource->id, 'slug' => $resource->slug]) }}" class="btn btn-block btn-outline-dark">
												{{ $category->isCaseStudies() ? __('View Story') : __('Read More') }}
											</a>
										</div>
									</div>
								</div>

							@endif
						@endforeach

					</div>
				</div>
			</section>

		@endforeach

	</div>
@endsection

@section('scripts')
	<script type="text/javascript">
		if(findGetParameter('q')) {
			document.querySelector('[section="'+findGetParameter('q')+'"]').click()
		}
	</script>
@endsection
