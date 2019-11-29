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
        <span :class="{'loading' : loadingSave}">
        <form>
            <div class="row p-b-10 section-border-bottom">
                <div class="col-md-12 m-b-15">
                    <a href="{{ route('points.spending.rewards') }}" class="bold f-s-15 color-blue">
                        <i class="arrow left blue"></i>
                        <span class="m-l-5">Add Spending Reward</span>
                    </a>
                </div>
                <div class="col-md-12">
                    <h3 class="page-title m-t-0 color-dark pull-left" v-text="programNamePreview"></h3>
                    <button type="button" @click="saveAction" class="btn btn-save pull-right">Save</button>
                </div>
            </div>
            <div class="row p-t-25 p-b-25">
                <div class="col-md-7 col-12">
                    <div class="well">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group m-b-0">
                                    <label class="light-font m-b-0 m-t-5">
                                        Free Shipping discount is currently
                                        <span class="bold"
                                              v-text="form.program.status == 0 ? 'Disabled' : 'Enabled'"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <a @click="toogleProgramStatus" v-cloak>
                                <span v-if="form.program.status == 0">
                                    <span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
                                </span>
                                    <span v-else>
                                    <span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="well m-t-20">
                        <div :class="{ 'loading' : loading }" v-cloak>
                            <div class="row section-border-bottom p-b-10 m-b-15">
                                <div class="col-md-8">
                                    <div class="form-group m-b-0">
                                        <label class="bolder f-s-15 m-b-0 m-t-5">
                                            General Settings
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div :class="{ 'loading' : loading }" v-cloak>
                                        <div class="form-group m-b-0">
                                            <label class="light-font m-b-10">
                                                <span>Amount of  @{{merchantSettings.points.plural_name}}  to redeem for this order discount</span>
                                            </label>
                                            <input class="form-control"
                                                   v-model="form.reward.points"
                                                   name="reward.points"
                                                   placeholder="e.g. 100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="well m-t-20">
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
                                        <input class="form-control"
                                               v-model="form.coupon.prefix"
                                               :placeholder="`e.g. FREESHIPPING`">
                                    </div>
                                </div>
                            </div>
                            <div class="row m-t-10" v-show="{{ !$woocommerce }}">
                                <div class="col-md-12">
                                    <div class="form-group m-b-0">
                                        <label class="light-font m-b-10">
                                            <span>Maximum shipping amount discount</span>
                                        </label>
                                        <input class="form-control"
                                               v-model="form.reward.maxShipping"
                                               name="reward.maxShipping"
                                               placeholder="e.g. 50">
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
                                    <a href="/display/email-notifications/points/spent" target="blank"
                                       class="bolder f-s-14 color-blue pull-right">Edit Notification</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="m-b-10">Send your customers an email when they complete this action.</p>
                                    <b-form-checkbox v-model="form.emailNotification">Yes, send an email
                                        notification
                                    </b-form-checkbox>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="well m-t-20">
                        <div :class="{ 'loading' : loading }" v-cloak>
                            @if(!$have_email_permissions)
                                <no-access :loading="loading"
                                    title="{{$email_upsell->upsell_title}}"
                                    desc="{{$email_upsell->upsell_text}}"
                                    icon="{{$email_upsell->upsell_image}}"
                                    plan="{{$email_upsell->getMinPlan()->name}}"></no-access>
                            @else
                                <div class="row section-border-bottom p-b-10 m-b-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="bolder f-s-15 m-b-0">
                                                Design
                                            </label>
                                            <a class="bolder f-s-14 color-blue pull-right" href=""
                                               @click.prevent="openModal">Preview Notification</a>
                                            <custom-modal
                                                    title="Preview email notification"
                                                    id="preview-email"
                                                    :toggle_modal="isModalOpen"
                                                    :close_callback="hideModal">
                                                <span v-html="bodyPreview"></span>
                                            </custom-modal>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <div>
                                                <label class="light-font m-b-5">
                                                    Reward Name
                                                </label>
                                                <input class="form-control" placeholder="Free Shipping discount"
                                                       v-model="form.program.nameDefault">
                                                <div class="row m-t-15 p-b-10 section-border-bottom">
                                                    <div class="col-md-12">
                                                        <span class="custom-tag" v-for="tag in reward_name_tags">
                                                            <span v-text="tag"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="light-font m-t-15 m-b-5">
                                                    Point Requirement Text
                                                </label>
                                                <input class="form-control" placeholder=""
                                                       v-model="form.program.rewardTextDefault">
                                                <div class="row m-t-15 p-b-10 section-border-bottom">
                                                    <div class="col-md-12">
                                                        <span class="custom-tag" v-for="tag in reward_text_tags">
                                                            <span v-text="tag"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="light-font m-t-15 m-b-5">
                                                    Reward Email Text
                                                </label>
                                                <div class="reward-email-text">
                                                    <trumbowyg
                                                            v-model="form.program.emailTextDefault"
                                                            @tbw-blur="blurEmailField"
                                                            @tbw-init="EditorCreated"
                                                            id="emailDefaultText"
                                                            :config="config"
                                                            class="editor"
                                                    ></trumbowyg>
                                                </div>
                                                <div class="row m-t-15 p-b-10 section-border-bottom">
                                                    <div class="col-md-12">
                                                        <span class="custom-tag email-tag" v-for="tag in tags">
                                                            <span v-text="tag" v-on:click="emailTagClick"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                          @if(!$have_customization_permissions)
                                              <no-access :loading="loading"
                                                  title="{{$customizations_upsell->upsell_title}}"
                                                  desc="{{$customizations_upsell->upsell_text}}"
                                                  icon="{{$customizations_upsell->upsell_image}}"
                                                  plan="{{$customizations_upsell->getMinPlan()->name}}"></no-access>
                                          @else

                                            <div class="light-font m-t-15 m-b-5">
                                                <p>Custom Icon</p>
                                                <span class="f-s-13 bolder color-light-grey">Recommended 250px x 250px - will auto size to fit</span>
                                            </div>
                                            <div class="file-drag-drop m-t-15" v-cloak>
                                                <b-form-file class="upload-icon" @change="iconImageChange"
                                                             v-model="form.program.icon"
                                                             accept="image/*"></b-form-file>

                                                <div class="custom-file-overlay">
                                                <span class="img" v-if="form.program.reward_icon || form.iconPreview">
                                                <img v-if="form.iconPreview" class="m-b-5" :src="form.iconPreview"
                                                     style="max-height:70px;max-width: 100%">
                                                        <img v-else-if="form.program.reward_icon" class="m-b-5"
                                                             :src="form.program.reward_icon"
                                                             style="max-height:70px;max-width: 100%">
                                                </span>
                                                    <span class="img" v-else>
                                                    <i class="icon-image-upload"></i>
                                                </span>
                                                    <h5 class="float f-s-17 bold"
                                                        v-if="(form.program.icon_name && form.program.action_icon) || form.iconPreview || form.program.reward_icon">
                                                    <span class="text">
                                                        <span v-text="form.program.icon_name"></span>
                                                    </span>
                                                    </h5>
                                                    <h5 class="float f-s-17 bold"
                                                        v-else-if="form.program.icon && form.program.icon.name">
                                                    <span class="text">
                                                        <span v-text="form.program.icon && form.program.icon.name"></span>
                                                    </span>
                                                    </h5>
                                                    <h5 class="f-s-17 bold" v-else>
                                                        Drag files to upload
                                                    </h5>
                                                    <i v-if="form.iconPreview ||  form.program.action_icon || form.program.reward_icon"
                                                       @click="cleariconImage(form.reward_id)"
                                                       class="fa fa-times color-light-grey pointer"></i>
                                                </div>
                                            </div>
                                          @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if(!$have_rest_permissions)
                        <div class="m-t-20 p-b-10">
                            <no-access :loading="loading"
                                       title="{{$restrictions_upsell->upsell_title}}"
                                       desc="{{$restrictions_upsell->upsell_text}}"
                                       icon="{{$restrictions_upsell->upsell_image}}"
                                       plan="{{$restrictions_upsell->getMinPlan()->name}}"></no-access>
                        </div>

                    @else
                        <div class="well restrictions-section m-t-20 p-b-10">
                            <div :class="{ 'loading' : loading }" v-cloak>

                                @include('points.common_sections.restrictions', ['type' => 'Spending', 'restrictions' => ['customer', 'product']])
                            </div>
                        </div>
                    @endif
                    <div class="well m-t-20 coupon-expiration">
                        <div :class="{ 'loading' : loading }" v-cloak>
                            <div class="row section-border-bottom p-b-10 m-b-15">
                                <div class="col-md-8">
                                    <div class="form-group m-b-0">
                                        <p class="bolder f-s-15 m-b-5">Coupon Expiration</p>
                                        <label class="light-font m-b-0">
                                            <div style="float: left;"
                                                 v-text='(form.reward.maxShipping ? (merchantSettings.currency.displaySign ? (merchantSettings.currency.selectedFormat + form.reward.maxShipping) : (form.reward.maxShipping + " " + merchantSettings.currency.selectedFormat)) : "{value}") + " off shipping coupon expiration is"'>
                                            </div>
                                            &nbsp;
                                            <span class="bold"
                                                  v-text="form.coupon.status == 0 ? 'Disabled' : 'Enabled'"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 text-right">
                                    <a @click="toogleCouponLimit" v-cloak>
                                    <span v-if="form.coupon.status == 0">
                                        <span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
                                    </span>
                                        <span v-else>
                                        <span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
                                    </span>
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-8 p-r-0">
                                    <input class="form-control" v-model="form.coupon.limit.value" placeholder="e.g. 30">
                                </div>
                                <div class="col-md-4 col-4">
                                    <select class="mb-3 form-control custom-select"
                                            v-model="form.coupon.limit.duration">
                                        <option value="days">Days</option>
                                        <option value="weeks">Weeks</option>
                                        <option value="months">Months</option>
                                        <option value="years">Years</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="well m-t-20 spending-expiration">
                        @if(!$have_limits_permissions)
                            <no-access :loading="loading"
                                       title="{{$limits_upsell->upsell_title}}"
                                       desc="{{$limits_upsell->upsell_text}}"
                                       icon="{{$limits_upsell->upsell_image}}"
                                       plan="{{$limits_upsell->getMinPlan()->name}}"></no-access>
                        @else
                            <div :class="{ 'loading' : loading }" v-cloak>
                                <div class="row section-border-bottom p-b-10 m-b-15">
                                    <div class="col-md-8">
                                        <div class="form-group m-b-0">
                                            <p class="bolder f-s-15 m-b-5">Spending Limits</p>
                                            <label class="light-font m-b-0">
                                                Spending Limits are
                                                <span class="bold"
                                                      v-text="form.spending.limit.status == 0 ? 'Disabled' : 'Enabled'"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <a @click="toggleSpendingLimit" v-cloak>
                                    <span v-if="form.spending.limit.status == 0">
                                        <span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
                                    </span>
                                            <span v-else>
                                        <span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
                                    </span>
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 col-8 p-r-0">
                                        <div class="input-group">
                                            <input class="form-control" v-model="form.spending.limit.value"
                                                   :placeholder="form.point_namePlural">
                                            <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2"
                                                  v-html="form.point_namePlural"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-4">
                                        <select class="mb-3 form-control custom-select"
                                                v-model="form.spending.limit.period">
                                            <option value="lifetime">Per lifetime</option>
                                            <option value="month">Per month</option>
                                            <option value="week">Per week</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    @include('points.spending.rewards._partials._reward_codes')

                    <div class="well m-t-20 zapier-settings" v-show="merchantSettings.isZapierConnected">
                        <div :class="{ 'loading' : loading }" v-cloak>
                            <div class="row section-border-bottom p-b-10 m-b-15">
                                <div class="col-md-8">
                                    <div class="form-group m-b-0">
                                        <p class="bolder f-s-15 m-b-5">Zapier</p>
                                        <label class="light-font m-b-0">
                                            <div style="float: left;">
                                                Zapier trigger
                                            </div>
                                            &nbsp;
                                            <span class="bold"
                                                  v-text="form.zapier.status == 0 ? 'Disabled' : 'Enabled'"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 text-right">
                                    <a @click="toggleZapierTrigger" v-cloak>
                                    <span v-if="form.zapier.status == 0">
                                        <span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
                                    </span>
                                        <span v-else>
                                        <span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
                                    </span>
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-12">
                                    <input class="form-control" v-model="form.zapier.name" placeholder="zap key 12345">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <points-reward-preview
                        :reward_points='form.reward.points'
                        :reward_text='rewardTextPreviewGen()'
                        :program_name='programNamePreview'
                        :loading='loading'
                        icon_name="free-shipping">
                </points-reward-preview>
            </div>
        </form>
        </span>

        @include('points.spending.rewards._partials._modal_reward_codes')

    </div>

