@extends('layouts.app')

@section('title', 'Email Settings')

@section('content')
    <div id="email-settings" class="m-t-20 m-b-10 loader" v-cloak>
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
                <div class="col-md-12 m-b-15">
                    <a href="{{ route('display.email') }}" class="bold f-s-15 color-blue">
                        <i class="arrow left blue"></i>
                        <span class="m-l-5">Email Notifications</span>
                    </a>
                </div>
                <div class="col-md-6 col-12">
                    <h3 class="page-title m-t-0 color-dark">
                        Email Settings
                    </h3>
                </div>
                <div class="col-md-6 col-12">
                    <save-button class="text-right" :saving="saving" @event="saveSetting"></save-button>
                </div>
            </div>

            <div class="row p-t-25 p-b-25 section-border-bottom">
                <div class="col-md-5 col-12">
                    <h5 class="bolder m-b-15">General Settings</h5>
                </div>
                <div class="col-md-7 col-12">

                    <div class="well">
                        <div :class="{ 'loading' : loading }" v-cloak>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="bold m-b-5">
                                        From Name
                                    </label>
                                    <input type="text" class="form-control m-b-5" name="name" v-model="form.name"
                                           placeholder="{company}">
                                </div>
                            </div>
                            <div class="row m-t-10 m-b-10">
                                <div class="col-md-12">
                                    <label class="bold m-b-5">
                                        Reply to Email
                                    </label>
                                    <input type="email" class="form-control" name="replyEmail" v-model="form.replyEmail"
                                           placeholder="you@your-domain.com">
                                </div>
                            </div>

                            <div class="m-t-15 m-b-10">
                                <label class="bold m-b-0">
                                    Logo (recommended: 300px x 60px - will auto fit to size)
                                </label>
                                <div class="file-drag-drop w-100 m-t-10" v-cloak>
                                    <b-form-file class="upload-icon"
                                                 @change="iconChange"
                                                 name="new_icon"
                                                 accept="image/*">
                                    </b-form-file>

                                    <div class="custom-file-overlay">
                                    <span class="img" style="">
                                        <i class="icon-image-upload"
                                           v-if="!form.icon && !form.new_icon"></i>
                                        <img :src="form.icon" style="max-height:60px;max-width: 100%">
                                    </span>
                                        <h5 class="float f-s-17 bold">
                                        <span class="text"
                                              v-if="form.icon || form.new_icon"
                                              v-text="form.icon_name">
                                          </span>
                                            <span v-else>Drag files to upload</span>
                                        </h5>
                                        <i v-if="form.icon || form.new_icon"
                                           @click="clearIconImage"
                                           class="fa fa-times color-light-grey pointer"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row p-t-25 p-b-25">
                <div class="col-md-5 col-12">
                    <h5 class="bolder m-b-15">Advanced Branding</h5>
                    <p>Use your own custom domain as the from email, and also remove Lootly branding in your emails.</p>
                </div>
                <div class="col-md-7 col-12">
                    @if($has_remove_branding_permissions)
                        <div class="well">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group m-b-10">
                                        <label class="bold m-b-0">
                                            <div class="m-b-5">
                                                Email Branding
                                            </div>
                                        </label>
                                    </div>
                                    <b-form-checkbox class="w-100"
                                                    v-model="form.emailBranding"
                                                    name="emailBranding">
                                        Remove Lootly branding in email footer
                                    </b-form-checkbox>
                                </div>
                            </div>
                        </div>
                    @else
                        <no-access :loading="loading"
                            title="{{$branding_upsell->upsell_title}}"
                            desc="{{$branding_upsell->upsell_text}}"
                            icon="{{$branding_upsell->upsell_image}}"
                            plan="{{$branding_upsell->getMinPlan()->name}}"></no-access>
                    @endif

                    @if(!$have_domain_permissions)
                        <div class="m-t-15">
                            <no-access :loading="loading"
                                title="{{$domain_upsell->upsell_title}}"
                                desc="{{$domain_upsell->upsell_text}}"
                                icon="{{$domain_upsell->upsell_image}}"
                                plan="{{$domain_upsell->getMinPlan()->name}}"></no-access>
                        </div>
                    @else
                        <div class="well m-t-15">
                            <div :class="{ 'loading' : loading }" v-cloak>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="m-b-10">
                                            <label class="bold m-b-0">
                                                Custom Domain
                                            </label>
                                            <p class="m-b-0">
                                            To send emails from your email address, follow our 
                                            <a class="" href="">guide here</a>.
                                            </p>
                                        </div>
                                        <div class="row">
                                            <div class="col-9 p-r-0">
                                                <input type="text" class="form-control" name="customDomain"
                                                    v-model="form.customDomain"
                                                    placeholder="rewards@your-domain.com">
                                            </div>
                                            <div class="col-3">
                                                <button v-b-modal.custom-domain type="button" class="btn btn-primary btn-blue btn-block" style="height: 33.5px">Setup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                @if($has_remove_branding_permissions || $have_domain_permissions)
                    </div>
                @endif
                </div>
            </div>
        </form>

        <b-modal class="custom-modal" hide-footer id="custom-domain" title="Setup Custom Domain" v-cloak>
              <div id="">
                  <p>To complete the setup of your custom domain, you must add the following DNS records to ensure effective delivery.</p>

                  <div class="email-setup-section">
                      <label class="bold d-block m-b-5">DKIM</label>
                      <div class="email-setup-form-group">
                          <label>Hostname:</label>
                          <input value="1234abcd-domainkey" class="form-control">
                          <button type="button" class="btn btn-primary btn-copy" onclick="copyText(this)">Copy</button>
                      </div>
                      <div class="email-setup-form-group">
                          <label class="m-t-0">Type:</label> 
                          <span>TXT</span>
                      </div>
                      <div class="email-setup-form-group">
                          <label>Value:</label>
                          <textarea class="form-control">123456789longvalue-here</textarea>
                          <button type="button" class="btn btn-primary btn-copy" onclick="copyText(this)">Copy</button>
                      </div>
                  </div>
                  <div class="email-setup-section">
                      <label><b>Return-Path</b></label>
                      <div class="email-setup-form-group">
                          <label>Hostname:</label>
                          <input value="pm-bounces" class="form-control">
                          <button type="button" class="btn btn-primary btn-copy" onclick="copyText(this)">Copy</button>
                      </div>
                      <div class="email-setup-form-group">
                          <label class="m-t-0">Type:</label> 
                          <span>CNAME</span>
                      </div>
                      <div class="email-setup-form-group">
                          <label>Value:</label>
                          <input value="pm.mtasv.net" class="form-control">
                          <button type="button" class="btn btn-primary btn-copy" onclick="copyText(this)">Copy</button>
                      </div>
                  </div>
                  <div class="email-setup-section">
                      <label class="bold">
                          Confirm Email Address
                      </label>
                      <p class="m-b-5">
                          Our email provider Postmark, will send you an email shortly to confirm your domain to complete the setup process.
                      </p>
                  </div>
                  <p class="m-b-10">After you update your DNS records and confirm your email address, our system will begin the process of verifying everything.</p>
                  <p class="m-b-10">This is typically completed within 2 hours.</p>
                  <div class="text-center">
                      <button class="btn btn-success email-setup-finish">Finish</button>
                  </div>
              </div>
        </b-modal>
    </div>
