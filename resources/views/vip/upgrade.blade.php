@extends('layouts.app')

@section('title', 'VIP')

@section('content')
<div class="upsells-page m-t-20 m-b-30">
    <div class="row m-t-20 p-b-10 section-border-bottom">
        <div class="col-12">
            <h3 class="page-title m-t-0 color-dark">VIP</h3>
        </div>
    </div>
    
    <div class="well m-t-20">
    	<div class="row">
    		<div class="col-md-7">
    			<h4 class="bolder f-s-18 m-t-10">Increase purchase frequency, average order size and customer value</h4>
    			<p class="m-t-15">Expand your loyalty program with a VIP system for your best customers, and reward them with exclusive discounts.</p>
    			<div class="m-t-30">
    				<div class="upsells-option">
                        <table>
                            <tr>
                                <td><i style="top: 1px;" class="icon-vip"></i></td>
                                <td>
                                    <p class="bold f-s-16">Tier Customization</p> 
                                    <p>Create unique VIP Tiers with custom titles, colors, images, icons and more.</p>
                                </td>
                            </tr>
                        </table>
    				</div>
    				<div class="upsells-option m-t-25">
                        <table>
                            <tr>
                                <td><i class="icon-vip"></i></td>
                                <td>
                                    <p class="bold f-s-16">Milestone Rewards</p> 
                                    <p>Offer unique rewards when customers first achieve VIP Status and for their continued loyalty with lifetime rewards.</p>
                                </td>
                            </tr>
                        </table>
    				</div>
    				<div class="upsells-option m-t-25">
                        <table>
                            <tr>
                                <td><i class="icon-vip"></i></td>
                                <td>
                                    <p class="bold f-s-16">Earning Requirements</p> 
                                    <p>Specify earning periods and tier requirements to encourage users to shop & interact more with your brand to maintain their VIP Tier.</p>
                                </td>
                            </tr>
                        </table>
    				</div>
    			</div>

    			<div class="text-center m-t-40 m-b-20">
    				<a class="btn upgrade-plan-btn" href="{{ route('account.upgrade') }}">Upgrade to 
                        {!! App\Models\PaidPermission::getByTypeCode(\Config::get('permissions.typecode.VIP_Program'))->getMinPlan()->name !!}
                    </a>
    			</div>
    		</div>
    		<div class="col-md-5">
    			<div class="upsells-image"></div>
    		</div>
    	</div>
    </div>
</div>
@endsection
