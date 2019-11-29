@extends('website.layout')

@section('title', $resource->title)

@section('page-name', 'blog')

@section('meta')
    <meta name="title" content="{{ $resource->title }}">
    <meta name="description" content="{{ $resource->meta_description }}">
    <meta name="author" content="{{ $resource->author->name }}">

    <meta property="og:title" content="{{ $resource->title }}">
    <meta property="og:description" content="{{ $resource->meta_description }}">

    <meta property="og:url" content="https://www.lootly.io">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('content')

    <section class="head-section has-feature-image">
        <div class="back-to-articles">
            <a class="" href="{{ url('resources') }}">
                <i class="fa fa-arrow-left"></i> Back to articles
            </a>
        </div>
        <h1>{{ $resource->title }}</h1>
        <p class="article-credit">
            {{ $resource->author->name }} on {{ $resource->created_at->format('F d, Y') }}
        </p>
    </section>

    <img class="feature-image" src="{{ asset('storage/' . $resource->featured_image) }}">

    <section class="">
        <div class="container sm-sec">
            <div class="row">
                <div class="col-12">

                    {!! $resource->body !!}

                </div>
            </div>
        </div>
    </section>

    <section class="about-ryan">
        <div class="container">
            <div class="row">
                <div class="col-12">
                     <img src="{{ url($resource->author->photo) }}" class="img-responsive" alt="{{ $resource->author->name }}">
                    <h4>{{ $resource->author->name }}</h4>
                    <p>{{ $resource->author->note }}</p>
                </div>
            </div>
        </div>
    </section>

@endsection
