@extends('layouts.app')
@section('title', 'Free Shipping discount')
@section('content')
    <div id="action-page" class="loader m-t-20 m-b-10" v-cloak>
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
                    <a href="{{ route('referrals.reward') }}" class="bold f-s-15 color-blue">
                        <i class="arrow left blue"></i>
                        <span class="m-l-5">Referrals Rewards</span>
                    </a>
                </div>
                <div class="col-md-12">
                    <h3 class="page-title m-t-0 color-dark pull-left">
                        Sender Reward: <span v-text="form.defaultRewardName"></span>
                    </h3>
                    <button type="button" @click="saveAction" class="btn btn-save pull-right">Save</button>
                </div>
            </div>
            <div class="row p-t-25 p-b-25">
                <div class="col-md-7 col-12">
                    <div class="well">
                        <div :class="{ 'loading' : loading }" v-cloak>
                            <div class="row section-border-bottom p-b-10 m-b-15">
                                <div class="col-md-8">
                                    <div class="form-group m-b-0">
                                        <label class="bolder f-s-15 m-b-0 m-t-5">
                                            Reward Details
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group m-b-0">
                                        <label class="light-font m-b-10">
                                            <span>Coupon prefix (optional)</span>
                                        </label>
                                        <input class="form-control" v-model="form.coupon.prefix"
                                               :placeholder="`e.g. FREESHIPPING`">
                                    </div>
                                </div>
                            </div>
                            <div class="row m-t-10" v-show="!merchantSettings.isWooCommerce">
                                <div class="col-md-12">
                                    <div class="form-group m-b-0">
                                        <label class="light-font m-b-10">
                                            <span>Maximum shipping amount discount</span>
                                        </label>
                                        <input class="form-control" name="reward.maxShipping" v-model="form.reward.maxShipping"
                                               placeholder="e.g. 30">
                                    </div>
                                </div>
                            </div>
                            <div class="row m-t-10">
                                <div class="col-md-12">
                                    <div class="form-group m-b-0">
                                        <label class="light-font m-b-10">
                                            <span>Minimum order amount (optional)</span>
                                        </label>
                                        <input class="form-control" v-model="form.reward.minOrder"
                                               placeholder="e.g. 10">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="well m-t-20">
                        <div :class="{ 'loading' : loading }" v-cloak>
                            <div class="row m-b-15">
                                <div class="col-md-12">
                                    <label class="bolder f-s-15 m-b-0 pull-left">
                                        Email Notification
                                    </label>
                                    <a href="{{route('display.email.referral.sender-reward')}}"
                                       class="bolder f-s-14 color-blue pull-right">Edit Notification</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="m-b-10">Send your customers an email when they complete this action.</p>
                                    <b-form-checkbox value="1" v-model="form.emailNotification">Yes, send an email
                                        notification
                                    </b-form-checkbox>
                                </div>
                            </div>
                        </div>
                    </div>

                    <referrals-reward-design
                            v-bind:form="form"
                            :email-text-preview="emailTextPreview"
                            :merchant-settings="merchantSettings"
                            :icon-default-class="icon_default_class"
                            :loading="loading"
                            :config="config">
                    </referrals-reward-design>

                    <referrals-reward-coupon-expiration
                            v-bind:form="form"
                            :loading="loading"
                    ></referrals-reward-coupon-expiration>

                    @include('points.spending.rewards._partials._reward_codes')

                </div>
                <referrals-reward-preview
                        v-bind:form="form"
                        :loading="loading"
                        :reward_text='rewardTextPreview'
                        :reward_name='programNamePreview'>
                </referrals-reward-preview>
            </div>
        </form>

        @include('points.spending.rewards._partials._modal_reward_codes')

    </div>
