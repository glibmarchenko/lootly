@extends('layouts.app')
@section('title', 'Widget Branding')
@section('content')
    <div id="branding-page" class="loader m-t-20 m-b-10" v-cloak>
        <div>
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
                        <a href="{{ route('display.widget') }}" class="bold f-s-15 color-blue">
                            <i class="arrow left blue"></i>
                            <span class="m-l-5">Widget Overview</span>
                        </a>
                    </div>
                    <div class="col-md-12">
                        <h3 class="page-title m-t-0 color-dark pull-left">
                            Widget Branding
                        </h3>
                        <save-button class="pull-right" :saving="saving" @event="saveAction"></save-button>
                    </div>
                </div>
                <div class="row p-t-25 p-b-25">
                    <div class="col-md-7 col-12">
                        <div class="well">
                            <div class="branding-section" :class="{ 'loading' : loading }" v-cloak>
                                <label class="bolder f-s-15 m-b-15 m-t-5">
                                    Brand Colors
                                </label>
                                <div class="row first">
                                    <div class="col-md-6">
                                        <div class="m-b-0">
                                            <label class="light-font m-b-10">
                                                <span>Primary Color</span>
                                            </label>
                                            <colorpicker :color="form.primaryColor" v-model="form.primaryColor"
                                                         name="primaryColor"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="m-b-0">
                                            <label class="light-font m-b-10">
                                                <span>Secondary Color</span>
                                            </label>
                                            <colorpicker :color="form.secondaryColor" v-model="form.secondaryColor"
                                                         name="secondaryColor"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="m-b-0">
                                            <label class="light-font m-b-10">
                                                <span>Header Background Color</span> 
                                            </label>
                                            <select-color-picker :color="form.headerBackground" v-model="form.headerBackground" name="headerBackground"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="m-b-0">
                                            <label class="light-font m-b-10">
                                                <span>Header Background Font Color</span>
                                            </label>
                                            <select-color-picker :options="selectFontsOptions" :color="form.headerBackgroundFontColor" v-model="form.headerBackgroundFontColor" name="headerBackgroundFontColor"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="m-b-0">
                                            <label class="light-font m-b-10">
                                                <span>Button Color</span>
                                            </label>
                                            <select-color-picker :color="form.buttonColor" v-model="form.buttonColor" name="buttonColor"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="m-b-0">
                                            <label class="light-font m-b-10">
                                                <span>Button Font Color</span>
                                            </label>
                                            <select-color-picker :options="selectFontsOptions" :color="form.buttonFontColor" v-model="form.buttonFontColor" name="buttonFontColor"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="m-b-0">
                                            <label class="light-font m-b-10">
                                                <span>Link Color</span>
                                            </label>
                                            <select-color-picker :color="form.linkColor" v-model="form.linkColor" name="linkColor"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="m-b-0">
                                            <label class="light-font m-b-10">
                                                <span>Tab Color</span> 
                                            </label>
                                            <select-color-picker :color="form.tabColor" v-model="form.tabColor" name="tabColor"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="m-b-0">
                                            <label class="light-font m-b-10">
                                                <span>Tab Font Color</span>
                                            </label>
                                            <select-color-picker :options="selectFontsOptions" :color="form.tabFontColor" v-model="form.tabFontColor" name="tabFontColor"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="well m-t-20">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class=" m-b-0">
                                        <label class="light-font m-b-5">
                                            <span>Font</span>
                                        </label>
                                        <b-form-select v-model="form.font" :options="fonts"
                                                        name="font"></b-form-select>
                                        <p class="d-block m-t-10">Need a font not shown? Let us know 
                                            <a class="bold light-link" v-b-modal.suggestion-modal>here</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($merchant->plan() && $merchant->plan()->growth_order >= 1)
                            <div class="well m-t-20">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block bold m-b-10">
                                                <span>CSS Editor (advanced)</span>
                                            </label>
                                            <textarea class="form-control"
                                                      v-model="form.customCSS" 
                                                      placeholder="Custom CSS styles" 
                                                      style="min-height: 150px;"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>                        
                        @else
                            <no-access :loading="loading"
                                class="m-t-15"
                                title="Advanced CSS Editor" 
                                desc="The CSS Editor allows for deeper customization into the design aspect of the Lootly widget, including: Block ordering and hiding/removing blocks." 
                                icon="design.png" 
                                plan="Growth"></no-access>                        
                        @endif

                        @if($has_remove_branding_permissions)
                            <div class="well m-t-20">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block bold m-b-10">
                                                <span>Widget Branding</span>
                                            </label>
                                            <b-form-checkbox v-model="form.hideLootlyLogo" name="hideLootlyLogo">
                                                Remove Lootly branding in widget footer
                                            </b-form-checkbox>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="m-t-15">
                                <no-access :loading="loading"
                                    title="{{$branding_upsell->upsell_title}}"
                                    desc="{{$branding_upsell->upsell_text}}"
                                    icon="{{$branding_upsell->upsell_image}}"
                                    plan="{{$branding_upsell->getMinPlan()->name}}"></no-access>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-5 col-12">
                        <div class="sticky-top">
                            <div class="well">
                                <div :class="{ 'loading' : loading || saving }" style="max-width: 360px;margin: auto;" v-cloak>
                                    <h5 class="bold m-b-20">Widget Preview</h5>
                                    <widget-preview :company="storeName" :welcome="welcome" :branding="form" style="border-radius: 10px; overflow: hidden;box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.12);padding: 0"></widget-preview>
                                    <button id="widgetBtn" 
                                            type="button" 
                                            class="btn icon-only"
                                            style="margin-left: auto;display: block;margin-top: 30px;" 
                                            v-bind:style="{ background: form.tabColor, color: form.tabFontColor}">
                                        <div class="widget-close-btn">×</div>
                                    </button>
                                </div>
                                <div id="widgetStyles"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Suggestion Modal -->
        <b-modal id="suggestion-modal" class="custom-modal" title="Widget Font Request" hide-footer v-cloak>
            <div class="row m-b-10 m-t-10">
                <div class="col-md-12">
                    <p class="light-font">Need a font not shown? Let our team know and we will follow up within 24 hours.</p>
                </div>
            </div>
            <div class="row m-b-10">
                <div class="col-md-12">
                    <label class="light-font m-b-5">Font Name</label>
                    <input class="form-control" placeholder="Font Name">
                </div>
            </div>
            <div class="row m-t-20 p-b-10 p-t-20 border-top">
                <div class="col-md-6 offset-md-3">
                    <a @click="submitSuggestion" class="btn modal-btn-lg btn-block btn-success btn-glow">
                        Submit
                    </a>
                </div>
            </div>
        </b-modal>
    </div>
