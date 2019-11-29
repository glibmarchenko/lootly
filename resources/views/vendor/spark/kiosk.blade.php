@extends('spark::layouts.app')

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.4.6/mousetrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
@endsection

@section('content')
<spark-kiosk :user="user" inline-template>
    <div class="spark-screen container">
        <div class="row">
            <!-- Tabs -->
            <div class="col-md-3 spark-settings-tabs">
                <aside>

                    @include('spark::_partials._kiosk-nav')

                </aside>
            </div>

            <!-- Tab cards -->
            <div class="col-md-9">
                <div class="tab-content">
                    <!-- Announcements -->
                    <div role="tabcard" class="tab-pane active" id="announcements">
                        @include('spark::kiosk.announcements')
                    </div>

                    <!-- Metrics -->
                    <div role="tabcard" class="tab-pane" id="metrics">
                        @include('spark::kiosk.metrics')
                    </div>

                    <!-- User Management -->
                    <div role="tabcard" class="tab-pane" id="users">
                        @include('spark::kiosk.users')
                    </div>

                    <!-- Customer Management -->
                    <div role="tabcard" class="tab-pane" id="customers">
                        @include('spark::kiosk.customers')
                    </div>

                    <!-- Resource Center Management -->
                    <div role="tabcard" class="tab-pane" id="resources">
                        @include('spark::kiosk.resources')
                    </div>
                </div>
            </div>
        </div>
    </div>
</spark-kiosk>
@endsection
