<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    @section('title', 'Unsubscribed page')
    @include('layouts._head')
</head>
<body>

<div id="app" class="h-100">
    <div id="main-navbar">
        <nav class="navbar fixed-top navbar-expand-lg">
            <div class="col-md-2 col-8 col-sm-6 p-l-0">
                <div class="navbar-brand color-white">
                    <img src="{{ $merchant->logo_url ?: url(config('app.logo')) }}">
                </div>
            </div>
            <button class="navbar-toggler d-block d-md-none" type="button" data-toggle="collapse" data-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation" onclick="showSidebar(this)">
                <span class="fa fa-bars"></span>
            </button>
        </nav>
    </div>

    <div class="container-fluid contents-wrapper">
        <div class="row">
            <div class="contents-block">
                <div class="alert alert-success m-t-20">
                    Your email has been unsubscribed from <strong>{{ $merchant->name }}</strong> loyalty emails
                </div>
            </div>
        </div>

    </div>
</div>
</body>
</html>