@endsection
@section('scripts')
    @include('layouts.trumbowyg-scripts')

    <script type="module">
      import { rewardCodesMixin } from '/js/mixins/reward-codes-mixin.js';

      Vue.component('Trumbowyg', VueTrumbowyg.default);

      var page = new Vue({
        el: '#action-page',
        mixins: [ rewardCodesMixin ],
        data: {
          merchantId: Spark.state.currentTeam.id,
          merchantSettings: {
            name: 'TestStore',
            logo: '{{ config('email-notification.default_logo') }}',
            remove_branding: false,
            points: {
              singular_name: 'point',
              plural_name: 'points'
            },
            currency: {
              id: null,
              sign: '$',
              name: 'USD',
              displaySign: true,
              selectedFormat: '$'
            },
            integrations: {},
            isWooCommerce: false
          },
          form: {
            reward_id: '',
            defaultRewardName: 'Free Shipping discount',
            program: {
              name: 'You will receive',
              nameDefault: 'You will receive',
              rewardType: 'Free Shipping',
              rewardText: '',
              rewardTextDefault: '{currency}{max-shipping} off shipping {min-shipping}',
              emailTextDefault: 'You just earned a <strong>{reward-text}</strong> discount at {company-name}, as thanks for referring {referral-name} to make a purchase at our store.',
              icon: null,
              icon_name: 'icon-package',
              reward_icon: null,
              status: true,
            },
            reward: {
              minOrder: '',
              maxShipping: ''
            },
            coupon: {
              status: 0,
              prefix: '',
              limit: {
                value: '',
                duration: 'days'
              }
            },
            iconPreview: '',
            emailNotification: 1,
            reward_name_tags: [],
            reward_text_tags: ['{currency}', '{max-shipping}', '{min-shipping}'], // third tag is using in min value generating
            reward_email_tags: ['{referral-name}', '{customer}', '{reward-text}', '{company-name}', '{coupon-code}'],
          },
          loading: true,
          rewardEmailTextPreview: '',
          icon_default_class: 'icon-package',
          icon_parent_el: 'icon_el',
          config: {
            svgPath: '/fonts/icons/trumbowyg-icons.svg',
            btns: [['fontsize', 'foreColor', 'bold', 'italic'], ['lineheight', 'horizontalRule', 'link'], ['justifyLeft', 'justifyCenter', 'justifyRight']]
          },
          alert: {
            type: '',
            text: '',
            dismissSecs: 5,
            dismissCountDown: 0
          },
        },
        created: function () {
          this.getMerchantSettings()
          this.getRewards()
        },
        methods: {
          getMerchantSettings: function () {
            axios.get('/api/merchants/' + this.merchantId + '/settings/common').then(response => {
              if (response.data && response.data.data) {
                let merchantSettings = response.data.data
                // Get merchant settings
                this.merchantSettings.name = merchantSettings.name
                if (merchantSettings.logo) { this.merchantSettings.logo = merchantSettings.logo }

                this.merchantSettings.integrations = merchantSettings.integrations;

                // Get email notification settings
                if (merchantSettings.email_notification_settings && merchantSettings.email_notification_settings.data) {
                  this.merchantSettings.remove_branding = !!merchantSettings.email_notification_settings.data.remove_branding
                }

                // Get currency settings
                this.merchantSettings.currency.displaySign = !!merchantSettings.currency_display_sign
                if (merchantSettings.merchant_currency && merchantSettings.merchant_currency.data) {
                  this.merchantSettings.currency.id = merchantSettings.merchant_currency.data.id
                  this.merchantSettings.currency.sign = merchantSettings.merchant_currency.data.currency_sign
                  this.merchantSettings.currency.name = merchantSettings.merchant_currency.data.name
                  this.merchantSettings.currency.selectedFormat = (this.merchantSettings.currency.displaySign) ? this.merchantSettings.currency.sign : this.merchantSettings.currency.name
                }
                if (merchantSettings.points_settings && merchantSettings.points_settings.data) {
                  this.merchantSettings.points.singular_name = merchantSettings.points_settings.data.name
                  this.merchantSettings.points.plural_name = merchantSettings.points_settings.data.plural_name
                }
                this.merchantSettings.isWooCommerce = merchantSettings.woocommerce
                if (this.merchantSettings.isWooCommerce) {
                    this.form.program.rewardTextDefault = 'Free shipping'
                }
              }
            }).catch(errors => {
              console.log(errors)
            })
          },
          getRewards: function () {
            let _this = this
            axios.get('/referrals/rewards/sender/get/' + this.form.defaultRewardName).then((response) => {
              if (response.data.rewards.merchant_reward.length === 0) {

              } else {
                response.data.rewards.merchant_reward.forEach(function (merchant_reward, key) {
                  let coupon_expiration = merchant_reward.coupon_expiration_time.split(' ')
                  _this.form.reward_id = merchant_reward.id
                  _this.form.program.status = merchant_reward.active_flag
                  _this.form.reward.points = merchant_reward.points_required
                  _this.form.reward.maxShipping = merchant_reward.max_shipping
                  if (merchant_reward.reward_icon_name) _this.form.program.icon_name = merchant_reward.reward_icon_name
                  _this.form.coupon.status = merchant_reward.coupon_expiration
                  _this.form.coupon.prefix = merchant_reward.coupon_prefix
                  _this.form.reward.minOrder = merchant_reward.order_minimum
                  _this.form.program.name = merchant_reward.reward_name
                  _this.form.program.rewardText = merchant_reward.reward_text
                  _this.form.program.rewardTextDefault = merchant_reward.rewardDefaultText
                  _this.form.program.emailTextDefault = merchant_reward.reward_email_text
                  _this.form.emailNotification = merchant_reward.send_email_notification
                  _this.form.program.reward_icon = merchant_reward.reward_icon
                  _this.form.coupon.limit.value = coupon_expiration[0]
                  _this.form.coupon.limit.duration = coupon_expiration[1]
                })
              }
              if (!this.form.iconPreview) {
                showPreviewIcon(this.form.program.reward_icon, this.icon_default_class, this.icon_parent_el)
              } else {
                clearPreviewIcon(this.icon_default_class, this.icon_parent_el)
              }
              this.getRewardCoupons()
              this.loading = false
            }).catch((error) => {
              this.loading = false
              if (error.response && error.response.data) this.errors = error.response.data.errors
            })
          },
          saveAction: function () {
            this.loading = true
            axios.post('/referrals/rewards/sender/store', this.form).then((response) => {
              this.alert.dismissCountDown = this.alert.dismissSecs
              if (response.data.status == 404) {
                this.alert.type = 'danger'
                this.alert.text = response.data.message
              } else {
                this.setFormReward(response.data.merchant_reward.id);

                this.alert.type = 'success'
                this.alert.text = 'Reward saved successfully'
              }
              this.loading = false
            }).catch((error) => {
              this.loading = false
              clearErrors(this.$el)
              showErrors(this.$el, error.response.data.errors)
            })
          },
          countDownChanged (dismissCountDown) {
            this.alert.dismissCountDown = dismissCountDown
          },
          minOrderGen: function () {
            const minPhrase = 'for orders over '
            const name = this.form.program.rewardTextDefault
            if (!this.form.reward.minOrder) {
              if (name.indexOf(minPhrase) != -1) {
                this.form.program.rewardTextDefault = [name.split(minPhrase)[0],
                  name.substr(name.indexOf(minPhrase) + minPhrase.length)].join('')
              }
              this.wasMinGen = false
              return
            }
            if (this.wasMinGen || name.indexOf(minPhrase) != -1) {
              this.wasMinGen = true
              return
            }
            const minTag = this.form.reward_text_tags[2] //get tag to add text before it
            const tagIndex = name.indexOf(minTag)
            if (tagIndex != -1) {
              this.form.program.rewardTextDefault = [name.split(minTag)[0],
                minPhrase,
                minTag,
                name.split(minTag)[1]].join('')
              this.wasMinGen = true
            }
          }
        },
        computed: {
          programNamePreview: function () {
            return this.form.program.name = this.form.program.nameDefault
          },
          rewardTextPreview: function () {
            this.minOrderGen()
            return this.form.program.rewardText = this.form.program.rewardTextDefault
              .replace(/{max-shipping}/g, this.form.reward.maxShipping)
              .replace(/{currency}/g, this.merchantSettings.currency.selectedFormat)
              .replace(/{min-shipping}/g, this.form.reward.minOrder ? `${this.merchantSettings.currency.selectedFormat}${this.form.reward.minOrder}` : '')
          },
          emailTextPreview: function () {
            const points_name = this.form.reward.value > 1 ? this.merchantSettings.points.plural_name : this.merchantSettings.points.singular_name

            if (this.form.program.emailTextDefault) {
              this.rewardEmailTextPreview = this.form.program.emailTextDefault
                .replace(/{points}/g, this.form.reward.points)
                .replace(/{points-name}/g, points_name ? points_name : '{points-name}')
                .replace(/{company(-name)?}/g, this.merchantSettings.name)
                .replace(/{reward-text}/g, this.rewardTextPreview)
            }

            let preview = `
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="text"
                            style="padding-bottom: 13px; color:#2a2a2a; font-family:Arial,sans-serif; font-size:16px; line-height:26px; text-align:center;">
                            {customer},
                        </td>
                    </tr>
                    <tr>
                        <td class="text pb-25"
                            style="padding-bottom: 46px; color:#2a2a2a; font-family:Arial,sans-serif; font-size:16px; line-height:26px; text-align:center;">
                            ${this.rewardEmailTextPreview}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-2" style="padding-bottom: 30px; color:#2a2a2a; font-family:Arial,sans-serif; font-size:20px; line-height:24px; text-align:center; font-weight:bold;">
                            {coupon-code}
                        </td>
                    </tr>
                    <tr>
                        <td class="img-center" style="padding-bottom: 27px; text-align:center;">
                            <span style="color: #7ab74d;">{reward-icon}</span>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            {button}
                        </td>
                    </tr>
                </table>
                `
            const button = '<table border="0" cellspacing="0" cellpadding="0" style="margin-bottom:40px"><tr><td class="text-button" style="padding: 14px 37px; border-radius: 5px; color:#ffffff; font-family:Arial,sans-serif; font-size:17px; line-height:21px; text-align:center; font-weight:bold;" bgcolor="#022c82"><a href="#" target="_blank" class="link-white" style="color:#ffffff; text-decoration:none;"><span class="link-white" style="color:#ffffff; text-decoration:none;">'
              + 'Shop Now' + // btn text
              '</span></a></td></tr></table>'
            return preview
              .replace(/{company(-name)?}/g, this.merchantSettings.name)
              .replace(/{customer}/g, 'Joe Smith')
              .replace(/{referral-name}/g, 'Ryan')
              .replace(/{coupon-code}/g, '123456abcdef')
              .replace(/{reward-icon}/g, '<img src="<?php echo url("/images/icons/email-notification/ico_dollar.jpg"); ?>" width="84" height="84" border="0" alt="" />')
              .replace(/{button}/g, button)
          },
        },
        watch: {
          // 'form.reward.maxShipping': function() {
          //     if(this.form.currency){
          //         this.form.program.rewardText = 'Up to '+ this.form.currency +' '+this.form.reward.maxShipping+' off';
          //     }
          // }
        }
      })

    </script>
@endsection

