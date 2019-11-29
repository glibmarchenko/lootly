@extends('layouts.app')

@section('title', 'Rewards Page')

@section('content')
<div class="upsells-page m-t-20 m-b-30">
    <div class="row m-t-20 p-b-10 section-border-bottom">
        <div class="col-12">
            <h3 class="page-title m-t-0 color-dark">Rewards Page</h3>
        </div>
    </div>
    
    <div class="well m-t-20">
    	<div class="row">
    		<div class="col-md-7">
    			<!-- <h4 class="bolder f-s-18 m-t-10"></h4> -->
    			<p class="m-t-15"> Showcase all aspects of your loyalty program on a dedicated page. Fully customize all images, colors, font sizes and FAQ to bring the page to life.</p>
    			<div class="m-t-30">
    				<div class="upsells-option">
                        <table>
                            <tr>
                                <td><i class="icon-heart f-s-30"></i></td>
                                <td>
                                    <p class="bold f-s-16">Display your popular Earning & Spending Rewards</p> 
                                    <p>Select the exact earning actions and spending rewards to display on your rewards page. You can even change the ordering to emphasize a specific item.</p>
                                </td>
                            </tr>
                        </table>
    				</div>
    				<div class="upsells-option m-t-25">
                        <table>
                            <tr>
                                <td><i class="icon-heart f-s-30"></i></td>
                                <td>
                                    <p class="bold f-s-16">Complete Design Customization</p> 
                                    <p>Match all colors, text, and images to your brand, in addition to having access to an HTML editor to customize the layout of content on the page.</p>
                                </td>
                            </tr>
                        </table>
    				</div>
    				<div class="upsells-option m-t-25">
                        <table>
                            <tr>
                                <td><i class="icon-heart f-s-30"></i></td>
                                <td>
                                    <p class="bold f-s-16">Frequently Asked Questions</p> 
                                    <p>Add your own questions & answers so customers are aware exactly how your loyalty program works.</p>
                                </td>
                            </tr>
                        </table>
    				</div>
    			</div>

    			<div class="text-center m-t-40 m-b-20">
    				<a class="btn upgrade-plan-btn" href="{{ route('account.upgrade') }}">Upgrade to 
                        {!! App\Models\PaidPermission::getByTypeCode(\Config::get('permissions.typecode.RewardsPage'))->getMinPlan()->name !!}                        
                    </a>
    			</div>
    		</div>
    		<div class="col-md-5">
    			<div class="upsells-image">
                    <img src="{{ url('images/assets/reward-iphone.png') }}" style="">         
                </div>
    		</div>
    	</div>
    </div>
</div>
@endsection
