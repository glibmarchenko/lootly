@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="upsells-page m-t-20 m-b-30">
    <div class="row m-t-20 p-b-10 section-border-bottom">
        <div class="col-12">
            <h3 class="page-title m-t-0 color-dark">Reports & Customer Insights</h3>
        </div>
    </div>
    
    <div class="well m-t-20">
    	<div class="row">
    		<div class="col-md-7">
    			<h4 class="bolder f-s-18 m-t-10">Insights at your Fingertips</h4>
    			<p class="m-t-15">Get a complete understanding of how well your Loyalty & Referral program is working, and discover new ways to enhance your customer's experience.</p>
    			<div class="m-t-30">
    				<div class="upsells-option">
                        <table>
                            <tr>
                                <td><i class="icon-reports" style="font-size: 37px;"></i> </td>
                                <td>
                                    <p class="bold f-s-16">Discover what your Customers Love</p> 
                                    <p>Optimize your loyalty program overtime by introducing new earning actions & spending rewards based on current usage.</p>
                                </td>
                            </tr>
                        </table>
    				</div>
    				<div class="upsells-option m-t-25">
                        <table>
                            <tr>
                                <td><i class="icon-reports" style="font-size: 37px;"></i> </td>
                                <td>
                                    <p class="bold f-s-16">Top Referrers</p> 
                                    <p>Utilizing a Referral Program is a great way to drive new customers to your store, now see who is delivering the most shares & orders.</p>
                                </td>
                            </tr>
                        </table>
    				</div>
    				<div class="upsells-option m-t-25">
                        <table>
                            <tr>
                                <td><i class="icon-reports" style="font-size: 37px;"></i> </td>
                                <td>
                                    <p class="bold f-s-16">Loyalty Investment</p> 
                                    <p>See how well each aspect of your loyalty platform is performing, analyze costs and compare value generated over time.</p>
                                </td>
                            </tr>
                        </table>
    				</div>
    			</div>

    			<div class="text-center m-t-40 m-b-20">
    				<a class="btn upgrade-plan-btn" href="{{ route('account.upgrade') }}">Upgrade to 
                        {!! App\Models\PaidPermission::getByTypeCode(\Config::get("permissions.typecode.InsightsReports"))->getMinPlan()->name !!}
                    </a>
    			</div>
    		</div>
    		<div class="col-md-5">
    			<div class="upsells-image">
                    <img src="{{ url('images/assets/reports-overview.png') }}" style="">         
                </div>
    		</div>
    	</div>
    </div>
</div>
@endsection
