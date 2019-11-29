@extends('website.layout')

@section('title', $resource->title)

@section('page-name', 'blog')

@section('meta')
    <meta name="title" content="{{ $resource->title }}">
    <meta name="description" content="{{ $resource->meta_description }}">
    <meta name="author" content="{{ $resource->author->name ?? '' }}">

    <meta property="og:title" content="{{ $resource->title }}">
    <meta property="og:description" content="{{ $resource->meta_description }}">

    <meta property="og:url" content="https://www.lootly.io">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('content')

    <section class="head-section">
        <div class="back-to-articles">
            <a class="" href="{{ url('resources') }}">
                <i class="fa fa-arrow-left"></i> {{ __('Back to articles') }}
            </a>
        </div>
        <h1>
            {{ $resource->title }}
        </h1>
    </section>

    <section class="titles-sec border-bottom">
        <div class="container">
            <div class="row">
                <div class="col-4 text-center">
                    <h5><b>{{ __('Industry') }}:</b> {{ $resource->caseStudy->industry }}</h5>
                </div>
                <div class="col-4 text-center">
                    <h5><b>{{ __('Platform') }}:</b> {{ $resource->caseStudy->platform }}</h5>
                </div>
                <div class="col-4 text-center">
                    <h5><b>{{ __('Favorite Feature') }}:</b> {{ $resource->caseStudy->favorite_feature }}</h5>
                </div>
            </div>
        </div>
    </section>

    <section class="">
        <div class="container">
            <div class="row m-b-20">
                <div class="col-4 text-center">
                    <h2 class="overview-title">{{ $resource->caseStudy->stat_first_title }}</h2>
                    <p>{{ $resource->caseStudy->stat_first_value }}</p>
                </div>
                <div class="col-4 text-center">
                    <h2 class="overview-title">{{ $resource->caseStudy->stat_second_title }}</h2>
                    <p>{{ $resource->caseStudy->stat_second_value }}</p>
                </div>
                <div class="col-4 text-center">
                    <h2 class="overview-title">{{ $resource->caseStudy->stat_third_title }}</h2>
                    <p>{{ $resource->caseStudy->stat_third_value }}</p>
                </div>
            </div>
            <div class="row m-t-50">
                <div class="col-12">
                    <div class="quotes quote-top">
                        <p class="text-center f-s-17">
                            <i class="i-left-quote"></i>{{ strip_tags($resource->caseStudy->top_quote) }}<i class="i-right-quote"></i>
                        </p>
                    </div>
                    <div class="sm-company-box box-customer">
                        <img src="{{ asset('storage/' . $resource->mini_image) }}">
                        <div>
                            <h5>{{ $resource->caseStudy->customer_name }}</h5>
                            <p>{{ $resource->caseStudy->position_title }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="article md-sec">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <div class="article-company-body">
                        <h3 class="blue-color m-t-20">{{ __('Company Overview') }}</h3>
                        {!! $resource->caseStudy->company_body !!}
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <img src="{{ asset('storage/' . $resource->caseStudy->company_image) }}" class="article-company-image">
                </div>
            </div>
        </div>
    </section>

    <section class="article sec-gray">
        <div class="container md-sec">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <div class="flex-center">
                        <div class="quotes quote-challenge sm-quotes">
                            <p class="f-s-17">
                                <i class="i-left-quote"></i>{{ strip_tags($resource->caseStudy->challenge_quote) }}<i class="i-right-quote"></i>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="article-challenge-body">
                        <h3 class="blue-color">{{ __('Challenge') }}</h3>
                        {!! $resource->caseStudy->challenge_body !!}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="article">
        <div class="container md-sec">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <div class="article-solution-body">
                        <h3 class="blue-color">{{ __('Solution') }}</h3>
                        {!! $resource->caseStudy->solution_body !!}
                        <div class="quotes quotes-solution m-t-30 sm-quotes">
                            <p class="f-s-17">
                                <i class="i-left-quote"></i>{{ strip_tags($resource->caseStudy->solution_quote) }}<i class="i-right-quote"></i>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 text-center">
                    <img src="{{ asset('storage/' . $resource->caseStudy->solution_image) }}">
                </div>
            </div>
        </div>
    </section>

    <section class="article sec-gray">
        <div class="container md-sec">
            <div class="row">
                <div class="col-12 col-sm-6 sm-order-2">
                    <div class="flex-center">
                        <img src="{{ asset('storage/' . $resource->caseStudy->results_image) }}">
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div style="max-width: 480px">
                        <h3 class="blue-color">{{ __('Results') }}</h3>
                        {!! $resource->caseStudy->results_body !!}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="request-demo-section">
        <div class="container md-sec">
            <div class="row">
                <div class="col-12">
                    <div class="">
                        <h2>{{ __('Discover how Lootly can grow your business') }}</h2>
                        <div class="">
                            <button type="button" class="btn btn-primary btn-lg" onclick="requestDemo()">
                                {{ __('Request a Demo') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
