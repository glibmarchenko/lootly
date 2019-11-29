<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>

    @include('layouts._head')

</head>

<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TQLL3SK"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <div id="app" class="h-100">
        @include('_partials._nav')

        <div class="container-fluid contents-wrapper">
            <div class="row">
                <div class="sidebar-block p-l-0">
                    @include('_partials._sidebar')
                </div>
                <div class="contents-block">
                    @include('_partials._messages')

                    @yield('content')
                </div>
            </div>
            
        </div>
    </div>
    <!-- Scripts -->
    {{--<script src="/js/sweetalert.min.js"></script>--}}
    <script src="{{ url('js/app.js') }}"></script>
    @yield('scripts')
    @yield('modals')
</body>
</html>