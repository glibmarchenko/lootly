<!--Header Section Start-->
<header id="header" class="header @yield('navbar')">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-12">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <a href="/" class="logo navbar-brand">
                        <span class="img"></span>
                    </a>

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="fa fa-bars"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="mainNavbar">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Features
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="/features/points-rewards">Points & Rewards</a>
                                    <a class="dropdown-item" href="/features/vip">VIP Program</a>
                                    <a class="dropdown-item" href="/features/referrals">Referrals</a>
                                    <a class="dropdown-item" href="/features/insights">Insights</a>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/pricing">Pricing</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/apps">Integrations</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Company
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="/about">About</a>
                                    <a class="dropdown-item" href="/faq">FAQ</a>
                                    <a class="dropdown-item" href="/contact">Contact</a>
                                    <a class="dropdown-item" href="/resources">Resources</a>
                                    <a class="dropdown-item" href="http://support.lootly.io/">Support</a>
                                </div>
                            </li>
                        </ul>
                        <div class="form-inline">
                            <div class="">
                                @if(Auth::guest())
                                    <a href="/login" class="btn btn-link">Login</a>
                                    <a href="/signup/" class="btn btn-signup">Sign Up Free</a>
                                    <button onclick="requestDemo()" class="btn btn-req-demo">Request a Demo</button>
                                @else
                                    <a href="{{ url('dashboard') }}" class="btn btn-success btn-dash">My Dashboard</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</header>
<!--Header Section End-->

