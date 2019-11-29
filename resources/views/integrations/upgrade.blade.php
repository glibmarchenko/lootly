@extends('layouts.app')

@section('title', 'Integrations')

@section('content')
<div class="upsells-page m-t-20 m-b-30">
    <div class="row m-t-20 p-b-10 section-border-bottom">
        <div class="col-12">
            <h3 class="page-title m-t-0 color-dark">Integrations</h3>
        </div>
    </div>
    
    <div class="well m-t-20">
    	<div class="row">
    		<div class="col-md-7">
    			<h4 class="bolder f-s-18 m-t-10">Expand your Loyalty Program</h4>
    			<p class="m-t-15">Connect your Lootly account to other popular marketing apps to offer new ways for customers to earn points or to communicate with them.</p>
    			<div class="m-t-30">
    				<div class="upsells-option">
                        <table>
                            <tr>
                                <td><i class="icon-integrations f-s-30"></i></td>
                                <td>
                                    <p class="bold f-s-16">Points for Reviews</p> 
                                    <p>Connect Lootly to TrustSpot to offer the ability for customers to earn points when they write a review for your platform.</p>
                                </td>
                            </tr>
                        </table>
    				</div>
    				<div class="upsells-option m-t-25">
                        <table>
                            <tr>
                                <td><i class="icon-integrations f-s-30"></i></td>
                                <td>
                                    <p class="bold f-s-16">Email Marketing</p> 
                                    <p>Connect your Klaviyo account to send special emails to your loyalty customers or to specific VIP Tier members.</p>
                                </td>
                            </tr>
                        </table>
    				</div>
    				<div class="upsells-option m-t-25">
                        <table>
                            <tr>
                                <td><i class="icon-integrations f-s-30"></i></td>
                                <td>
                                    <p class="bold f-s-16">Social Proof (coming soon)</p> 
                                    <p>Connect PushOwl to your loyalty program to reward customers for enabling notifications to help drive new revenue to your store.</p>
                                </td>
                            </tr>
                        </table>
    				</div>
    			</div>

    			<div class="text-center m-t-40 m-b-20">
    				<a class="btn upgrade-plan-btn" href="{{ route('account.upgrade') }}">Upgrade to 
                        {!! App\Models\PaidPermission::getByTypeCode(\Config::get("permissions.typecode.Integrations"))->getMinPlan()->name !!}
                    </a>
    			</div>
    		</div>
    		<div class="col-md-5">
    			<div class="upsells-image">
                    <img src="{{ url('images/assets/integrations.png') }}" style="width: 95%; margin-top: 70px;">         
                </div>
    		</div>
    	</div>
    </div>
</div>
@endsection
