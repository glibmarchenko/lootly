@extends('layouts.app')

@section('title', 'Rewards Page')

@section('content')
	<div id="rewards-settings" class="loader m-t-20 m-b-10" v-cloak>
	    <b-alert v-cloak
	             :show="alert.dismissCountDown"
	             dismissible
	             :variant="alert.type"
	             @dismissed="alert.dismissCountdown=0"
	             @dismiss-count-down="countDownChanged">
	        @{{alert.text}}
	    </b-alert>

	    <div class="row">
	        <div class="col-md-12 m-b-15">
	            <a href="{{ route('display.reward-page') }}" class="bold f-s-15 color-blue">
	                <i class="arrow left blue"></i>
	                <span class="m-l-5">Reward Page Overview</span>
	            </a>
	        </div>
	    </div>

        <div class="row section-border-bottom p-b-10">
        	<div class="col-md-6 col-12">
	            <h3 class="page-title m-t-0 color-dark pull-left">
	                Rewards Page
	            </h3>
        	</div>
            <div class="col-md-6 col-12 text-right">
					@if(!$has_editor_permissions)
						<button v-if="editingMode == 'default'" 
								class="btn btn-default m-r-10" 
								:disabled="saving"
								style="background: #f3f3f3;" 
								@click="htmlNoAccess">
							<i class="fa fa-lock m-r-5" aria-hidden="true"></i> Switch to HTML Editor 
						</button>
					@else
						<button v-if="editingMode == 'default'" class="btn btn-default m-r-10" @click="switchToHtmlMode" :disabled="saving">
							Switch to HTML Editor 
						</button>

						<button v-if="editingMode == 'html'" class="btn btn-default m-r-10" @click="switchToDefaultMode" :disabled="saving">
							Switch to Settings 
						</button>
					@endif

                <save-button class="inline-block" :saving="saving" @event="saveModal"></save-button>

            </div>
        </div>

		<span class="rewards-page-settings" v-show="editingMode == 'default'">
			<!-- Header Section -->
			@include('display.reward-page._settings.header')

			<!-- How it works Section -->
			@include('display.reward-page._settings.how-it-works')

			<!-- Earning Section -->
			@include('display.reward-page._settings.earning')

			<!-- Vip Section -->
			@include('display.reward-page._settings.vip')

			<!-- Spending Section -->
			@include('display.reward-page._settings.spending')

			<!-- Referral Section -->
			@include('display.reward-page._settings.referral', ['receiver' => $receiver, 'sender' => $sender])

			<!-- FAQ Section -->
			@include('display.reward-page._settings.faq')

			<div class="row section-border-bottom p-t-25 p-b-25">
				<div class="col-md-6 col-12">
					<div class="well">
						<div :class="{ 'loading' : loading }" v-cloak>
							<div class="border-bottom p-b-10">
								<label class="bolder f-s-15 m-b-0">
									Reward Page code
								</label>
								<p class="m-t-5">Copy & Paste the below code into a dedicated page on your website.</p>
							</div>
							<textarea id="referralIframe" class="form-control m-t-10" style="min-height: 110px;"><iframe src="{{ url('/rewards-page') }}/{{ $merchant_details->api_key }}" frameborder="0" style="width: 100%; min-height: 600px;"></iframe></textarea>
							<div class="text-right">
								<button type="button" 
										class="btn btn-copy m-t-15"
										onclick="copyText('#referralIframe')" 
										style="min-width: 90px;">Copy</button>
							</div>
						</div>						
					</div>
				</div>
			</div>
		</span>

		<span v-show="editingMode == 'html'">
			<!-- HTML MODE -->
			@include('display.reward-page._settings.html')
		</span>

	   <b-alert v-cloak
	            :show="alert.dismissCountDown"
	            dismissible
	            class="m-t-15 m-b-0"
	            :variant="alert.type"
	            @dismissed="alert.dismissCountdown=0"
					@dismiss-count-down="countDownChanged">
	   	@{{alert.text}}
	   </b-alert>

	   <div class="row m-t-20 p-b-10" v-if="editingMode == 'default'">
	   <div class="col-md-12">
               <save-button class="text-right" :saving="saving" @event="saveModal"></save-button>
	   	</div>
	   </div>
	</div>