@endsection

@section('scripts')
    @include('layouts.trumbowyg-scripts')
    <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
    <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>

    <script type="module">
      import { rewardCodesMixin } from '/js/mixins/reward-codes-mixin.js';
      import { restrictionsMixin } from '/js/mixins/restrictions-mixin.js';

      Vue.component('Trumbowyg', VueTrumbowyg.default);

      var page = new Vue({
        el: '#action-page',
        components: {
          Multiselect: window.VueMultiselect.default
        },
        mixins: [ rewardCodesMixin, restrictionsMixin ],
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
            isZapierConnected: false
          },
          form: {
            reward_id: '',
            point_namePlural: '<?php echo isset($points_settings) ? $points_settings->plural_name : 'Rewards Issued'; ?>',
            program: {
              defaultRewardName: 'Free shipping discount',
              status: 1,
              name: 'Free Shipping',
              nameDefault: (parseInt('{{ $woocommerce }}') ? 'Free' : '{value} Off') + ' Shipping {min-value}',
              rewardType: 'Free shipping',
              rewardTextDefault: '{points} {points-name}',
              rewardText: '',
              rewardTextPreview: '',
              emailTextDefault: 'You just redeemed <strong>{points} {points-name}</strong> for a <strong>{reward-name}</strong> <br/>at {company-name}. Below is your coupon <br/>code to use on your next purchase.',
              icon: null,
              reward_icon: null,
              icon_name: 'icon-package',
            },

            reward: {
              points: '',
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
            zapier: {
              status: 0,
              name: ''
            },
            restrictions: {
              status: 0,
              customer: [],
              product: []
            },
            spending: {
                type: 0,
                fixedText: '{points} {points-name}',
                variableText: '{points} {points-name} per {amount} spent',
                value: '',
                limit: {
                    status: 0,
                    value: '',
                    period: 'week',
                    type: 'points'
                }
            },
            iconPreview: '',
            emailNotification: true,
            isWooCommerce: parseInt('{{ $woocommerce }}')
          },
          icon_default_class: 'icon-package',
          icon_parent_el: 'free-shipping',
          alert: {
            type: '',
            text: '',
            dismissSecs: 5,
            dismissCountDown: 0
          },
          wasMinGen: false,
          loading: true,
          rewardEmailTextPreview: '',
          loadingSave: false,
          merchant_reward_id: '{{ $id }}',
          isModalOpen: false,
          config: {
            svgPath: '/fonts/icons/trumbowyg-icons.svg',
            btns: [['fontsize', 'foreColor', 'bold', 'italic'], ['lineheight', 'horizontalRule', 'link'], ['justifyLeft', 'justifyCenter', 'justifyRight'], ['viewHTML']]
          },
          reward_config: {
            svgPath: '/fonts/icons/trumbowyg-icons.svg',
            btns: [['fontsize', 'foreColor', 'bold', 'italic'], ['lineheight', 'horizontalRule', 'link'], ['justifyLeft', 'justifyCenter', 'justifyRight'], ['viewHTML']]
          },
          tags: ['{points}', '{points-name}', '{reward-name}', '{company-name}'],
          reward_name_tags: ['{currency}', '{value}', '{min-value}'],  // second tag is using in min value generating
          reward_text_tags: ['{points}', '{points-name}'],
        },
        created: function () {
          this.getMerchantSettings()
          this.getRewards()
          this.getRestrictionsData()

          let message = window.localStorage.getItem('successAlert')
          if (message) {
            this.alert.dismissCountDown = this.alert.dismissSecs
            this.alert.type = 'success'
            this.alert.text = message
            window.localStorage.removeItem('successAlert')
          }
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
                this.merchantSettings.isZapierConnected = merchantSettings.zapier_connected
              }
            }).catch(errors => {
              console.log(errors)
            })
          },
          hideModal: function () {
            this.isModalOpen = false
          },
          openModal: function () {
            this.emailTextPreviewGen()
            this.isModalOpen = true
          },
          getRewards: function () {
            let discount_name = window.sessionStorage.getItem('discount_name')
            let _this = this
            axios.get('/points/spending/rewards/get/' + this.form.program.defaultRewardName).then((response) => {
              if (response.data.rewards.merchant_reward.length === 0) {

              } else {
                response.data.rewards.merchant_reward.forEach(function (merchant_reward, key) {
                  if (merchant_reward['id'] == _this.merchant_reward_id) {
                    let coupon_expiration                = merchant_reward.coupon_expiration_time.split(' ')
                    _this.form.reward_id                 = merchant_reward.id
                    _this.form.program.status            = merchant_reward.active_flag
                    _this.form.reward.points             = merchant_reward.points_required
                    _this.form.reward.maxShipping        = merchant_reward.max_shipping
                    _this.form.program.icon_name         = merchant_reward.reward_icon_name
                    _this.form.coupon.status             = merchant_reward.coupon_expiration
                    _this.form.coupon.prefix             = merchant_reward.coupon_prefix
                    _this.form.reward.minOrder           = merchant_reward.order_minimum
                    _this.form.program.nameDefault       = merchant_reward.rewardDefaultName
                    _this.form.program.rewardTextDefault = merchant_reward.rewardDefaultText
                    _this.form.program.emailTextDefault  = merchant_reward.reward_email_text
                    _this.form.emailNotification         = (merchant_reward.send_email_notification === '1')
                    _this.form.program.reward_icon       = merchant_reward.reward_icon
                    _this.form.coupon.limit.value        = coupon_expiration[0]
                    _this.form.coupon.limit.duration     = coupon_expiration[1]
                    _this.form.zapier.status             = merchant_reward.zap_status
                    _this.form.zapier.name               = merchant_reward.zap_key
                    _this.form.spending.limit.status     = merchant_reward.spending_limit
                    _this.form.spending.limit.value      = merchant_reward.spending_limit_value
                    _this.form.spending.limit.type       = merchant_reward.spending_limit_type
                    _this.form.spending.limit.period     = merchant_reward.spending_limit_period
                    _this.form.restrictions.status       = merchant_reward.restrictions_enabled

                    _this.getRestrictions(merchant_reward.id, 'reward')

                  }
                })
              }
              if (!this.form.iconPreview) {
                clearPreviewIcon(this.icon_default_class, this.icon_parent_el)
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
            axios.post('/points/spending/rewards/store', this.form).then((response) => {
              this.alert.dismissCountDown = this.alert.dismissSecs
              if (response.data.status == 404) {
                this.alert.type = 'danger'
                this.alert.text = response.data.message
              } else {
                this.setFormReward(response.data.merchant_reward.id);

                history.replaceState({}, '', '/points/spending/actions/free-shipping-discount/' + this.form.reward_id);

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
          toogleProgramStatus: function () {
            if (this.form.program.status == 0) {
              this.form.program.status = 1
            } else {
              this.form.program.status = 0
            }
          },
          toogleSpendingRestrictions: function () {
            if (this.form.restrictions.status == 0) {
              this.form.restrictions.status = 1
            } else {
              this.form.restrictions.status = 0
            }
          },
          toogleCouponLimit: function () {
            if (this.form.coupon.status == 0) {
              this.form.coupon.status = 1
            } else {
              this.form.coupon.status = 0
            }
          },
          toggleSpendingLimit: function () {
              if (this.form.spending.limit.status == 0) {
                  this.form.spending.limit.status = 1
              } else {
                  this.form.spending.limit.status = 0
              }
          },
          toggleZapierTrigger: function () {
            if (this.form.zapier.status == 0) {
                this.form.zapier.status = 1
            } else {
                this.form.zapier.status = 0
            }
          },
          cleariconImage: function (rewardId) {
            clearPreviewIcon('test', this.icon_parent_el)
            let $el = $('.upload-icon')
            resetForm($el)
            if (this.form.program.reward_icon) {
              axios.delete('/points/spending/rewards/icon/' + rewardId).then((response) => {
                this.form.program.icon = null
                this.form.program.icon_name = this.icon_default_class
                this.form.program.reward_icon = null
                this.form.iconPreview = ''
                showPreviewIcon(this.form.program.reward_icon, this.icon_default_class, this.icon_parent_el)
              }).catch((error) => {

              })
            } else {
              this.form.program.icon = null
              this.form.program.icon_name = this.icon_default_class
              this.form.program.action_icon = null
              this.form.iconPreview = ''
              showPreviewIcon(this.form.program.reward_icon, this.icon_default_class, this.icon_parent_el)
            }
          },
          iconImageChange: function (evt) {
            var $this = this
            var files = evt.target.files
            if (files.length != 0) {
              var f = files[0]
              this.form.program.icon_name = f.name
              var reader = new FileReader()
              this.form.program.reward_icon = ''

              reader.onload = (function (theFile) {
                return function (e) {
                  $this.form.iconPreview = e.target.result
                  clearPreviewIcon($this.icon_default_class, $this.icon_parent_el)
                  showPreviewIcon($this.form.iconPreview, $this.icon_default_class, $this.icon_parent_el)
                }

              })(f)

              reader.readAsDataURL(f)
            }
          },
          countDownChanged (dismissCountDown) {
            this.alert.dismissCountDown = dismissCountDown
          },
          rewardTextPreviewGen (get = false) {
            if (this.form.reward.points) {
              this.form.program.rewardText = this.form.program.rewardTextDefault
                .replace(/{points}/g, this.form.reward.points)
                .replace(/{points-name}/g, (this.form.reward.points > 1) ? this.merchantSettings.points.plural_name : this.merchantSettings.points.singular_name)
                .replace(/{company-name}/g, '{{$company}}')
                .replace(/{reward-name}/g, this.form.program.name)
                .replace(/{currency}/g, this.merchantSettings.currency.selectedFormat)
                .replace(/\&nbsp\;/g, ' ')
                .replace(/(<([^>]+)>)/ig, '')
              if (this.form.reward.minOrder && get) {
                this.form.program.rewardText += ' for orders over $' + this.form.reward.minOrder
              }
              return this.form.program.rewardText
            } else {
              return this.form.program.rewardText = this.form.program.rewardTextDefault
            }
          },
          emailTextPreviewGen () {
            const points_name = this.form.reward.points > 1 ? this.merchantSettings.points.plural_name : this.merchantSettings.points.singular_name
            if (this.form.reward.maxShipping) {
              if (this.form.program.emailTextDefault) {
                this.rewardEmailTextPreview = this.form.program.emailTextDefault
                  .replace(/{points}/g, this.form.reward.points)
                  .replace(/{points-name}/g, points_name ? points_name : '{points-name}')
                  .replace(/{company(-name)?}/g, '{{$company}}')
                  .replace(/{reward-name}/g, this.form.program.name)
                  .replace(/{ship-value}/g, this.form.reward.maxShipping ? (this.merchantSettings.currency.displaySign ? this.merchantSettings.currency.selectedFormat + '' + this.form.reward.maxShipping : this.form.reward.maxShipping + ' ' + this.merchantSettings.currency.selectedFormat) : '{ship-value}')
              }
            } else {
              if (this.form.program.emailTextDefault) {
                this.rewardEmailTextPreview = this.form.program.emailTextDefault
                  .replace(', up to {ship-value} shipping cost.', '.')
                  .replace(/{points}/g, this.form.reward.points)
                  .replace(/{points-name}/g, points_name ? points_name : '{points-name}')
                  .replace(/{company(-name)?}/g, '{{$company}}')
                  .replace(/{reward-name}/g, this.form.program.name)
              }
            }
          },
          emailTagClick: function (event) {
            let textarea = $('#emailDefaultText')

            const range = $('#emailDefaultText').trumbowyg('getRange')
            if (range === null) {
              return
            }
            let position = range.startOffset,
              html = range.startContainer.textContent.slice(0, position)
            position += html.length - html.replace(/(<([^>]+)>)/ig, '').length
            const text = range.startContainer.textContent.replace(/\&nbsp;/g, ' ')
            let txt = [
              text.slice(0, position),
              event.target.outerText,
              text.slice(position),
            ].join('')
            let parent = range.startContainer.parentNode
            if (parent.nodeName == 'P') {
              let rows = textarea.trumbowyg('html').replace(/\&nbsp;/g, ' ').split('</p>'),
                selectedIndex = 0
              while (parent.previousSibling) {
                parent = parent.previousSibling
                if (parent.nodeName == 'BR') {
                  continue
                }
                selectedIndex++
              }
              rows[selectedIndex] = '<p>' + txt
              textarea.trumbowyg('html', rows.join('</p>'))
            } else {
              textarea.trumbowyg('html', txt)
            }
            return
          },
          blurEmailField: function (event) {
            $('#emailDefaultText').trumbowyg('saveRange')
          },
          minOrderGen: function () {
            const minPhrase = 'for orders over '
            const name = this.form.program.nameDefault
            if (!this.form.reward.minOrder) {
              if (name.indexOf(minPhrase) != -1) {
                this.form.program.nameDefault = [name.split(minPhrase)[0],
                  name.substr(name.indexOf(minPhrase) + minPhrase.length)].join('')
              }
              this.wasMinGen = false
              return
            }
            if (this.wasMinGen || name.indexOf(minPhrase) != -1) {
              this.wasMinGen = true
              return
            }
            const minTag = this.reward_name_tags[2]
            const tagIndex = name.indexOf(minTag)
            if (tagIndex != -1) {
              this.form.program.nameDefault = [name.split(minTag)[0],
                minPhrase,
                minTag,
                name.split(minTag)[2]].join('')
              this.wasMinGen = true
            }
          },
          EditorCreated: function () {
            // $(".trumbowyg-viewHTML-button").attr('disabled', 'true');
            this.checkEditorPermissions($('.trumbowyg-viewHTML-button'))
          },
          checkEditorPermissions: function (elem = null) {
            if ('{{$has_editor_permissions}}' === '1') {
              return true
            } else {
              if (elem) {
                $(elem).addClass('trumbowyg-disable')
                $(elem).attr('disabled', 'true')
                $(elem).css('cursor', 'pointer')
                $(elem).wrap('<div id="blocked-editor" class="blocked-editor"></div>')
                $('#blocked-editor').click(e => {
                  swal({
                    className: 'upgrade-swal',
                    title: '{{$editor_upsell->upsell_title}}',
                    text: '{{$editor_upsell->upsell_text}}',
                    icon: 'images/permissions/{{$editor_upsell->upsell_image}}',
                    buttons: {
                      catch: {
                        text: 'Upgrade to {{$editor_upsell->getMinPlan()->name}}',
                        value: 'upgrade',
                      }
                    },
                  })
                    .then((value) => {
                      if (value == 'upgrade') {
                        window.location.href = '/account/upgrade'
                      }
                    })
                })
              }
              return false
            }
          },
        },
        computed: {
          rewardTextPreview: {
            get: function () {
              return this.rewardTextPreviewGen()
            },
            set: function (val) {
              //this.form.reward.points = val.split(' ')[0]
              //this.form.reward.minOrder = val.split('for orders over $')[1]

              const regex = new RegExp('\\s*(\\d+)\\s*' + this.this.merchantSettings.points.singular_name)
              this.form.reward.points = val.match(regex)[1]
              if (val.indexOf('orders over $') != -1) {
                this.form.reward.minOrder = val.split('orders over $')[1]
              }
            }
          },
          programNamePreview: {
            get: function () {
              let displaySign = this.merchantSettings.currency.displaySign;
              let sign = this.merchantSettings.currency.selectedFormat;
              this.minOrderGen()
              return this.form.program.name = this.form.program.nameDefault
                .replace(/{min-value}/g, this.form.reward.minOrder ? formatCurrency(sign, this.form.reward.minOrder, displaySign) : '')
                .replace(/{value}/g, this.form.reward.maxShipping ? this.form.reward.maxShipping : '{value}')
                .replace(/{currency}/g, this.form.reward.maxShipping ? sign : '')
                // .replace(/{min-value}/g, this.form.reward.minOrder ? `${this.merchantSettings.currency.displaySign ? this.merchantSettings.currency.selectedFormat + '' + this.form.reward.minOrder : this.form.reward.minOrder + ' ' + this.merchantSettings.currency.selectedFormat}` : '')
                .replace(/\&nbsp\;/g, ' ')
                .replace(/(<([^>]+)>)/ig, '')
            },
            set: function (val) {
            },
          },
          bodyPreview: {
            get: function () {
              const points_name = this.form.reward.value > 1 ? this.merchantSettings.points.plural_name : this.merchantSettings.points.singular_name
              let preview = `
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="img" style="padding: 0 10px; text-align:left;">
                            <div class="text-center m-b-20">
                                    <img src="<?php echo isset($company_logo) ? $company_logo : 'https://s3.amazonaws.com/lootly-logos/logo-email.png'; ?>" style="max-height: 50px;">
                            </div>
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
                                    <td class="text pb-30" style="padding-bottom: 39px; color:#2a2a2a; font-family:Arial,sans-serif; font-size:16px; line-height:26px; text-align:center;">
                                        You have <strong>{point-balance}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        {button}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <table  width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>
                            <div  class="border-top p-l-15 p-r-15">
                                <p class="text-center m-t-10">Powered by Lootly</p>
                            </div>
                            <hr/>
                        </td>
                    </tr>
                </table>
                `
              const button = '<table border="0" cellspacing="0" cellpadding="0" style="margin-bottom:40px"><tr><td class="text-button" style="padding: 14px 37px; border-radius: 5px; color:#ffffff; font-family:Arial,sans-serif; font-size:17px; line-height:21px; text-align:center; font-weight:bold;" bgcolor="#022c82"><a href="#" target="_blank" class="link-white" style="color:#ffffff; text-decoration:none;"><span class="link-white" style="color:#ffffff; text-decoration:none;">'
                + 'Earn More Points' + // btn text
                '</span></a></td></tr></table>'
              return preview
                .replace(/{company(-name)?}/g, '{{$company}}')
                .replace(/{customer}/g, 'Joe Smith')
                .replace(/{points-name}/g, points_name)
                .replace(/{coupon-code}/g, '123456abcdef')
                .replace(/{reward-icon}/g, '<img src="<?php echo url("/images/icons/email-notification/ico_dollar.jpg"); ?>" width="84" height="84" border="0" alt="" />')
                .replace(/{point-balance}/g, '200 ' + this.merchantSettings.points.plural_name)
                .replace(/{button}/g, button)
            }
          },
        },
        watch: {
          'form.reward.maxShipping': function () {
            var defaultName = 'Free Shipping'
          },
          'form.reward.minOrder': function () { this.minOrderGen()},
        }
      })
    </script>
@endsection
