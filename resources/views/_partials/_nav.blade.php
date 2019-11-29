<div id="main-navbar">
    <nav class="navbar fixed-top navbar-expand-lg">
        <div class="col-md-2 col-8 col-sm-6 p-l-0">
            <a class="navbar-brand color-white" href="{{ url('/dashboard') }}">
                <img src="{{ url(config('app.logo')) }}">
            </a>
        </div>
        <button class="navbar-toggler d-block d-md-none" type="button" data-toggle="collapse" data-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation" onclick="showSidebar(this)">
            <span class="fa fa-bars"></span>
        </button>

        <div class="navbar-right col-md-10 col-sm-9 col-6" v-cloak>
            <ul class="navbar-nav ml-auto pull-right">
                @if(Auth::guest())

                    <!-- Authentication Links -->
                    <li class="m-t-0"><a class="m-t-10" href="{{ route('login') }}">Login</a></li>
                    <li class="m-t-0"><a class="m-t-10" href="{{ route('register') }}">Register</a></li>
                @else

                    {{--<li class="d-none d-lg-block">
                        <a class="m-t-10" @click.prevent="connectShopify">Get Shopify App</a>
                        <a class="m-l-10" :href="auth_url" v-if="auth_url">Install App</a>
                    </li>--}}

                    <li class="account-select dropdown d-none d-md-block">
                        <a id="myAccounts" class="m-t-5 d-block" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <div class="form-control custom-select text-left">
                                <span v-if="currentStore.name">@{{currentStore.name }}</span>
                                <span v-else>My Accounts</span>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-bordered" aria-labelledby="myAccounts">
                            <a class="dropdown-item" :href="'/settings/{{ Spark::teamsPrefix() }}/'+ store.id +'/switch'" v-text="store.name" v-for="store in stores"
                               {{--@click.prevent="updateMerchant(store)"--}}></a>
                            <a class="dropdown-item" v-b-modal.create-account-modal>
                                <i class="icon-add"></i> Create Account
                            </a>
                        </div>
                    </li>
                    <li class="notification-dropdown dropdown d-none d-md-block">
                        <a class="" id="notifications" data-toggle="dropdown" aria-haspopup="true"
                           aria-expanded="false">
                            <span class="badge bell-badge badge-secondary">
                                <span v-text="announcement_count"></span>
                            </span>
                            <i class="fa fa-bell-o m-r-15 m-t-15" aria-hidden="true"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="notifications">
                            <div class="dropdown-header text-center">
                                <span class="bolder">Notifications</span>
                                <span class="badge bell-badge badge-secondary text-center">
                                    <span v-text="announcement_count"></span>
                                </span>
                            </div>
                            <ul class="p-l-0">
                                <li v-for="announcement in announcements" :id="announcement.id">
                                    <a href="">
                                        <span class="icon">
                                            <span class="icon-customers"></span>
                                        </span>
                                        <span class="text" v-text="announcement.body"></span>
                                        <span class="meta" v-text="announcement.created_at"></span>
                                        <span class="close"
                                              @click.prevent="removeAnnouncement(announcement.id)"><i
                                                    class="fa fa-times-circle" aria-hidden="true"></i></span>
                                    </a>
                                </li>
                            </ul>
                            <div class="dropdown-footer text-center">
                                <a href="" class="color-blue">Mark all as read</a>
                            </div>
                        </div>
                    </li>

                    <li class="user-account dropdown d-none d-md-block">
                        <a id="userAccount" data-toggle="dropdown" aria-haspopup="true"
                           aria-expanded="false">
                            <div class="user-account-avatar">
                                <span class="user-avatar">@{{user_name}}</span>
                            </div>
                            <div class="user-account-info">
                                <h5 :class="[currentStore.name? '' : 'm-t-10']">
                                    @{{user.first_name }} @{{user.last_name }}
                                </h5>
                                <p>@{{currentStore.name }}</p>
                            </div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="userAccount">
                            <a class="dropdown-item" href="{{ route('settings') }}">
                                <span class="icon-gear"></span> Account Settings
                            </a>

                            <a class="dropdown-item" href="{{ route('account.billing') }}">
                                <span class="icon-billings"></span> Billing
                            </a>
                            <a class="dropdown-item" href="http://support.lootly.io/">
                                <span class="icon-support"></span> Support
                            </a>
                            <div role="separator" class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('/logout') }}">
                                <span class="icon-logout"></span> Logout
                            </a>
                        </div>
                    </li>
                @endif
            </ul>
        </div>
    </nav>

    @include('_partials._create-account', ['switchOnSuccess' => 1])
</div>