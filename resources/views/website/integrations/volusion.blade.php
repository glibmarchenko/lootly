@extends('website.layout')

@section('title', 'Volusion Integration')

@section('page-name', 'full-page')

@section('meta')
    <meta name="title" content="Volusion Integration | Lootly">
    <meta name="description" content="">
    <meta name="keywords" content="volusion, volusion referrals, volusion loyalty, volusion rewards, volusion lootly">
    <meta property="og:title" content="Volusion Integration | Lootly">
    <meta property="og:image" content="{{ url('images/assets/main/integrations/volusion.svg') }}">
    <meta property="og:url" content="{{ url('/apps/volusion') }}">
    <meta property="og:description" content="">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('content')
    <section class="page-overview" style="min-height: 495px;">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="sec-content text-left">
                        <h1>Volusion Integration</h1>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <span class="flex-center">
                        <img class="m-auto" src="{{url('images/assets/main/integrations/volusion.svg')}}">
                    </span>
                </div>
            </div>
        </div>
    </section>

    @include('website._partials._request-demo-section')
    
@endsection
