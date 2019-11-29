<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    
    @include('website._partials._head')

</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TQLL3SK"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <div id="app" class="@yield('page-name')">

        @include('website._partials._header')

        {{-- @include('website._partials._request-demo') --}}

        <div class="container-fluid contents" style="padding: 0; overflow: hidden;">
            @yield('content')
        </div>

        @include('website._partials._footer')
    </div>

    <!-- Scripts -->
    <script src="{{ url('js/main.js') }}"></script>
    @yield('scripts')

</body>
</html>