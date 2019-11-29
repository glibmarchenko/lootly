@extends('layouts.app')

@section('title', 'Referrals')

@section('content')
<div class="upsells-page m-t-20 m-b-30">
    <div class="row m-t-20 p-b-10 section-border-bottom">
        <div class="col-12">
            <h3 class="page-title m-t-0 color-dark">Referrals</h3>
        </div>
    </div>
    
    <div class="well m-t-20">
    	<div class="row">
    		<div class="col-md-7">
    			<h4 class="bolder f-s-18 m-t-10">Increase new revenue and new relationships with customers</h4>
    			<p class="m-t-15">Leverage your existing customers by allowing them to become brand ambassadors for your business.</p>
    			<div class="m-t-30">
    				<div class="upsells-option">
                        <table>
                            <tr>
                                <td><i class="icon-heart f-s-30"></i></td>
                                <td>
                                    <p class="bold f-s-16">Multiple Ways to Share</p> 
                                    <p>Make it easy for your customers to share their referral discount across social media and email from the Lootly widget on your site.</p>
                                </td>
                            </tr>
                        </table>
    				</div>
    				<div class="upsells-option m-t-25">
                        <table>
                            <tr>
                                <td><i class="icon-heart f-s-30"></i></td>
                                <td>
                                    <p class="bold f-s-16">Rewards for the Sender & Receiver</p> 
                                    <p>Create unique rewards for customers who share their referral link, and for new buyers who use the link to make a purchase with you.</p>
                                </td>
                            </tr>
                        </table>
    				</div>
    				<div class="upsells-option m-t-25">
                        <table>
                            <tr>
                                <td><i class="icon-heart f-s-30"></i></td>
                                <td>
                                    <p class="bold f-s-16">Referral Insights</p> 
                                    <p>Discover the best sharing options that are driving referral orders, and also gain better visiblity into who your top referrers are.</p>
                                </td>
                            </tr>
                        </table>
    				</div>
    			</div>

    			<div class="text-center m-t-40 m-b-20">
    				<a class="btn upgrade-plan-btn" href="{{ route('account.upgrade') }}">Upgrade to 
                        {!! App\Models\PaidPermission::getByTypeCode(\Config::get('permissions.typecode.ReferralProgram'))->getMinPlan()->name !!}
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
