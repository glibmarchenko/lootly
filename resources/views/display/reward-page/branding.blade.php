@extends('layouts.app')

@section('title', 'Reward Page Branding')

@section('content')
	<div id="branding-page" class="loader m-t-20 m-b-10" v-cloak>
		<b-alert v-cloak
			:show="alert.dismissCountDown"
			dismissible
			:variant="alert.type"
			@dismissed="alert.dismissCountdown=0"
			@dismiss-count-down="countDownChanged">
			@{{alert.text}}
		</b-alert>

		<form>
			<div class="row p-b-10 section-border-bottom">
				<div class="col-md-12 m-b-15">
					<a href="{{ route('display.reward-page') }}" class="bold f-s-15 color-blue">
						<i class="arrow left blue"></i>
						<span class="m-l-5">Reward Page Overview</span>
					</a>
				</div>
				<div class="col-md-12">
					<h3 class="page-title m-t-0 color-dark pull-left">
						Rewards Page Branding
					</h3>
                    <save-button class="pull-right" :saving="saving" @event="saveAction"></save-button>
				</div>
			</div>
			<div class="row p-t-25 p-b-25">
				<div class="col-md-7 col-12">
					<div class="well">
						<!-- <div class="row"> -->
							<div :class="{ 'loading' : loading }" v-cloak>
								<div class="border-bottom p-b-10 m-b-15">
									<label class="bolder f-s-15 m-b-0 m-t-5">
										General Settings
									</label>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-input">
											<label class="light-font m-b-5">
												<span>Font</span>
											</label>
											<b-form-select v-model="form.font" :options="fonts" name="font"></b-form-select>
											<p class="d-block m-t-10">Need a font that is not available? 
												Our <a class="bold light-link" href="/contact">support team</a> will gladly add it for you.
											</p>
										</div>
									</div>
								</div>

								@if($has_remove_branding_permissions)
									<div class="row m-t-15 m-b-5">
										<div class="col-md-12">
											<div class="form-input">
												<label class="d-block bold m-b-10">
													<span>Page Branding</span>
												</label>
												<b-form-checkbox v-model="form.widgetBranding" name="widgetBranding">
													Remove Lootly branding in footer
												</b-form-checkbox>
											</div>
										</div>
									</div>
								@else
									<div class="row m-t-15 m-b-5">
										<div class="col-md-12">
												<div class="form-input">
													<label class="d-block bold m-b-10">
														<span>Page Branding</span>
													</label>
													<b-form-checkbox v-model="form.widgetBranding" name="widgetBranding">
														Remove Lootly branding in footer
													</b-form-checkbox>
												</div>
										</div>
									</div>
								@endif
							</div>
						</div>
						@if(!$has_remove_branding_permissions)
							<div class="m-t-15">
										<no-access :loading="loading"
											title="{{$branding_upsell->upsell_title}}"
											desc="{{$branding_upsell->upsell_text}}"
											icon="{{$branding_upsell->upsell_image}}"
											plan="{{$branding_upsell->getMinPlan()->name}}"></no-access>
							</div>
						@endif
					<!-- </div> -->
				</div>
				<div class="col-md-5 col-12">
					<div class="sticky-top">
						<div class="well p-t-0 p-b-0 p-r-0 p-l-0">
							<div class="rewards-page-component" :class="{ 'loading' : loading || saving }" :style="{'font-family': form.font}">
								<div style="padding: 20px;margin-bottom: 20px; text-align: center; color: #fff" :style="{'background': form.headerColor}">
									<h1>Rewards Program</h1>
								</div>
								<div class="p-b-20 p-l-20 p-r-20" style="line-height: 25px;">
									<p>Rewards Program</p>
									<p>How it Works?</p>
									<p>Want to earn more Points?</p>
									<p>Become a VIP and earn even more Points</p>
									<p>Redeem your Points</p>
								</div>
								<footer class="text-center border-top p-t-0" v-if="!form.widgetBranding">
									<div class="lootly-copywrite p-t-10">
										<span>Powered By</span>
										<a href="">
											<img src="{{ config('app.logo-inner') }}"
											style="width: 100px;margin: auto;">
										</a>
									</div>
								</footer>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </form>
	</div>
@endsection

@section('scripts')
<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet">
<script>
	var page = new Vue({
		el: '#branding-page',
		data: {
			form: {
				headerColor: "{{$header->header_color}}" ? "{{$header->header_color}}" : '#0170af',
				font: "{{$rewards_branding->font}}" ? "{{$rewards_branding->font}}" : 'lato',
				widgetBranding: '{{$rewards_branding->remove_branding}}' == '1' ? '{{$rewards_branding->remove_branding}}' : false
			},
			fonts: [
				{value: 'lato', text: 'Lato'},
				{value: 'courier', text: 'Courier'},
				{value: 'roboto', text: 'Roboto'},
				{value: 'arial', text: 'Arial'}
			],
			alert: {
				type: '',
				text: '',
				dismissSecs: 5,
				dismissCountDown: 0
			},
			saving: false,
			loading: false
		},
		created: function () {
			this.getSettings();
			if(this.form.widgetBranding === '1'){
				this.form.widgetBranding = true;
			} else {
				this.form.widgetBranding = false;
			}
			console.log(this.form);
		},
		methods: {
			getSettings: function () {

			},
			saveAction: function () {
				console.log(this.form);
				this.saving = true;
				axios.post("{{route('display.reward-page.branding.store')}}", this.form).then((response) => {
					if (response.status == 404) {
						this.alert.type = 'danger'
						this.alert.text = response.data.message;
	                } else {
						this.alert.dismissCountDown = this.alert.dismissSecs;
						this.alert.type = 'success';
						this.alert.text = response.data.message;
					}
					this.saving = false;
				}).catch((error) => {
					this.saving = false;
	               clearErrors(this.el);
	               console.log(error);
	               showErrors(this.el, error.data);
	               this.alert.type = 'danger';
	               this.alert.dismissCountDown = this.alert.dismissSecs;
	               this.alert.text = error.data;
	            });
			},
			countDownChanged (dismissCountDown) {
				this.alert.dismissCountDown = dismissCountDown
			}
		}
	})

</script>
@endsection