@endsection

@section('scripts')
    <script>
      function copyText(el) {
        text = $(el).closest('.email-setup-form-group').find('.form-control');
        text.select();
        document.execCommand("copy");
      }
      
      var page = new Vue({
        el: '#email-settings',
        data: {
          form: {
            name: '',
            replyEmail: '',
            customDomain: '',
            emailBranding: 0,
            icon: '',
            new_icon: '',
            icon_name: '',
          },
          alert: {
            type: '',
            text: '',
            dismissSecs: 5,
            dismissCountDown: 0
          },
          loading: false,
          saving: false
        },
        created: function () {
          this.getData()
        },
        methods: {
          getData: function () {
            this.loading = true
            let that = this
            axios.get('/settings/display/email-notifications/settings').then((response) => {

              if (response.data.data) {
                let data = response.data.data
                that.form.emailBranding = !!data.remove_branding
                if (data.from_name) that.form.name = data.from_name
                if (data.reply_to_email) that.form.replyEmail = data.reply_to_email
                if (data.custom_domain) that.form.customDomain = data.custom_domain
                if (data.company_logo) that.form.icon = data.company_logo
                if (data.company_logo_name) that.form.icon_name = data.company_logo_name
              }
              that.loading = false

            }).catch((error) => {
              that.loading = false
              this.errors = error.response.data.errors
            })
          },
          saveSetting () {
            const that = this
            if (!that.saving) {
              that.saving = true

              let formData = JSON.parse(JSON.stringify(this.form))
              if (formData.new_icon.length) formData.icon = ''

              axios.post('/settings/display/email-notifications/settings', formData).then((response) => {
                that.saving = false

                this.alert.type = 'success'
                this.alert.text = 'Email settings saved successfully!'

                if (response.data.data) {
                  let data = response.data.data
                  that.form.emailBranding = !!data.remove_branding
                  that.form.name = data.from_name
                  that.form.replyEmail = data.reply_to_email
                  that.form.customDomain = data.custom_domain
                  that.form.icon = data.company_logo
                  that.form.icon_name = data.company_logo_name
                  that.form.new_icon = ''
                }
              }).catch((error) => {
                that.saving = false
                clearErrors(this.$el)
                console.log(error.response.data.errors)
                showErrors(this.$el, error.response.data.errors)

                this.alert.type = 'danger'
                this.alert.text = error.response.data.message
              }).then(() => {
                this.alert.dismissCountDown = this.alert.dismissSecs
              })
            }

          },
          iconChange: function (evt) {
            var $this = this
            var files = evt.target.files
            var f = files[0]
            //$this.form.new_icon = ''

            if (files.length != 0) {

              var reader = new FileReader()

              $this.form.icon_name = f.name
              $this.form.new_icon = ''

              reader.onload = (function (theFile) {
                return function (e) {
                  $this.form.new_icon = e.target.result
                  $this.form.icon = e.target.result
                }

              })(f)

              reader.readAsDataURL(f)
            }
          },
          clearIconImage: function () {
            this.form.icon = ''
            this.form.icon_name = ''
            this.form.new_icon = ''
          },
          countDownChanged (dismissCountDown) {
            this.alert.dismissCountDown = dismissCountDown
          },
        }
      })
    </script>
@endsection