@endsection

@section('scripts')
    <link rel="stylesheet" type="text/css" href="{{ mix('css/_widget.css') }}">

    <script>
      var page = new Vue({
        el: '#branding-page',
        data: {
          storeName: Spark.state.currentTeam.name,
          form: {
            primaryColor: '#2b69d1',
            secondaryColor: '#3d3d3d',
            headerBackground: '#2b69d1',
            headerBackgroundFontColor: '#FFFFFF',
            buttonColor: '#2b69d1',
            buttonFontColor: '#FFFFFF',
            linkColor: '#2b69d1',
            tabColor: '#2b69d1',
            tabFontColor: '#FFFFFF',
            font: 'lato',
            customCSS: '',
            hideLootlyLogo: false
          },
          fonts: [
            {value: 'lato', text: 'Lato'},
            {value: 'proxima-nova', text: 'Proxima-Nova'},
            {value: 'Montserrat', text: 'Montserrat'},
            {value: 'arial', text: 'Arial'},
            {value: 'JUNGLEFE', text: 'Jungle Fever'},
          ],
          selectFontsOptions: [
            {label: 'White', value: '#FFFFFF'},
            {label: 'Black', value: '#000000'},
          ],      
          welcome: {
            header: {
              title: 'Welcome to',
              subtitle: '{company}'
            },
            title: 'Join our Rewards Program',
            subtitle: 'Access existing perks, savings and rewards just by shopping with us!',
            buttonText: 'Create an Account',
            login: 'Already have an account?',
            pointsRewardsTitle: 'Points & Rewards',
            pointsRewardsSubtitle: 'Earn points for completing actions, and turn your points into rewards.',
            pointsRewardsEarningTitle: 'Ways to earn',
            pointsRewardsSpendingTitle: 'Ways to spend',
            vipTitle: 'VIP Tiers',
            vipSubtitle: 'Gain access to exclusive rewards. Reach higher tiers for more exlucisve perks.',
            referralTitle: 'Referrals',
            referralSubtitle: 'Tell your friends about us and earn rewards',
            position: 'center',
            // Background Image
            background: '',
            new_background: '',
            background_name: '',
            background_opacity: '100%'
          },
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
          this.getSettings()
        },
        methods: {
          getSettings: function () {
            this.loading = true
            let that = this
            axios.get('/display/widget/widget/get').then((response) => {

              if (response.data.widget_settings) {
                let widget_settings = response.data.widget_settings;
                if (widget_settings.brand_primary_color) that.form.primaryColor = widget_settings.brand_primary_color
                if (widget_settings.brand_secondary_color) that.form.secondaryColor = widget_settings.brand_secondary_color
                if (widget_settings.brand_header_bg) that.form.headerBackground = widget_settings.brand_header_bg;
                if (widget_settings.brand_header_bg_font_color ) that.form.headerBackgroundFontColor = widget_settings.brand_header_bg_font_color;
                if (widget_settings.brand_button_color) that.form.buttonColor = widget_settings.brand_button_color;
                if (widget_settings.brand_button_font_color) that.form.buttonFontColor = widget_settings.brand_button_font_color;
                if (widget_settings.tab_bg_color) that.form.tabColor = widget_settings.tab_bg_color;
                if (widget_settings.tab_font_color) that.form.tabFontColor = widget_settings.tab_font_color;
                if (widget_settings.brand_button_font_color) that.form.buttonFontColor = widget_settings.brand_button_font_color;
                if (widget_settings.brand_link_color) that.form.linkColor = widget_settings.brand_link_color
                if (widget_settings.brand_font) that.form.font = widget_settings.brand_font
                if (widget_settings.custom_css) that.form.customCSS = widget_settings.custom_css
                that.form.hideLootlyLogo = !!widget_settings.brand_remove_in_widget

                // Get Not Login Settings
                if (widget_settings.widget_welcome_header_title) that.welcome.header.title = widget_settings.widget_welcome_header_title;
                if (widget_settings.widget_welcome_header_subtitle) that.welcome.header.subtitle = widget_settings.widget_welcome_header_subtitle;
                if (widget_settings.widget_welcome_title) that.welcome.title = widget_settings.widget_welcome_title;
                if (widget_settings.widget_welcome_subtitle) that.welcome.subtitle = widget_settings.widget_welcome_subtitle;
                if (widget_settings.widget_welcome_button_text) that.welcome.buttonText = widget_settings.widget_welcome_button_text;
                if (widget_settings.widget_welcome_login) that.welcome.login = widget_settings.widget_welcome_login;

                if (widget_settings.widget_welcome_points_rewards_title) that.welcome.pointsRewardsTitle = widget_settings.widget_welcome_points_rewards_title
                if (widget_settings.widget_welcome_points_rewards_subtitle) that.welcome.pointsRewardsSubtitle = widget_settings.widget_welcome_points_rewards_subtitle

                if (widget_settings.widget_welcome_points_rewards_earning_title) that.welcome.pointsRewardsEarningTitle = widget_settings.widget_welcome_points_rewards_earning_title
                if (widget_settings.widget_welcome_points_rewards_spending_title) that.welcome.pointsRewardsSpendingTitle = widget_settings.widget_welcome_points_rewards_spending_title

                if (widget_settings.widget_welcome_vip_title) that.welcome.vipTitle = widget_settings.widget_welcome_vip_title
                if (widget_settings.widget_welcome_vip_subtitle) that.welcome.vipSubtitle = widget_settings.widget_welcome_vip_subtitle
                if (widget_settings.widget_welcome_referral_title) that.welcome.referralTitle = widget_settings.widget_welcome_referral_title
                if (widget_settings.widget_welcome_referral_subtitle) that.welcome.referralSubtitle = widget_settings.widget_welcome_referral_subtitle

                if (widget_settings.widget_welcome_position) that.welcome.position = widget_settings.widget_welcome_position
                if (widget_settings.widget_welcome_background) that.welcome.background = widget_settings.widget_welcome_background
                if (widget_settings.widget_welcome_background_name) that.welcome.background_name = widget_settings.widget_welcome_background_name
                if (widget_settings.widget_welcome_background_opacity) that.welcome.background_opacity = widget_settings.widget_welcome_background_opacity
              }

              that.loading = false

            }).catch((error) => {
              that.loading = false
              this.errors = error.response.data.errors
            })
          },
          saveAction: function () {
            const that = this
            if (!that.saving) {
              that.saving = true
              axios.post('/display/widget/branding/store', this.form).then((response) => {
                that.saving = false
                this.alert.dismissCountDown = this.alert.dismissSecs
                if (response.data.status == 404) {
                  this.alert.type = 'danger'
                  this.alert.text = response.data.message
                } else {
                  this.alert.type = 'success'
                  this.alert.text = response.data.message
                  if (response.data.widget_settings) {
                    let widget_settings = response.data.widget_settings
                    that.form.primaryColor = widget_settings.brand_primary_color
                    that.form.secondaryColor = widget_settings.brand_secondary_color

                    that.form.headerBackground = widget_settings.brand_header_bg
                    that.form.headerBackgroundFontColor = widget_settings.brand_header_bg_font_color
                    that.form.buttonColor = widget_settings.brand_button_color
                    that.form.buttonFontColor = widget_settings.brand_button_font_color
                    that.form.tabColor = widget_settings.tab_bg_color
                    that.form.tabFontColor = widget_settings.tab_font_color

                    that.form.linkColor = widget_settings.brand_link_color
                    that.form.font = widget_settings.brand_font
                    that.form.customCSS = widget_settings.custom_css
                    that.form.hideLootlyLogo = !!widget_settings.brand_remove_in_widget
                  }
                }
              }).catch((error) => {
                that.saving = false
                clearErrors(this.$el)
                console.log(error.response.data.errors)
                showErrors(this.$el, error.response.data.errors)
                this.alert.dismissCountDown = this.alert.dismissSecs
                this.alert.type = 'danger'
                this.alert.text = error.response.data.message
              })
            }
          },
          submitSuggestion () {
            this.$root.$emit('bv::hide::modal', 'suggestion-modal');
                swal({
                    className: "upgrade-swal",
                    icon: "success",
                    text: 'Your request has been submitted. \n The Lootly team will follow up shortly.',
                });
          },
          countDownChanged (dismissCountDown) {
            this.alert.dismissCountDown = dismissCountDown
          }
        },
        watch: {
            'form.customCSS': function() {
                document.getElementById('widgetStyles').innerHTML = '<style>' + prefixCssSelectors(this.form.customCSS, '.lootly-widget') + '</style>'
            }
        }
      })

    </script>
@endsection

