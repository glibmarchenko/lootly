<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    
    @include('layouts._head')

</head>
<body>

    <div id="app" class="h-100">

        <div id="main-navbar" class="white-nav">
            <nav class="navbar fixed-top navbar-expand-lg">
                <div class="col-md-2 col-6 col-sm-3 p-l-0">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img src="{{ asset('images/logos/logo-black.png') }}">
                    </a>
                </div>
            </nav>
        </div>    

        <div class="container onboarding-wizard">
            @yield('content')
        </div>

    </div>

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}"></script>
    
    @yield('scripts')
</body>
</html>