@extends('website.layout')

@section('title', 'Customers')

@section('content')
	<section class="head-section">
		<h1>Our Customers</h1>
	</section>

    @include('website.company._nav')

    <div class="top-brands-nav">
        <ul class="tabs-nav">
            <li class="active">
                <a section="all">All</a>
            </li>
            <li>
                <a section="apparel-fashion" class="">Apparel & Fashion</a>
            </li>
            <li>
                <a section="electronics" class="">Electronics, Software & Telecom</a>
            </li>
            <li>
                <a section="finance" class="">Finance</a>
            </li>
            <li>
                <a section="food-beverage" class="">Food & Beverage</a>
            </li>
            <li>
                <a section="medical" class="">Health, Beauty & Medical</a>
            </li>
            <li>
                <a section="media-entertainment" class="">Media & Entertainment</a>
            </li>
            <li>
                <a section="nutrition" class="">Nutrition & Supplements</a>
            </li>
            <li>
                <a section="specialty" class="">Specialty</a>
            </li>
        </ul>
    </div>

    <div class="tabs-wrapper brands-wrapper">
	    <section class="top-brands apparel-fashion">
	        <div class="container">
	            <h2 class="brand-name">Apparel & Fashion</h2>
	            <div class="row">
	                <div class="col-sm-6 col-md-4">
	                    <div class="brand-card">
	                        <div class="brand-logo">
	                            <a href="">
	                                <h3>Amaryllis</h3>
	                            </a>
	                        </div>
	                        <div class="caption">
	                            <h5 class="brand-category">Women's Clothing</h5>
	                            <h4 class="brand-name">Amaryllis</h4>
	                            <div class="brand-reviews">
	                                <img src="{{ url('images/assets/main/company/filled-star.png') }}">
	                                <span>25,000 Reviews</span>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	                <div class="col-sm-6 col-md-4">
	                    <div class="brand-card">
	                        <div class="brand-logo">
	                            <a href="">
	                                <h3>Amaryllis</h3>
	                            </a>
	                        </div>
	                        <div class="caption">
	                            <h5 class="brand-category">Mens' Clothing</h5>
	                            <h4 class="brand-name">Amaryllis 2</h4>
	                            <div class="brand-reviews">
	                                <img src="{{ url('images/assets/main/company/filled-star.png') }}">
	                                <span>25,000 Reviews</span>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </section>
	    <section class="top-brands electronics">
	        <div class="container">
	            <h2 class="brand-name">Electronics, Software & Telecom</h2>

	            <div class="row">
	                <div class="col-sm-6 col-md-4">
	                    <div class="brand-card">
	                        <div class="brand-logo">
	                            <a href="">
	                                <h3>Electro</h3>
	                            </a>
	                        </div>
	                        <div class="caption">
	                            <h5 class="brand-category">Electronics</h5>
	                            <h4 class="brand-name">Electro</h4>
	                            <div class="brand-reviews">
	                                <img src="{{ url('images/assets/main/company/filled-star.png') }}">
	                                <span>25,000 Reviews</span>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </section>
	    <section class="top-brands finance">
	        <div class="container">
	            <h2 class="brand-name">Finance</h2>
	            <div class="row">
	                <div class="col-sm-6 col-md-4">
	                    <div class="brand-card">
	                        <div class="brand-logo">
	                            <a href="">
	                                <h3>Finance</h3>
	                            </a>
	                        </div>
	                        <div class="caption">
	                            <h5 class="brand-category">Finance</h5>
	                            <h4 class="brand-name">Finance</h4>
	                            <div class="brand-reviews">
	                                <img src="{{ url('images/assets/main/company/filled-star.png') }}">
	                                <span>25,000 Reviews</span>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </section>
	    <section class="top-brands food-beverage">
	        <div class="container">
	            <h2 class="brand-name">Food & Beverage</h2>
	            <div class="row">
	                <div class="col-sm-6 col-md-4">
	                    <div class="brand-card">
	                        <div class="brand-logo">
	                            <a href="">
	                                <h3>Food & Beverage</h3>
	                            </a>
	                        </div>
	                        <div class="caption">
	                            <h5 class="brand-category">Food & Beverage</h5>
	                            <h4 class="brand-name">Food & Beverage</h4>
	                            <div class="brand-reviews">
	                                <img src="{{ url('images/assets/main/company/filled-star.png') }}">
	                                <span>25,000 Reviews</span>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </section>
	    <section class="top-brands medical">
	        <div class="container">
	            <h2 class="brand-name">Health, Beauty & Medical</h2>
	            <div class="row">
	                <div class="col-sm-6 col-md-4 ">
	                    <div class="brand-card">
	                        <div class="brand-logo">
	                            <a href="">
	                                <h3>Health Care</h3>
	                            </a>
	                        </div>
	                        <div class="caption">
	                            <h5 class="brand-category">Medical</h5>
	                            <h4 class="brand-name">Health Care</h4>
	                            <div class="brand-reviews">
	                                <img src="{{ url('images/assets/main/company/filled-star.png') }}">
	                                <span>25,000 Reviews</span>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	                <div class="col-sm-6 col-md-4">
	                    <div class="brand-card">
	                        <div class="brand-logo">
	                            <a href="">
	                                <h3>Health Care 2</h3>
	                            </a>
	                        </div>
	                        <div class="caption">
	                            <h5 class="brand-category">Medical</h5>
	                            <h4 class="brand-name">Health Care</h4>
	                            <div class="brand-reviews">
	                                <img src="{{ url('images/assets/main/company/filled-star.png') }}">
	                                <span>25,000 Reviews</span>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </section>
	    <section class="top-brands media-entertainment">
	        <div class="container">
	            <h2 class="brand-name">Media & Entertainment</h2>
	            <div class="row">
	                <div class="col-sm-6 col-md-4">
	                    <div class="brand-card">
	                        <div class="brand-logo">
	                            <a href="">
	                                <h3>Media</h3>
	                            </a>
	                        </div>
	                        <div class="caption">
	                            <h5 class="brand-category">Media</h5>
	                            <h4 class="brand-name">Media</h4>
	                            <div class="brand-reviews">
	                                <img src="{{ url('images/assets/main/company/filled-star.png') }}">
	                                <span>25,000 Reviews</span>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </section>
	    <section class="top-brands nutrition">
	        <div class="container">
	            <h2 class="brand-name">GYM</h2>
	            <div class="row">
	                <div class="col-sm-6 col-md-4">
	                    <div class="brand-card">
	                        <div class="brand-logo">
	                            <a href="">
	                                <h3>GYM</h3>
	                            </a>
	                        </div>
	                        <div class="caption">
	                            <h5 class="brand-category">GYM</h5>
	                            <h4 class="brand-name">GYM</h4>
	                            <div class="brand-reviews">
	                                <img src="{{ url('images/assets/main/company/filled-star.png') }}">
	                                <span>25,000 Reviews</span>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </section>
	    <section class="top-brands specialty">
	        <div class="container">
	            <h2 class="brand-name">Specialty</h2>
	            <div class="row">
	                <div class="col-sm-6 col-md-4">
	                    <div class="brand-card">
	                        <div class="brand-logo">
	                            <a href="">
	                                <h3>Specialty</h3>
	                            </a>
	                        </div>
	                        <div class="caption">
	                            <h5 class="brand-category">Specialty</h5>
	                            <h4 class="brand-name">Specialty</h4>
	                            <div class="brand-reviews">
	                                <img src="{{ url('images/assets/main/company/filled-star.png') }}">
	                                <span>25,000 Reviews</span>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </section>
    </div>
@endsection
