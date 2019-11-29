<?php 
    if(!isset($merchant)){
        $merchantRepo = new App\Repositories\MerchantRepository;
        $merchant = $merchantRepo->getCurrent();
    }
?>
<nav id="main-sidebar" class="fixed-top">
    <ul class="list-unstyled components">
        <div class="accordion" id="sidebarMenu">
            <span> <!-- Dashboard -->
                <li class="">
                    <a class="{{ Request::is('dashboard') ? 'active': '' }}" href="{{route('dashboard')}}">
                        <span class="icon-home"></span> Dashboard
                    </a>
                </li>
            </span>
            <span> <!-- Points -->
                <li>
                    <a class="{{ Request::is('points/*') ? 'active': '' }}" 
                       data-toggle="collapse" 
                       data-target="#points">
                        <span class="icon-points"></span> Points
                    </a>
                </li>

                <div id="points" 
                     class="dropdown collapse {{ Request::is('points/*') ? 'show': '' }}"
                     data-parent="#sidebarMenu">

                    <li class="{{ Request::is('points/overview') ? 'active': '' }}">
                        <a href="{{ route('points.overview') }}">Overview</a>
                    </li>
                    <li class="{{ Request::is('points/earning') || Request::is('points/earning/*') ? 'active': '' }}">
                        <a href="{{ route('points.earning') }}">Earning</a>
                    </li>
                    <li class="{{ Request::is('points/spending') || Request::is('points/spending/*') ? 'active': '' }}">
                        <a href="{{ route('points.spending') }}">Spending</a>
                    </li>
                    <li class="{{ Request::is('points/activity') ? 'active': '' }}">
                        <a href="{{ route('points.activity') }}">Activity</a>
                    </li>
                    <li class="{{ Request::is('points/settings') ? 'active': '' }}">
                        <a href="{{ route('points.settings') }}">Settings</a>
                    </li>
                </div>
            </span>
            
            <span> <!-- Referrals -->
                <li>
                    <a class="{{ Request::is('referrals/*') ? 'active': '' }}"
                       @if($merchant->checkPermitionByTypeCode('ReferralProgram'))
                           data-toggle="collapse" data-target="#referrals"
                       @else
                           href="{{route('referrals.upgrade')}}"
                       @endif
                    >
                        <span class="icon-heart"></span> Referrals
                    </a>
                </li>
                <div id="referrals" 
                     class="dropdown collapse {{ Request::is('referrals/*') && !Request::is('referrals/upgrade') ? 'show': '' }}"
                     data-parent="#sidebarMenu">

                    <li class="{{ Request::is('referrals/overview') ? 'active': '' }}">
                        <a href="{{ route('referrals.overview') }}">Overview</a>
                    </li>
                    <li class="{{ Request::is('referrals/rewards') || Request::is('referrals/rewards/*') ? 'active': '' }}">
                        <a href="{{ route('referrals.reward') }}">Rewards</a>
                    </li>
                    <li class="{{ Request::is('referrals/sharing') ? 'active': '' }}">
                        <a href="{{ route('referrals.sharing') }}">Sharing</a>
                    </li>
                    <li class="{{ Request::is('referrals/activity') ? 'active': '' }}">
                        <a href="{{ route('referrals.activity') }}">Activity</a>
                    </li>
                    <li class="{{ Request::is('referrals/settings') ? 'active': '' }}">
                        <a href="{{ route('referrals.settings') }}">Settings</a>
                    </li>
                </div>
            </span>

            <span> <!-- VIP -->
                <li>
                    <a class="{{ Request::is('vip/*') ? 'active': '' }}"
                       @if($merchant->checkPermitionByTypeCode('VIP_Program'))
                           data-toggle="collapse" data-target="#vip"
                       @else
                           href="{{route('vip.upgrade')}}"
                       @endif
                    >
                        <span class="icon-vip"></span> VIP
                    </a>
                </li>
                <div id="vip"
                     class="dropdown collapse {{ Request::is('vip/*') && !Request::is('vip/upgrade') ? 'show': '' }}"
                     data-parent="#sidebarMenu">

                    <li class="{{ Request::is('vip/activity') ? 'active': '' }}">
                        <a href="{{ route('vip.activity') }}">Activity</a>
                    </li>
                    <li class="{{ Request::is('vip/members') ? 'active': '' }}">
                        <a href="{{ route('vip.members') }}">Members</a>
                    </li>
                    <li class="{{ Request::is('vip/tiers') || Request::is('vip/tiers/*') ? 'active': '' }}">
                        <a href="{{ route('vip.tiers') }}">Tiers</a>
                    </li>
                    <li class="{{ Request::is('vip/settings') ? 'active': '' }}">
                        <a href="{{ route('vip.settings') }}">Settings</a>
                    </li>
                </div>
            </span>
            
            <span> <!-- Display -->
                <li>
                    <a class="{{ Request::is('display/*') || Request::is('rewards/upgrade') ? 'active': '' }}"
                       data-toggle="collapse" 
                       data-target="#display">
                        <span class="icon-display"></span> Display
                    </a>
                </li>
                <div id="display" 
                     class="dropdown collapse {{ Request::is('display/*') || Request::is('rewards/upgrade') ? 'show': '' }}" 
                     data-parent="#sidebarMenu">

                    <li class="{{ Request::is('display/widget') || Request::is('display/widget/*') ? 'active': '' }}">
                        <a href="{{ route('display.widget') }}">Widget</a>
                    </li>
                    <li class="{{ Request::is('display/reward-page') || Request::is('display/reward-page/*') || Request::is('rewards/upgrade') ? 'active': '' }}">
                        <a href="{{ route('display.reward-page') }}">Reward Page</a>
                    </li>
                    <li class="{{ Request::is('display/email-notifications') || Request::is('display/email-notifications/*') ? 'active': '' }}">
                        <a href="{{ route('display.email') }}">Email Notifications</a>
                    </li>
                </div>
            </span>

            <span> <!-- Customers -->
                <li class="">
                    <a class="{{ Request::is('customers/*') || Request::is('customers') ? 'active': '' }}"
                       href="{{ url('customers') }}">
                        <span class="icon-customers"></span> Customers
                    </a>
                </li>
            </span>

            <span> <!-- Reports -->
                <li>
                    <a class="{{ Request::is('reports/*') ? 'active': '' }}"
                       @if($merchant->checkPermitionByTypeCode('InsightsReports'))
                           data-toggle="collapse" data-target="#reports"
                       @else
                           href="{{route('reports.upgrade')}}"
                       @endif>
                        <span class="icon-reports"></span> Reports
                    </a>
                </li>
                <div id="reports" 
                     class="dropdown collapse {{ Request::is('reports/*') && !Request::is('reports/upgrade') ? 'show': '' }}" 
                     data-parent="#sidebarMenu">
                    <li class="{{ Request::is('reports/overview') ? 'active': '' }}">
                        <a href="{{ route('reports.overview') }}">Program Overview</a>
                    </li>
                    <li class="{{ Request::is('reports/referrals') ? 'active': '' }}">
                        <a href="{{ route('reports.referrals') }}">Referrals</a>
                    </li>
                </div>
            </span>
            
            <span> <!-- Integrations -->
                <li>
                    <a class="{{ Request::is('integrations/*') ? 'active': '' }}" 
                       data-toggle="collapse" 
                       data-target="#integrations">
                        <span class="icon-integrations"></span> Integrations
                    </a>
                </li>
                <div id="integrations" 
                     class="dropdown collapse {{ Request::is('integrations/*') ? 'show': '' }}" 
                     data-parent="#sidebarMenu">
                    <li class="{{ Request::is('integrations/overview') || Request::is('integrations/upgrade') ? 'active': '' }}">
                        <a href="{{ route('integrations.overview') }}">Overview</a>
                    </li>
                    <li class="{{ Request::is('integrations/manage') || Request::is('integrations/manage/*') ? 'active': '' }}">
                        <a href="{{ route('integrations.manage') }}">Manage</a>
                    </li>
                </div>
            </span>

            <span class="d-md-none"> <!-- Account -->
                <li>
                    <a data-toggle="collapse" 
                       data-target="#settings">
                       <span class="icon-gear" style="font-size: 32px;"></span> Account
                    </a>
                </li>
                <div id="settings"
                     class="dropdown collapse" 
                     data-parent="#sidebarMenu">
                    <li>
                        <a href="{{ route('settings') }}">Account Settings</a>
                    </li>
                    <li>
                        <a href="{{ route('logout') }}">Logout</a>
                    </li>
                </div>
            </span>
        </div>
    </ul>

    @auth
        <div class="text-center my-3 font-weight-bold">
            @if ($merchant->plan()->name)
                <span>Plan: </span> {{ $merchant->plan()->name }}
            @else
                <span>Plan: </span> Free
            @endif
        </div>
    @endauth

    <div class="p-r-5">
        <a href="{{ route('account.upgrade') }}" class="btn btn-success btn-glow btn-block upgrade-btn m-t-15 m-b-15">Upgrade</a>
    </div>
</nav>