@endsection

@section('scripts')
<!-- CDNJS :: Vue.Draggable (https://cdnjs.com/) -->
<script src="//cdn.jsdelivr.net/npm/sortablejs@1.7.0/Sortable.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/Vue.Draggable/2.15.0/vuedraggable.min.js"></script>

<!-- Trumbowyg Editor -->
<link rel="stylesheet" type="text/css" href="https://unpkg.com/trumbowyg@2.10.0/dist/ui/trumbowyg.min.css">
<link rel="stylesheet" type="text/css" href="https://unpkg.com/trumbowyg@2.10.0/dist/plugins/colors/ui/trumbowyg.colors.min.css">
<script src="https://unpkg.com/trumbowyg@2.10.0/dist/trumbowyg.min.js"></script>
<script src="https://unpkg.com/vue-trumbowyg@3.3.0/dist/vue-trumbowyg.min.js"></script>
<script src="https://unpkg.com/trumbowyg@2.10.0/dist/plugins/fontsize/trumbowyg.fontsize.min.js"></script>
<script src="https://unpkg.com/trumbowyg@2.10.0/dist/plugins/colors/trumbowyg.colors.min.js"></script>
<script src="https://unpkg.com/trumbowyg@2.10.0/dist/plugins/lineheight/trumbowyg.lineheight.min.js"></script>
<script src="{{ url('js/plugins/html-formater.js') }}" type="text/javascript"></script>

<script>
	Vue.component('Trumbowyg', VueTrumbowyg.default)
	var page = new Vue({
		el: '#rewards-settings',
		data: {
			@if($reward_settings->html_mode)
				editingMode: 'html',
			@else
				editingMode: 'default',				
			@endif
			point_name: @if($points) '{!!$points->name!!}',
            @else'Point',
            @endif

         point_namePlural: @if($points) '{!!$points->plural_name!!}',
            @else'Points',
            @endif
			form: {
				header: {
					title: "{!! $header['title'] ? addslashes($header['title']) : 'Rewards Program' !!}",
					subtitle: "{!! $header['subtitle'] ? addslashes($header['subtitle']) : 'Check out the amazing ways to earn rewards with us.' !!}",
					button1: "{!! $header['button1_text'] ? addslashes($header['button1_text']) : 'Sign Up' !!}",
					button2: "{!! $header['button2_text'] ? addslashes($header['button2_text']) : 'Login' !!}",
					button1Link: "{!! $header['button1_link'] ? addslashes($header['button1_link']) : $merchant_domain.'/account/sign-up' !!}",
					button2Link: "{!! $header['button2_link'] ? addslashes($header['button2_link']) : $merchant_domain.'/account/login' !!}",
					background: "{!!$header['background_url']!!}",
		         new_background: '',
		         background_opacity: "{!!$header['background_opacity']!!}" ? "{!!$header['background_opacity']!!}" : '100%',
		      	background_name: "{!! addslashes($header['background_name']) !!}",
		            design: {
		            	color: "{!!$header['header_color']!!}" ? "{!!$header['header_color']!!}" : '#0170af',
		            	titleColor: "{!!$header['title_color']!!}" ? "{!!$header['title_color']!!}" : '#fff',
		            	subtitleColor: "{!!$header['subtitle_color']!!}" ? "{!!$header['subtitle_color']!!}" : '#fff',
		            	buttonColor: "{!!$header['button_color']!!}" ? "{!!$header['button_color']!!}" : '#fff',
		            	buttonTextColor: "{!!$header['button_text_color']!!}" ? "{!!$header['button_text_color']!!}" : '#000',
		            	titleFontSize: "{!!$header['title_font_size']!!}" ? "{!!$header['title_font_size']!!}" : '35',
		            	subtitleFontSize: "{!!$header['subtitle_font_size']!!}" ? "{!!$header['subtitle_font_size']!!}" : '16',
		            	buttonFontSize: "{!!$header['button_font_size']!!}" ? "{!!$header['button_font_size']!!}" : '15'
		            }
				},
				howItWorks: {
					title: "{!! $how_it_works['title'] ? addslashes($how_it_works['title']) : 'How does it work?' !!}",
					step1: "{!! $how_it_works['steep1_text'] ? addslashes($how_it_works['steep1_text']) : 'Join' !!}",
					step2: "{!! $how_it_works['steep2_text'] ? addslashes($how_it_works['steep2_text']) : 'Earn Points' !!}",
					step3: "{!! $how_it_works['steep3_text'] ? addslashes($how_it_works['steep3_text']) : 'Redeem' !!}",
		            design: {
		            	titleColor: "{!!$how_it_works['title_color']!!}" ? "{!!$how_it_works['title_color']!!}" : '#000',
		            	stepsColor: "{!!$how_it_works['steps_color']!!}" ? "{!!$how_it_works['steps_color']!!}" : '#000',
		            	titleFontSize: "{!!$how_it_works['title_font_size']!!}" ? "{!!$how_it_works['title_font_size']!!}" : '24',
		            	stepsFontSize: "{!!$how_it_works['steps_front_size']!!}" ? "{!!$how_it_works['steps_front_size']!!}" : '16',
		            	circleFullColor: "{!!$how_it_works['circle_full_color']!!}" ? "{!!$how_it_works['circle_full_color']!!}" : '#1cc04a',
		            	circleEmptyColor: "{!!$how_it_works['circle_empty_color']!!}" ? "{!!$how_it_works['circle_empty_color']!!}" : '#ebeef5',
		            	arrowsColor: "{!!$how_it_works['arrows_color']!!}" ? "{!!$how_it_works['arrows_color']!!}" : '#1cc04a'
		            }
				},
				earning: {
					title: "{!! $earning['title'] ? addslashes($earning['title']) : 'Want to earn more {points-name}?' !!}",
					actions: [],
					selectedActions: [
						@if($earning)
							@foreach($earning->merchant_actions as $merchant_action)
								{id: "{!!$merchant_action->id!!}"},
							@endforeach
						@endif
					],
					options: [
						@foreach($merchant_actions as $merchant_action)
							{id: "{!!$merchant_action->id!!}",
							icon: "{!!$merchant_action->action_icon ? $merchant_action->action_icon : $merchant_action->action_icon_name!!}",
							title: "{!! addslashes($merchant_action->action_name) !!}",
							points: "{!!$merchant_action->reward_text!!}"},
						@endforeach
					],
					design: {
						titleColor: "{!!$earning['title_color']!!}" ? "{!!$earning['title_color']!!}" : '#000',
						actionTextColor: "{!!$earning['action_text_color']!!}" ? "{!!$earning['action_text_color']!!}" : '#fff',
						pointColor: "{!!$earning['point_color']!!}" ? "{!!$earning['point_color']!!}" : '#0170af',
						ribbonColor: "{!!$earning['ribbon_color']!!}" ? "{!!$earning['ribbon_color']!!}" : '#fff',
						boxColor: "{!!$earning['box_color']!!}" ? "{!!$earning['box_color']!!}" : '#0170af',
						titleFontSize: "{!!$earning['title_font_size']!!}" ? "{!!$earning['title_font_size']!!}" : '24',
						actionFontSize: "{!!$earning['action_font_size']!!}" ? "{!!$earning['action_font_size']!!}" : '14',
						pointFontSize: "{!!$earning['point_font_size']!!}" ? "{!!$earning['point_font_size']!!}" : '16'
					}
				},
				vip: {
					title: "{!! $vip['title'] ? addslashes($vip['title']) : 'Become a VIP and earn even more {points-name}' !!}",
					design: {
						titleColor: "{!!$vip['title_color']!!}" ? "{!!$vip['title_color']!!}" : '#000',
						tierNameColor: "{!!$vip['tier_name_color']!!}" ? "{!!$vip['tier_name_color']!!}" : '#000',
						multiplierColor: "{!!$vip['multiplier_color']!!}" ? "{!!$vip['multiplier_color']!!}" : '#585858',
						requirementsColor: "{!!$vip['requirements_color']!!}" ? "{!!$vip['requirements_color']!!}" : '#585858',
						titleFontSize: "{!!$vip['title_font_size']!!}" ? "{!!$vip['title_font_size']!!}" : '24',
						tierNameFontSize: "{!!$vip['tier_name_font_size']!!}" ? "{!!$vip['tier_name_font_size']!!}" : '18',
						multiplierFontSize: "{!!$vip['multiplier_font_size']!!}" ? "{!!$vip['multiplier_font_size']!!}" : '16',
						requirementsFontSize: "{!!$vip['requirements_font_size']!!}" ? "{!!$vip['requirements_font_size']!!}" : '15'
					},
					tiers: [
						@foreach($vips as $vip)
							{
								id: {!!$vip->id!!},
								name: "{!!$vip->name!!}",
								icon_url: "{!!$vip->image_url!!}",
								icon_name: "{!!$vip->image_name!!}",
								icon_color: "{!!$vip->default_icon_color!!}",
								multiplier: {!!$vip->multiplier!!},
								text: "{!!$vip->requirement_text!!}",
							},
						@endforeach
					]
				},
				spending: {
					title: "{!! $spending['title'] ? addslashes($spending['title']) : 'Redeem your {points-name}' !!}",
					actions: [],
					selectedActions: [
						@if($spending)
							@foreach($spending->merchant_rewards as $merchant_reward)
								{id: "{!!$merchant_reward->id!!}"},
							@endforeach
						@endif
					],
					options: [
						@foreach($merchant_rewards as $merchant_reward)
							@if($merchant_reward->type_id == 1)
								{id: "{!!$merchant_reward->id!!}",
								icon: "{!!$merchant_reward->reward_icon_name!!}",
								title: "{!!$merchant_reward->reward_text!!}",
								points: "{!!$merchant_reward->reward_name!!}"},
							@endif
						@endforeach
					],
		            design: {
		            	titleColor: "{!!$spending['title_color']!!}" ? "{!!$spending['title_color']!!}" : '#000',
		            	boxTextColor: "{!!$spending['box_text_color']!!}" ? "{!!$spending['box_text_color']!!}" : '#fff',
		            	boxColor: "{!!$spending['box_color']!!}" ? "{!!$spending['box_color']!!}" : '#0170af',
		            	titleFontSize: "{!!$spending['title_font_size']!!}" ? "{!!$spending['title_font_size']!!}" : '24',
		            	boxFontSize: "{!!$spending['box_font_size']!!}" ? "{!!$spending['box_font_size']!!}" : '18'
		            }
				},
				referral: {
					title: "{!! $referral['title'] ? addslashes($referral['title']) : 'Refer a Friend' !!}",
					subtitle: "{!! $referral['subtitle'] ? addslashes($referral['subtitle']) : 'Share our store to earn great discounts for yourself and a friend!' !!}",

					design: {
						titleColor: "{!!$referral['title_color']!!}" ? "{!!$referral['title_color']!!}" : '#000',
						subtitleColor: "{!!$referral['subtitle_color']!!}" ? "{!!$referral['subtitle_color']!!}" : '#667885',
						titleFontSize: "{!!$referral['title_font_size']!!}" ? "{!!$referral['title_font_size']!!}" : '24',
						subtitleFontSize: "{!!$referral['subtitle_font_size']!!}" ? "{!!$referral['subtitle_font_size']!!}" : '16'
					},
					receiver: {
						exists: '{!!!empty($receiver)!!}',
						name: "{!! addslashes($receiver['reward_name']) !!}",
						text: "{!! addslashes($receiver['reward_text']) !!}",
						reward_icon: "{!! $receiver['reward_icon'] !!}",
						reward_icon_name: "{!! $receiver['reward_icon_name'] !!}",
					},
					sender: {
						exists: '{!! !empty($sender) !!}',
						name: "{!! addslashes($sender['reward_name']) !!}",
						text: "{!! addslashes($sender['reward_text']) !!}",
						reward_icon: "{!! $sender['reward_icon'] !!}",
						reward_icon_name: "{!! $sender['reward_icon_name'] !!}",
					},
				},
				faq: {
					status: "{!! $faq['status'] !!}" ? "{!!$faq['status']!!}" : 1,
					title: "{!! $faq['title'] ? addslashes($faq['title']) : 'Frequently Asked Questions' !!}",
					questions: [
						@if($faq)
							@foreach($faq->questions as $question)
								{
									id: "{!!$question->id!!}", 
									question: "{!! addslashes($question->question) !!}", 
									answer: @json($question->answer)
								},
							@endforeach
						@endif
					],
		            design: {
		            	titleColor: "{!!$faq['title_color']!!}" ? "{!!$faq['title_color']!!}" : '#000',
		            	questionColor: "{!!$faq['question_color']!!}" ? "{!!$faq['question_color']!!}" : '#000',
		            	answerColor: "{!!$faq['answer_color']!!}" ? "{!!$faq['answer_color']!!}" : '#545558',
		            	titleFontSize: "{!!$faq['title_font_size']!!}" ? "{!!$faq['title_font_size']!!}" : '24',
		            	questionFontSize: "{!!$faq['question_font_size']!!}" ? "{!!$faq['question_font_size']!!}" : '16',
		            	answerFontSize: "{!!$faq['answer_font_size']!!}" ? "{!!$faq['answer_font_size']!!}" : '15'
		            }
				},
				widgetsHTML: "",
			},
			htmlMode: {
				default: '',
				body: '',
				config: {
					svgPath: '/fonts/icons/trumbowyg-icons.svg',
					semantic: false,
					btns: [['fontsize', 'foreColor', 'bold', 'italic'], ['lineheight', 'horizontalRule', 'link'], ['justifyLeft', 'justifyCenter', 'justifyRight'], ['viewHTML']]
				},
			},
			alert: {
				type: '',
				text: '',
				dismissSecs: 5,
				dismissCountDown: 0
			},
			saving: false,
			loading: false,
			changesInHTML: false, // check if user save custom HTML to show warning
		},
		created: function () {
			if(this.form.htmlMode){
				this.changesInHTML = true
			} else {
				this.changesInHTML = false
			}
			this.computeActions('earning');
			this.computeActions('spending');
		},
		mounted: function() {
			this.getHtml('{{ addslashes($reward_settings->html) }}');
		},
		methods: {
			saveModal: function (){
				if(this.changesInHTML && this.editingMode != 'html'){
					swal({
						className: "warning-swal",
						title: 'HTML Editor Notice',
						text: "Saving any changes here will overwrite your custom html",
						icon: "/images/icons/fa-warning.png",
						buttons: true,
						dangerMode: true,
					})
					.then((save) => {
						if(save) {
							this.saveAction();
						}
					});
				} else {
					this.saveAction();
				}
			},
			saveAction: function () {
				this.saving = true;
				if(this.editingMode == 'html'){
					this.changesInHTML = true;
					this.form.htmlMode = true;
				} else {
					this.getHtml();
					this.htmlMode.body = this.htmlMode.default;
					this.changesInHTML = false;
					this.form.htmlMode = false;
				}
				this.form.widgetsHTML = this.htmlMode.body;

				axios.post("{!!route('display.reward-page.settings.store')!!}", this.form).then((response) => {
					if (response.status < 200 && response.status >= 400) {
						this.alert.dismissCountDown = this.alert.dismissSecs;
                  this.alert.type = 'danger'
                  this.alert.text = response.data.message;
               } else {
						this.alert.dismissCountDown = this.alert.dismissSecs;
						this.alert.type = 'success';
						this.alert.text = response.data.message;
						this.form.header.background = response.data.image_url;
						this.htmlMode.body = response.data.html;
					}
					this.saving = false;
				}).catch((error) => {
					this.saving = false;
               clearErrors(this.el);
               console.warn(error);
               showErrors(this.el, error.data);
               this.alert.type = 'danger';
               this.alert.dismissCountDown = this.alert.dismissSecs;
               this.alert.text = error.data;
            });
				
			},
			htmlNoAccess: function () {
				swal({
					className: "upgrade-swal",
					title: '{!!$editor_upsell->upsell_title!!}',
					text: "{!!$editor_upsell->upsell_text!!}",
					icon: "/images/permissions/{!!$editor_upsell->upsell_image!!}",
					buttons: {
						catch: {
							text: "Upgrade to {!!$editor_upsell->getMinPlan()->name!!}",
							value: "upgrade",
						}
					},
				})
				.then((value) => {
					if(value == 'upgrade') {
						window.location.href = '/account/upgrade';
					}
				});

			},
			switchToHtmlMode: function () {
				var comp = this;
				swal({
					className: "warning-swal",
					title: 'HTML Editor Notice',
					text: "Making changes to the HTML code will not be rendered on the regular settings screen due to custom content.",
					icon: "/images/icons/fa-warning.png",
					buttons: true,
					dangerMode: true,
				})
				.then((response) => {
					if (response) {
						comp.editingMode = 'html';
						comp.getHtml();
					}
				});
			},
			switchToDefaultMode: function () {
				var comp = this;
				swal({
					className: "warning-swal",
					title: 'Custom HTML Notice',
					text: "Your custom HTML changes will not render in the regular settings screen,  since this is a separate area.",
					icon: "/images/icons/fa-warning.png",
					buttons: true,
					dangerMode: true,
				})
				.then((willDelete) => {
					if (willDelete) {
						comp.editingMode = 'default';
					}
				});
			},
			getHtml: function (htmlForbody = null) {
				var vm = this;
				var html = '<!-- HEADER section -->\n'+
							this.$refs.headerSection.outerHTML + 
							'\n\n<!-- How It Works section -->\n'+
						   this.$refs.howItWorksSection.outerHTML + 
							'\n\n<!-- Earning section -->\n'+
						   this.$refs.earningSection.outerHTML + 
							'\n\n<!-- Vip section -->\n'+
						   this.$refs.vipSection.outerHTML +
							'\n\n<!-- Spending section -->\n'+
						   this.$refs.spendingSection.outerHTML +
							'\n\n<!-- Referral section -->\n'+
						   this.$refs.referralSection.outerHTML;

			   	if(this.$refs.faqSection) {
					html +=	'\n\n<!-- Faq section -->\n'+
					   this.$refs.faqSection.outerHTML;
			   	}

			   	// Convert RGB colors to HEX inside editor
			    var rgbMatch = html.match(/rgb\(.*?\)/g );
			    for(i = 0; i < rgbMatch.length; i++) {
			    	html = html.replace(rgbMatch[i], vm.rgbToHex(rgbMatch[i]))
			    }

			    // HTML Code Formatter for better UX 
				tempEl = $("<div class='rewards-page-wrapper'></div>").html(html);
				$.prettify_code( tempEl );
				this.htmlMode.default = tempEl.html();
				if (htmlForbody) {
					var tempBodyHtml = $("<div class='rewards-page-body-wrapper'></div>").html(htmlForbody.replace(new RegExp("\\\\", "g"), ""));
					tempBodyHtml.html(tempBodyHtml.text());
					$.prettify_code( tempBodyHtml )
					this.htmlMode.body = tempBodyHtml.html()
				}
				if(!this.htmlMode.body || this.htmlMode.body == '' || this.htmlMode.body == '\n') {
					this.htmlMode.body = this.htmlMode.default;
				}
				$('.trumbowyg-box').removeClass('trumbowyg-editor-visible').addClass('trumbowyg-editor-hidden');
				$('.trumbowyg-button-pane').addClass('trumbowyg-disable')
				$('.trumbowyg-viewHTML-button').addClass('trumbowyg-active')
				return this.htmlMode.body;

			},
			rgbToHex: function (rgb) {
				var hex = '#' + rgb.match(/\d+/g).map(y = z => ((+z < 16)?'0':'') + (+z).toString(16)).join('');
				return hex;
			},
			resetCustomizations: function () {
				swal({
					className: "warning-swal",
					title: 'Reset Customizations Notice',
					text: "This will reset all of the custom design changes you’ve made in the HTML Editor and return it back to it’s default state based on your Standard Editor Settings. Are you sure you want to do this? This cannot be reversed",
					icon: "/images/icons/fa-warning.png",
					buttons: ['Cancel', 'Yes'],
					dangerMode: true,
				})
				.then((reset) => {
					if(reset){
						this.htmlMode.body = this.htmlMode.default;
						$('body,html').animate({
							scrollTop: 0
						}, 200);
						this.alert.dismissCountDown = this.alert.dismissSecs;
						this.alert.type = 'success';
						this.alert.text = 'Success! The HTML Editor has been reset';
					}
				});
			},
			computeActions: function(type) {
				var computedActions = [];
				var comp = this;
				this.form[type].selectedActions.forEach(function(value, index){
					computedAction = comp.form[type].options.getObjectByKey('id', value.id);
					computedActions.push(computedAction);
				})
				return this.form[type].actions = computedActions;
			},
			addAction: function (type) {
				this.form[type].selectedActions.push({id: ''});
			},
			deleteAction: function (type, index) {
				this.form[type].selectedActions = this.form[type].selectedActions.removeByIndex(index);
				this.computeActions(type);
			},
			notSelectedAction: function (type, id, selected_id) {
				if(this.form[type].selectedActions.getObjectByKey('id', id) && id != selected_id)
					return true;

				return false;
			},
			addQuestion: function () {
				this.form.faq.questions.push({id: '', question: '', answer: ''});

				setTimeout( function() { 
					$('.faq-list .faq-item:last-child .col-md-12').addClass('opened');
				}, 0);

			},
			deleteQuestion: function (index) {
				this.form.faq.questions = this.form.faq.questions.removeByIndex(index);
			},
			toogleFaqStatus: function () {
               if (this.form.faq.status == 0) 
               return this.form.faq.status = 1;

               return this.form.faq.status = 0;
			},
			headerBackgroundChange: function (evt) {
				var $this = this;
				var files = evt.target.files;
				var f = files[0];

				if (files.length != 0) {
					var reader = new FileReader()

					$this.form['header'].background_name = f.name
					$this.form['header'].new_background = ''

					reader.onload = (function (theFile) {
						return function (e) {
							$this.form['header'].new_background = e.target.result
							$this.form['header'].background = e.target.result
						}

					})(f)

					reader.readAsDataURL(f)
				}
			},
			clearBackgroundImage: function ($type) {
				this.$refs.headerFileInput.reset();
				this.form[$type].background = ''
				this.form[$type].background_name = ''
				this.form[$type].new_background = ''
			},
			opacityCalc: function ($val) {
				$val = $val.replace('%', '')
				return parseInt($val) / 100
			},
			opacityFormat: function ($type) {
				this.form[$type].background_opacity = this.form[$type].background_opacity.replace('%', '') + '%'
			},
			countDownChanged (dismissCountDown) {
				this.alert.dismissCountDown = dismissCountDown
			},
			replacePointsTag(text){
				return text
					.replace(/{point-name}/ig, this.point_name)
					.replace(/{points-name}/ig, this.point_namePlural);
			},
			isActions(section) {
				var noneEmptyActions = [];
				section.actions.forEach((action, index) => {
					if(action != undefined){
						noneEmptyActions.push(action);
					}
				});
				return !(section.options.length == noneEmptyActions.length && section.selectedActions.length > section.options.length);
			},
		},
	});

	function copyText (id) {
		el = $(id)
		el.select()
		document.execCommand('copy')
	}
</script>
@endsection

