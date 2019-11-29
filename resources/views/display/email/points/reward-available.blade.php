@extends('layouts.app')

@section('title', 'Reward Available')

@section('content')
    <div id="email-action" class="loader m-t-20 m-b-10" v-cloak>
        <b-alert v-cloak
                 :show="alert.dismissCountDown"
                 dismissible
                 :variant="alert.type"
                 @dismissed="alert.dismissCountdown=0"
                 @dismiss-count-down="countDownChanged">
            @{{alert.text}}
        </b-alert>

        <form id="">
            <div class="row m-t-20 p-b-10 section-border-bottom">
                <div class="col-md-12 m-b-10">
                    <a href="{{ route('display.email') }}" class="bold f-s-15 color-blue">
                        <i class="arrow left blue"></i>
                        <span class="m-l-5">Email Notifications</span>
                    </a>
                </div>
                <div class="col-md-6 col-12">
                    <h3 class="page-title m-t-5 color-dark">
                        Reward Available
                    </h3>
                </div>
                <div class="col-md-6 col-12 text-right ">
                    <div class="m-t-5">
                        <button type="button" class="btn btn-test m-w-90" id="sendTestPopover">Send Test</button>
                        <b-popover triggers="focus"
                                   placement="bottom"
                                   ref="popover"
                                   target="sendTestPopover"
                                   class="custom-popover"
                                   title="Send To:">

                            <div class="send-test-popover" style="">
                                <input id="testEmail" class="form-control" placeholder="Your Email" v-model="email">
                                <button style="" class="btn btn-test" @click="sendTest">
                                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                </button>
                            </div>

                        </b-popover>
                        <save-button class="inline-block" :saving="saving" @event="saveSetting"></save-button>
                    </div>
                </div>
            </div>

            <div class="row m-t-25 m-b-40">
                <div class="col-md-7 col-12">
                    @if(!$merchant->checkPermitionByTypeCode('EmailCustomization'))
                        <no-access :loading="loading"
                                   title="{{$feature->upsell_title}}"
                                   desc="{{$feature->upsell_text}}"
                                   icon="{{$feature->upsell_image}}"
                                   plan="{{$feature->getMinPlan()->name}}"></no-access>

                    @else
                        <div class="well bg-white">
                            <div class="loading-spinner" :class="{ 'loading' : loading || saving }" v-cloak>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="bold m-b-5">
                                            Subject Line
                                        </label>
                                        <input type="text" class="form-control m-b-5" name="subjectLine"
                                               v-model="form.subjectLine"
                                               placeholder="Subject Line">
                                    </div>
                                </div>
                                <div class="row m-t-10">
                                    <div class="col-md-12">
                                        <label class="bold">
                                            Body
                                        </label>
                                        <trumbowyg
                                                v-model="form.body"
                                                :config="config"
                                                name="body"
                                                @tbw-init="EditorCreated"
                                                class="editor vip-tier-email-text"></trumbowyg>
                                    </div>
                                </div>
                                <div class="row m-t-15 p-b-10 section-border-bottom">
                                    <div class="col-md-12">
                                        <span class="custom-tag" v-for="tag in tags">
                                            <span v-text="tag"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="light-font m-b-10">
                                                <span>Button text</span>
                                            </label>
                                            <input type="text" class="form-control" name="button.text"
                                                   placeholder="Button text"
                                                   v-model="form.button.text">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group m-b-0">
                                            <label class="light-font m-b-10">
                                                <span>Button Color</span>
                                            </label>
                                            <colorpicker :color="form.button.color" name="button.color"
                                                         v-model="form.button.color"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="well m-t-20">
                            <div class="loading-spinner" :class="{ 'loading' : loading || saving }" v-cloak>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group m-b-0">
                                            <label class="light-font m-b-0 m-t-5">
                                                Reward Available notification is currently
                                                <span class="bold" v-text="form.status ? 'Enabled' : 'Disabled'"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <a @click="form.status = form.status ? 0 : 1" v-cloak>
                                              <span v-if="form.status == 1" class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
                                           <span v-else class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-md-5 col-12">
                    <div class="sticky-top">
                        <div class="well bg-white p-t-15 p-b-15 p-l-0 p-r-0">
                            <div class="border-bottom p-l-15 p-r-15 m-b-10">
                                <h5 class="bold m-b-15 f-s-16">
                                    Preview
                                </h5>
                            </div>
                            <div id="preview-block" class="p-t-15 p-b-15 p-l-25 p-r-25">
                                <div class="loading-spinner" :class="{ 'loading' : loading || saving }" v-cloak>
                                    <div class="text-center m-b-20">
                                        <img :src="merchantSettings.logo" style="max-height: 60px;">
                                    </div>

                                    <div v-html="bodyPreview"></div>
                                </div>
                            </div>
                            <div v-if="!merchantSettings.remove_branding" class="border-top p-l-15 p-r-15">
                                <p class="text-center m-t-10">Powered by Lootly</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')

    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trumbowyg@2.10.0/dist/ui/trumbowyg.min.css">
    <link rel="stylesheet" type="text/css"
          href="https://unpkg.com/trumbowyg@2.10.0/dist/plugins/colors/ui/trumbowyg.colors.min.css">
    <script src="https://unpkg.com/trumbowyg@2.10.0/dist/trumbowyg.min.js"></script>
    <script src="https://unpkg.com/vue-trumbowyg@3.3.0/dist/vue-trumbowyg.min.js"></script>
    <script src="https://unpkg.com/trumbowyg@2.10.0/dist/plugins/fontsize/trumbowyg.fontsize.min.js"></script>
    <script src="https://unpkg.com/trumbowyg@2.10.0/dist/plugins/colors/trumbowyg.colors.min.js"></script>
    <script src="https://unpkg.com/trumbowyg@2.10.0/dist/plugins/lineheight/trumbowyg.lineheight.min.js"></script>

    <script>
      Vue.component('Trumbowyg', VueTrumbowyg.default)
      var page = new Vue({
        el: '#email-action',
        data: {
          merchantId: Spark.state.currentTeam.id,
          merchantSettings: {
            name: 'TestStore',
            logo: '',
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
            }
          },
          form: {
            status: 1,
            subjectLine: 'Get your {reward-name} reward now at {company-name}',
            body: `<?php echo File::get(storage_path()."/email-notification/points_reward_available-editable.html"); ?>`,
            button: {
              text: 'Get my Coupon',
              color: '#022c82'
            },
          },
          tags: ['{customer}', '{reward-name}', '{company-name}', '{point-balance}', '{reward-icon}', '{button}'],
          icons: ['reward-icon'],
          config: {
            svgPath: '/fonts/icons/trumbowyg-icons.svg',
            btns: [['fontsize', 'foreColor', 'bold', 'italic'], ['lineheight', 'horizontalRule', 'link'], ['justifyLeft', 'justifyCenter', 'justifyRight'], ['viewHTML']]
          },
          email: '',
          alert: {
            type: '',
            text: '',
            dismissSecs: 5,
            dismissCountDown: 0
          },
          saving: false,
          loading: false,
          sendign: false
        },
        created: function () {
          this.getData()
          this.getMercahntSettings()
        },
        methods: {
          getMercahntSettings: function () {
            axios.get('/api/merchants/' + this.merchantId + '/settings/common').then(response => {
              if (response.data && response.data.data) {
                let merchantSettings = response.data.data
                // Get merchant settings
                this.merchantSettings.name = merchantSettings.name
                this.merchantSettings.logo = merchantSettings.email_notification_settings.data.company_logo ? merchantSettings.email_notification_settings.data.company_logo : '{{ config('email-notification.default_logo') }}';

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
                // Get points setting
                if (merchantSettings.points_settings && merchantSettings.points_settings.data) {
                  this.merchantSettings.points.singular_name = merchantSettings.points_settings.data.name
                  this.merchantSettings.points.plural_name = merchantSettings.points_settings.data.plural_name
                }
              }
            }).catch((errors) => {
              console.log(errors)
            })
          },
          getData: function () {
            let comp = this
            comp.loading = true
            axios.get('/settings/display/email-notifications/points/reward-available').then((response) => {
              if (response.data.data) {
                let notification_settings = response.data.data
                comp.form.status = notification_settings.status
                if (notification_settings.subject) comp.form.subjectLine = notification_settings.subject
                if (notification_settings.body) comp.form.body = notification_settings.body
                if (notification_settings.button_text) comp.form.button.text = notification_settings.button_text
                if (notification_settings.button_color) comp.form.button.color = notification_settings.button_color
              }
              setTimeout(function(){
                comp.loading = false
              }, 700)
            }).catch((error) => {
              comp.loading = false
              this.errors = error.response.data.errors
            })
          },
          saveSetting () {
            const comp = this
            if (!comp.saving) {
              comp.saving = true
              let formData = comp.form
              formData.icons = {}
              for (let i = 0; i < this.icons.length; i++) {
                let findIcon = document.querySelector('#preview-block [data-name="' + this.icons[i] + '"]')
                if (findIcon) {
                  formData.icons[this.icons[i]] = window.getComputedStyle(findIcon).color
                }
              }
              axios.post('/settings/display/email-notifications/points/reward-available', formData).then((response) => {
                comp.saving = false
                comp.alert.dismissCountDown = comp.alert.dismissSecs
                comp.alert.type = 'success'
                comp.alert.text = 'Action saved successfully!'

                if (response.data.data) {
                  let data = response.data.data
                  comp.form.status = data.status
                  comp.form.subjectLine = data.subject
                  comp.form.body = data.body
                  comp.form.button.text = data.button_text
                  comp.form.button.color = data.button_color
                }
              }).catch((error) => {
                comp.saving = false
                clearErrors(comp.$el)
                console.log(error.response.data.errors)
                showErrors(comp.$el, error.response.data.errors)
                comp.alert.dismissCountDown = comp.alert.dismissSecs
                comp.alert.type = 'danger'
                comp.alert.text = error.response.data.message
              })
            }
          },
          sendTest () {
            if (this.email == '') {
              document.getElementById('testEmail').classList.add('border-red')
              return false
            }
            this.$refs.popover.$emit('close')

            if (!this.sending) {
              this.sending = true
              axios.post('/api/merchants/' + this.merchantId + '/email-notifications/points/reward-available/send-test-email', {
                'to_email': this.email
              }).then((response) => {
                this.alert.text = 'Test Mail has been sent successfully!'
                this.alert.type = 'success'
                console.log(response)
              }).catch((error) => {
                console.log(error)
                this.alert.text = error.response.data.message
                this.alert.type = 'danger'
              }).then(() => {
                this.sending = false
                this.alert.dismissCountDown = this.alert.dismissSecs
              })
            }

          },
          countDownChanged (dismissCountDown) {
            this.alert.dismissCountDown = dismissCountDown
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
                    icon: '/images/permissions/{{$editor_upsell->upsell_image}}',
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
          }
        },
        computed: {
          bodyPreview () {
            var button = '<table border="0" cellspacing="0" cellpadding="0"><tr><td class="text-button" style="padding: 14px 37px; border-radius: 5px; color:#ffffff; font-family:Arial,sans-serif; font-size:17px; line-height:21px; text-align:center; font-weight:bold;" bgcolor="' + this.form.button.color + '"><a href="#" target="_blank" class="link-white" style="color:#ffffff; text-decoration:none;"><span class="link-white" style="color:#ffffff; text-decoration:none;">' + this.form.button.text + '</span></a></td></tr></table>'
            var previewText = this.form.body.toString()
              .replace(/{customer}/g, 'Joe Smith')
              .replace(/{reward-name}/g, '$10 off')
              .replace(/{company-name}/g, this.merchantSettings.name)
              .replace(/{reward-icon}/g, '<i data-name="reward-icon" style="font-size: 84px;color: inherit;" class="icon icon-coin"></i>')
              .replace(/{point-balance}/g, '1000')
              .replace(/{button}/g, button)
            return previewText
          }
        },
        watch: {
          email: function () {
            document.getElementById('testEmail').classList.remove('border-red')
          }
        }
      })
    </script>
@endsection