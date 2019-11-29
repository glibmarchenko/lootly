/*
 |--------------------------------------------------------------------------
 | Rewards Page
 |--------------------------------------------------------------------------
 */

window.Vue = require('vue');
window.Bus = new Vue();

require('./_components');

var app = new Vue({
	el: '#rewards-page',
	data: {
		point_name: data.points ? data.points.name : 'Point',
		point_namePlural: data.points ? data.points.plural_name : 'Points',
		form: {
			header: {
				title: data.header != null && data.header != '' ? data.header.title : 'Rewards Program',
				subtitle: data.header != null && data.header != '' ? data.header.subtitle : 'Check out the amazing ways to earn rewards with us.',
				button1: data.header != null && data.header != '' ? data.header.button1_text : 'Sign Up',
				button2: data.header != null && data.header != '' ? data.header.button2_text : 'Login',
				button1Link: data.header != null && data.header != '' ? data.header.button1_link : data.merchant_domain+'/account/sign-up',
				button2Link: data.header != null && data.header != '' ? data.header.button2_link : data.merchant_domain+'/account/login',
				background: data.header != null && data.header != '' ? data.header.background_url : '',
				new_background: '',
				background_opacity: data.header != null && data.header != '' ? data.header.background_opacity : '100%',
				background_name: data.header != null && data.header != '' ? data.header.background_name : '',
				design: {
					color: data.header != null && data.header != '' ? data.header.header_color : '#0170af',
					titleColor: data.header != null && data.header != '' ? data.header.title_color : '#fff',
					subtitleColor: data.header != null && data.header != '' ? data.header.subtitle_color : '#fff',
					buttonColor: data.header != null && data.header != '' ? data.header.button_color : '#fff',
					buttonTextColor: data.header != null && data.header != '' ? data.header.button_text_color : '#000',
					titleFontSize: data.header != null && data.header != '' ? data.header.title_font_size : '35',
					subtitleFontSize: data.header != null && data.header != '' ? data.header.subtitle_font_size : '16',
					buttonFontSize: data.header != null && data.header != '' ? data.header.button_font_size : '15'
				}
			},
			howItWorks: {
				title: data.how_it_works ? data.how_it_works.title : 'How does it work?',
				step1: data.how_it_works ? data.how_it_works.steep1_text : 'Join',
				step2: data.how_it_works ? data.how_it_works.steep2_text : 'Earn Points',
				step3: data.how_it_works ? data.how_it_works.steep3_text : 'Redeem',
				design: {
					titleColor: data.how_it_works ? data.how_it_works.title_color : '#000',
					stepsColor: data.how_it_works ? data.how_it_works.steps_color : '#000',
					titleFontSize: data.how_it_works ? data.how_it_works.title_font_size : '24',
					stepsFontSize: data.how_it_works ? data.how_it_works.steps_front_size : '16',
					circleFullColor: data.how_it_works ? data.how_it_works.circle_full_color : '#1cc04a',
					circleEmptyColor: data.how_it_works ? data.how_it_works.circle_empty_color : '#ebeef5',
					arrowsColor: data.how_it_works ? data.how_it_works.arrows_color : '#1cc04a'
				}
			},
			earning: {
				title: data.earning ? data.earning.title : 'Want to earn more {point-name}?',
				actions: data.selected_actions,
				design: {
					titleColor: data.earning ? data.earning.title_color : '#000',
					actionTextColor: data.earning ? data.earning.action_text_color : '#fff',
					pointColor: data.earning ? data.earning.point_color : '#0170af',
					ribbonColor: data.earning ? data.earning.ribbon_color : '#fff',
					boxColor: data.earning ? data.earning.box_color : '#0170af',
					titleFontSize: data.earning ? data.earning.title_font_size : '24',
					actionFontSize: data.earning ? data.earning.action_font_size : '14',
					pointFontSize: data.earning ? data.earning.point_font_size : '16'
				}
			},
			vip: {
				status: data.vip.program_status,
				title: data.vip ? data.vip.title : 'Become a VIP and earn even more {point-name}',
				design: {
					titleColor: data.vip ? data.vip.title_color : '#000',
					tierNameColor: data.vip ? data.vip.tier_name_color : '#000',
					multiplierColor: data.vip ? data.vip.multiplier_color : '#585858',
					requirementsColor: data.vip ? data.vip.requirements_color : '#585858',

					titleFontSize: data.vip ? data.vip.title_font_size : '24',
					tierNameFontSize: data.vip ? data.vip.tier_name_font_size : '18',
					multiplierFontSize: data.vip ? data.vip.multiplier_font_size : '16',
					requirementsFontSize: data.vip ? data.vip.requirements_font_size : '15'
				},
				tiers: data.vips,
			},
			spending: {
				title: data.spending ? data.spending.title : 'Redeem your {points-name}',
				actions: data.selected_rewards,
				design: {
					titleColor: data.spending ? data.spending.title_color : '#000',
					boxTextColor: data.spending ? data.spending.box_text_color : '#fff',
					boxColor: data.spending ? data.spending.box_color : '#0170af',
					titleFontSize: data.spending ? data.spending.title_font_size : '24',
					boxFontSize: data.spending ? data.spending.box_font_size : '18'
				}
			},
			referral: {
				status: data.referral.program_status,
				title: data.referral ? data.referral.title : 'Refer a Friend',
				subtitle: data.referral ? data.referral.subtitle : 'Share our store to earn great discounts for yourself and a friend!',
				design: {
					titleColor: data.referral ? data.referral.title_color : '#000',
					subtitleColor: data.referral ? data.referral.subtitle_color : '#667885',
					titleFontSize: data.referral ? data.referral.title_font_size : '24',
					subtitleFontSize: data.referral ? data.referral.subtitle_font_size : '16'
				},
				receiver: {
					exists: data.receiver ? '1' : '',
					name: data.receiver ? data.receiver.reward_name : null,
					text: data.receiver ? data.receiver.reward_text : null,
					reward_icon: data.receiver ? data.receiver.reward_icon : null,
					reward_icon_name: data.receiver ? data.receiver.reward_icon_name : null,
				},
				sender: {
					exists: data.sender ? '1' : '',
					name: data.sender ? data.sender.reward_name : null,
					text: data.sender ? data.sender.reward_text : null,
					reward_icon: data.sender ? data.sender.reward_icon : null,
					reward_icon_name: data.sender ? data.sender.reward_icon_name : null,
				},
			},
			faq: {
				status: data.faq ? data.faq.status : 1,
				title: data.faq ? data.faq.title : 'Frequently Asked Questions',
				questions: data.questions ? data.questions : [],
				design: {
					titleColor: data.faq ? data.faq.title_color : '#000',
					questionColor: data.faq ? data.faq.question_color : '#000',
					answerColor: data.faq ? data.faq.answer_color : '#545558',
					titleFontSize: data.faq ? data.faq.title_font_size : '24',
					questionFontSize: data.faq ? data.faq.question_font_size : '16',
					answerFontSize: data.faq ? data.faq.answer_font_size : '15'
				}
			},
			branding: true
		},
		html: '',
		html_mode : data.reward_settings.html_mode == 1 ? 1 : 0,
	},
	created: function () {
		// console.log(data);
	},
	methods: {
		replacePointsTag: function(text){
			if(!text) {
				return '';
			} else {
				return text
					.replace(/{point-name}/ig, this.point_name)
					.replace(/{points-name}/ig, this.point_namePlural);
			}
		},
	}
})


