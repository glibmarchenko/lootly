@extends('layouts.app')

@section('title', 'Referrals Sharing')

@section('content')
    <div id="sharing-page" class="p-b-40 p-t-20 loader" :class="{'loading': loading}" v-cloak>
        <b-alert :show="alert.dismissCountDown"
                 dismissible
                 :variant="alert.type"
                 @dismissed="alert.dismissCountdown=0"
                 @dismiss-count-down="countDownChanged">
            @{{alert.text}}
        </b-alert>
        <div class="row p-b-10 section-border-bottom">
            <div class="col-md-6 col-12">
                <h3 class="page-title pull-left m-t-0 color-dark">Referrals Sharing</h3>
            </div>
            <div class="col-md-6 col-12 text-right">
                <span v-if="saving" class="i-loading"></span>
                <button v-show="!saving" class="btn btn-save" @click.prevent="saveSetting">Save</button>
            </div>
        </div>

        <div class="row m-t-25">
            <div class="col-md-7">
                <div class="well bg-white">
                    <div class="row" v-bind:class="[form.facebook.status == 1 ? 'section-border-bottom p-b-15': '']">
                        <div class="col-md-8">
                            <div class="form-group m-b-0">
                                <i class="icon-facebook v-a-t m-t-5 m-r-10" style="font-size: 32px;"></i>
                                <label class="bold inline-block m-b-0">
                                    <span class="f-s-15">Facebook</span>
                                    <p class="light-font m-b-0">
                                        Facebook sharing is
                                        <span class="bold"
                                              v-text="form.facebook.status == 0 ? 'Disabled' : 'Enabled'"></span>
                                    </p>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <a @click="toogleProgramStatus('facebook')" v-cloak>
						<span v-if="form.facebook.status == 0">
							<span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
						</span>
                                <span v-else>
							<span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
						</span>
                            </a>
                        </div>
                    </div>
                    <div class="row m-t-15" v-if="form.facebook.status == 1">
                        <div class="col-md-12">
                            <div class="form-group m-b-0">
                                <label class="light-font m-b-10 w-100">
                                    Facebook message
                                    <span class="pull-right">
	                    		</span>
                                </label>
                                <input type="text" placeholder="Facebook message" class="form-control m-b-5"
                                       v-model="form.facebook.message">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="well bg-white m-t-20">
                    <div class="row" v-bind:class="[form.twitter.status == 1 ? 'section-border-bottom p-b-15': '']">
                        <div class="col-md-8">
                            <div class="form-group m-b-0">
                                <i class="icon-twitter v-a-t m-t-5 m-r-10" style="font-size: 32px;"></i>
                                <label class="bold inline-block m-b-0">
                                    <span class="f-s-15">Twitter</span>
                                    <p class="light-font m-b-0">
                                        Twitter sharing is
                                        <span class="bold"
                                              v-text="form.twitter.status == 0 ? 'Disabled' : 'Enabled'"></span>
                                    </p>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <a @click="toogleProgramStatus('twitter')" v-cloak>
						<span v-if="form.twitter.status == 0">
							<span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
						</span>
                                <span v-else>
							<span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
						</span>
                            </a>
                        </div>
                    </div>
                    <div class="row m-t-15" v-if="form.twitter.status == 1">
                        <div class="col-md-12">
                            <div class="form-group m-b-0">
                                <label class="light-font m-b-10 w-100">
                                    Tweet message
                                    <span class="pull-right">
	                    			<span v-text="tweetMsgLength"></span>/280
	                    		</span>
                                </label>
                                <input type="text" placeholder="Tweet message" class="form-control m-b-5"
                                       v-model="form.twitter.message">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="well bg-white m-t-20">
                    <div class="row" v-bind:class="[form.google.status == 1 ? 'section-border-bottom p-b-15': '']">
                        <div class="col-md-8">
                            <div class="form-group m-b-0">
                                <i class="fa fa-google bordered-icon v-a-t m-t-5 m-r-10"></i>
                                <label class="bold inline-block m-b-0">
                                    <span class="f-s-15">Google</span>
                                    <p class="light-font m-b-0">
                                        Google+ sharing is
                                        <span class="bold" v-text="form.google.status == 0 ? 'Disabled' : 'Enabled'"></span>
                                    </p>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <a @click="toogleProgramStatus('google')" v-cloak>
                            <span v-if="form.google.status == 0">
                                <span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
                            </span>
                                <span v-else>
                                <span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
                            </span>
                            </a>
                        </div>
                    </div>
                    <div class="row m-t-15" v-if="form.google.status == 1">
                        <div class="col-md-12">
                            <div class="form-group m-b-0">
                                <label class="light-font m-b-10 w-100">
                                    Google+ message
                                </label>
                                <input type="text" placeholder="Google message" class="form-control m-b-5" v-model="form.google.message">
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- Email Patterns Section -->
                <div class="well bg-white m-t-20">
                    <div class="row" v-bind:class="[form.email.status == 1 ? 'section-border-bottom p-b-15': '']">
                        <div class="col-md-8">
                            <div class="form-group m-b-0">
                                <i class="fa fa-envelope-o bordered-icon bold v-a-t m-t-5 m-r-10"></i>
                                <label class="bold inline-block m-b-0">
                                    <span class="f-s-15">Email</span>
                                    <p class="light-font m-b-0">
                                        Email sharing is
                                        <span class="bold"
                                              v-text="form.email.status == 0 ? 'Disabled' : 'Enabled'"></span>
                                    </p>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <a @click="toogleProgramStatus('email')" v-cloak>
						<span v-if="form.email.status == 0">
							<span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
						</span>
                                <span v-else>
							<span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
						</span>
                            </a>
                        </div>
                    </div>
                    <div class="row m-t-15" v-if="form.email.status == 1">
                        <div class="col-md-12">
                            <div class="form-group m-b-15">
                                <label class="light-font m-b-10 w-100">
                                    Subject line
                                </label>
                                <input type="text" placeholder="Subject line" class="form-control"
                                       v-model="form.email.subject">
                            </div>
                            <div class="form-group m-b-5">
                                <label class="light-font m-b-10 w-100">
                                    Email body
                                </label>
                                <textarea style="height: 100px;" placeholder="Email body" class="form-control"
                                          v-model="form.email.body"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="well bg-white m-t-20">
                    <div class="row section-border-bottom p-b-10 m-b-15">
                        <div class="col-md-12">
                            <div class="form-group m-b-0">
                                <label class="bolder f-s-15 m-b-0 m-t-0">
                                    Design
                                </label>
                                <a class="bolder f-s-14 color-blue pull-right" @click="previewShare">Preview share</a>
                            </div>
                            <p class="m-t-10">
                                When you share on a social platform, your website's meta image and description are
                                automatically pulled. Customize these items below instead.
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group m-b-0">
                                <div>
                                    <label class="light-font m-b-5">
                                        Title
                                    </label>
                                    <input class="form-control"
                                           placeholder="e.g. {reward-name} Off your next purchase at {company}"
                                           v-model="form.title">
                                </div>
                                <div>
                                    <label class="light-font m-t-15 m-b-5">
                                        Description
                                    </label>
                                    <input class="form-control"
                                           placeholder="e.g. {company} is the largest site for high quality products, check us out today."
                                           v-model="form.description">
                                </div>
                                <!-- Faceboock Image Section -->
                                <span v-if="form.facebook.status == 1">
	                            <div class="light-font m-t-15 m-b-5">
	                                <p>
	                                	Facebook Image 
	                                	<span class="f-s-13 bold color-light-grey">(1200x630px recommended size)</span>
	                                </p>
	                            </div>
	                            <div class="file-drag-drop m-t-10"
                                     v-bind:class="form.facebook.icon ? 'background-file': ''"
                                     v-bind:style="{'background-image': 'url('+form.facebook.icon+')'}"
                                     v-cloak>
	                                <b-form-file class="upload-icon"
                                                 @change="facebookIconChange"
                                                 v-model="form.facebook.icon" accept="image/*">
                         			</b-form-file>

	                                <div class="custom-file-overlay">
	                                  	<span class="img">
			                                <i class="icon-image-upload"
                                               v-if="!form.facebook.icon || !form.facebook.icon"></i>
	                                    </span>

	                                    <h5 class="float f-s-17 bold">
											<span class="text"
                                                  v-if="form.facebook.icon || form.facebook.icon"
                                                  v-text="form.facebook.icon_name">
											  </span>
											<span v-else>Drag files to upload</span>
	                                    </h5>

	                                    <i v-if="form.facebook.icon || form.facebook.icon"
                                           @click="clearIconImage('facebook')"
                                           class="fa fa-times color-light-grey pointer"></i>
	                                </div>
	                            </div>
                             </span>
                                <!-- Twitter Image Section -->
                                <span v-if="form.twitter.status == 1">
	                            <div class="light-font m-t-15 m-b-5">
	                                <p>
	                                	Twitter Image 
	                                	<span class="f-s-13 bold color-light-grey">(1024x512px recommended size)</span>
	                                </p>
	                            </div>
	                            <div class="file-drag-drop m-t-10"
                                     v-bind:class="form.twitter.icon || form.twitter.icon ? 'background-file': ''"
                                     v-bind:style="{'background-image': 'url('+form.twitter.icon+')'}"
                                     v-cloak>
	                                <b-form-file class="upload-icon"
                                                 @change="twitterIconChange"
                                                 v-model="form.twitter.icon" accept="image/*">
                         			</b-form-file>

	                                <div class="custom-file-overlay">
	                                  	<span class="img">
			                                <i class="icon-image-upload"
                                               v-if="!form.twitter.icon || !form.twitter.icon"></i>
	                                    </span>

	                                    <h5 class="float f-s-17 bold">
											<span class="text"
                                                  v-if="form.twitter.icon || form.twitter.icon"
                                                  v-text="form.twitter.icon_name">
											  </span>
											<span v-else>Drag files to upload</span>
	                                    </h5>

	                                    <i v-if="form.twitter.icon || form.twitter.icon"
                                           @click="clearIconImage('twitter')"
                                           class="fa fa-times color-light-grey pointer"></i>
	                                </div>
	                            </div>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="sticky-top">
                    <div class="well bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group m-b-0">
                                    <label class="bold m-b-10">
                                        Referral Variables
                                    </label>
                                    <p>
                                        Customize your referral messages with your own variables.
                                    </p>
                                </div>
                                <ul class="list-of-tags m-t-15">
                                    <li>
                                        <span>{company}</span>
                                    </li>
                                    <li class="">
                                        <span>{company-website}</span>
                                    </li>
                                    <li>
                                        <span>{sender-name}</span>
                                    </li>
                                    <li>
                                        <span>{reward-name}</span>
                                    </li>
                                    <li>
                                        <span>{receiver-name}</span>
                                    </li>
                                    <li>
                                        <span>{referral-link}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Modal -->
        <b-modal class="custom-modal" hide-footer id="preview-share" title="Social Preview" v-cloak>
            <div class="m-b-10 m-t-10">

                <b-tabs class="preview-box-tabs" v-model="tabIndex">
                    <b-tab title="Facebook" v-if="form.facebook.status == 1">
                        <div class="share-card">
                            <div class="share-card-header">
                                <i class="fa fa-facebook-official fb"></i>
                                <p class="inline-block"
                                   v-text="facebookMessage"></p>
                            </div>
                            <div class="share-card-body">
                                <img class="share-card-image"
                                     v-if="form.facebook.icon"
                                     :src="form.facebook.icon">

                                <div class="share-card-desc">
                                    <h3 v-text="getTitle"></h3>
                                    <p v-text="getDescription"></p>
                                    <a :href="website" v-text="website"></a>
                                </div>

                            </div>
                        </div>
                    </b-tab>
                    <b-tab title="Twitter" v-if="form.twitter.status == 1">
                        <div class="share-card p-b-10">
                            <div class="share-card-header">
                                <i class="fa fa-twitter-square twitter"></i>
                                <p class="inline-block"
                                   v-text="twitterMessage"></p>
                            </div>
                            <div class="share-card-body">
                                <img class="share-card-image"
                                     v-if="form.twitter.icon"
                                     :src="form.twitter.icon">

                                <div class="share-card-desc">
                                    <h3 v-text="getTitle"></h3>
                                    <p v-text="getDescription"></p>
                                    <a :href="website" v-text="website"></a>
                                </div>
                            </div>
                            <div class="share-card-footer">
                                <span>
                                    <i class="fa fa-share" aria-hidden="true"></i>
                                </span>
                                <span>
        						    <i class="fa fa-retweet" aria-hidden="true"></i>
                                    109
                                </span>
                                <span>
        						<i class="fa fa-star" aria-hidden="true"></i>
                                    96
                                </span>
                                <span class="dots">
        						<i class="fa fa-circle" aria-hidden="true"></i>
        						<i class="fa fa-circle" aria-hidden="true"></i>
        						<i class="fa fa-circle" aria-hidden="true"></i>
        					</span>
                            </div>
                        </div>
                    </b-tab>
                </b-tabs>

            </div>
        </b-modal>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
      var page = new Vue({
        el: '#sharing-page',
        data: {
          form: {
            facebook: {
              status: 1,
              message: 'Visit {company} to receive your {reward-name} on your next order. {referral-link}',
              icon: '',
              old_icon: '',              
              icon_name: ''
            },
            twitter: {
              status: 1,
              message: 'Visit {company} to receive your {reward-name} on your next order. {referral-link}',
              icon: '',
              old_icon: '',              
              icon_name: ''
            },
            google: {
              status: 0,
              message: 'Visit {company} to receive your {reward-name} for your next order.',
              icon: '',
              old_icon: '',              
              icon_name: ''
            },
            email: {
              status: 0,
              subject: '{sender-name} just sent you a {reward-name} at {company}',
              body: '{receiver-name}, ' + '\n' + '{sender-name} just sent you a coupon for {reward-name} off your next order at {company}.'
            },
            title: '',
            description: ''
          },
          alert: {
            type: '',
            text: '',
            dismissSecs: 5,
            dismissCountDown: 0
          },
          tabIndex: 0,
          website: '{{$company_website}}',
          loading: true,
          saving: false,
        },
        created: function () {
          if (!this.website || this.website == '') {
            this.website = 'www.your-website.com'
          }
          this.getData()
        },
        methods: {
          getData: function () {
            axios.get('/api/merchants/' + Spark.state.currentTeam.id + '/referrals/sharing', this.$root.query).then((response) => {
              if (response.data.data) {
                let sharingData = response.data.data
                this.form.facebook.status = sharingData.facebook_status
                this.form.facebook.message = sharingData.facebook_message
                this.form.facebook.icon = sharingData.facebook_icon
                this.form.facebook.old_icon = sharingData.facebook_icon
                this.form.facebook.icon_name = sharingData.facebook_icon_name

                this.form.twitter.status = sharingData.twitter_status
                this.form.twitter.message = sharingData.twitter_message
                this.form.twitter.icon = sharingData.twitter_icon
                this.form.twitter.old_icon = sharingData.twitter_icon
                this.form.twitter.icon_name = sharingData.twitter_icon_name

                this.form.google.status = sharingData.google_status
                this.form.google.message = sharingData.google_message
                this.form.google.icon = sharingData.google_icon
                this.form.google.old_icon = sharingData.google_icon
                this.form.google.icon_name = sharingData.google_icon_name

                this.form.email.status = sharingData.email_status
                this.form.email.subject = sharingData.email_subject
                this.form.email.body = sharingData.email_body

                this.form.title = sharingData.share_title
                this.form.description = sharingData.share_description
              }
            }).catch((error) => {
              console.log(error)
            }).then(() => {
              this.loading = false
            })
          },
          toogleProgramStatus: function ($type) {
            if (this.form[$type].status == 0) {
              this.form[$type].status = 1
            } else {
              this.form[$type].status = 0
            }
          },
          facebookIconChange: function (evt) {
            var $this = this
            var files = evt.target.files
            var f = files[0]
            $this.form['facebook'].icon = ''

            if (files.length != 0) {

              var reader = new FileReader()

              $this.form['facebook'].icon = ''
              $this.form['facebook'].icon_name = f.name

              reader.onload = (function (theFile) {
                return function (e) {
                  $this.form['facebook'].icon = e.target.result
                }

              })(f)

              reader.readAsDataURL(f)
            }

          },
          twitterIconChange: function (evt) {
            var $this = this
            var files = evt.target.files
            var f = files[0]
            $this.form['twitter'].icon = ''

            if (files.length != 0) {
              var reader = new FileReader()

              $this.form['twitter'].icon = ''
              $this.form['twitter'].icon_name = f.name

              reader.onload = (function (theFile) {
                return function (e) {
                  $this.form['twitter'].icon = e.target.result
                }

              })(f)

              reader.readAsDataURL(f)
            }
          },
          googleIconChange: function (evt) {
            var $this = this
            var files = evt.target.files
            var f = files[0]
            $this.form['google'].icon = ''

            if (files.length != 0) {
              var reader = new FileReader()
              $this.form['google'].icon_name = f.name
              $this.form['google'].icon = ''

              reader.onload = (function (theFile) {
                return function (e) {
                  $this.form['google'].icon = e.target.result
                }

              })(f)

              reader.readAsDataURL(f)
            }
          },
          clearIconImage: function ($type) {
            this.form[$type].icon = ''
          },
          saveSetting: function () {

            if (!this.saving) {
              this.saving = true
              axios.post('/api/merchants/' + Spark.state.currentTeam.id + '/referrals/sharing', this.form).then((response) => {
                this.alert.dismissCountDown = this.alert.dismissSecs
                this.alert.type = 'success'
                this.alert.text = 'Sharing settings saved!'
              }).catch((error) => {
                console.log(error)
                this.alert.dismissCountDown = this.alert.dismissSecs
                this.alert.type = 'danger'
                this.alert.text = 'Error'
              }).then(() => {
                this.saving = false
              })
            }

          },
          countDownChanged: function (dismissCountDown) {
            this.alert.dismissCountDown = dismissCountDown
          },
          previewShare: function () {
            this.tabIndex = 0
            this.$root.$emit('bv::show::modal', 'preview-share')
          }
        },
        computed: {
          tweetMsgLength: {
            get: function () {
              return this.form.twitter.message.length
            },
          },
          facebookMessage: {
            get: function () {
              return (this.form.facebook.message) ? this.form.facebook.message
                .replace(/{company}/ig, '{{$company}}')
                .replace(/{reward-name}/ig, '{{$receiver_reward}}')
                .replace(/{referral-link}/ig, '{{$referral_link}}')
                .replace(/{company-website}/ig, this.website) : ''
            },
            set: function (val) { return this.form.facebook.message = val}
          },
          twitterMessage: {
            get: function () {
              return (this.form.twitter.message) ? this.form.twitter.message
                .replace(/{company}/ig, '{{$company}}')
                .replace(/{reward-name}/ig, '{{$receiver_reward}}')
                .replace(/{referral-link}/ig, '{{$referral_link}}')
                .replace(/{company-website}/ig, this.website) : ''
            },
            set: function (val) { return this.form.twitter.message = val}
          },
          googleMessage: {
            get: function () {
              return (this.form.google.message) ? this.form.google.message
                .replace(/{company}/ig, '{{$company}}')
                .replace(/{reward-name}/ig, '{{$receiver_reward}}')
                .replace(/{referral-link}/ig, '{{$referral_link}}')
                .replace(/{company-website}/ig, this.website) : ''
            },
            set: function (val) { return this.form.google.message = val}
          },
          emailSubject: {
            get: function () {
              return (this.form.email.subject) ? this.form.email.subject
                .replace(/{company}/ig, '{{$company}}')
                .replace(/{reward-name}/ig, '{{$receiver_reward}}')
                .replace(/{referral-link}/ig, '{{$referral_link}}')
                .replace(/{company-website}/ig, this.website) : ''
            },
            set: function (val) { return this.form.email.subject = val}
          },
          emailBody: {
            get: function () {
              return this.form.email.body
                .replace(/{company}/ig, '{{$company}}')
                .replace(/{reward-name}/ig, '{{$receiver_reward}}')
                .replace(/{referral-link}/ig, '{{$referral_link}}')
                .replace(/{company-website}/ig, this.website)
            },
            set: function (val) { return this.form.email.body = val}
          },
          getTitle: {
            get: function () {
              return (this.form.title) ? this.form.title
                .replace(/{company}/ig, '{{$company}}')
                .replace(/{reward-name}/ig, '{{$receiver_reward}}')
                .replace(/{referral-link}/ig, '{{$referral_link}}')
                .replace(/{company-website}/ig, this.website) : ''
            },
            set: function (val) { return this.form.title = val}
          },
          getDescription: {
            get: function () {
              return (this.form.description) ? this.form.description
                .replace(/{company}/ig, '{{$company}}')
                .replace(/{reward-name}/ig, '{{$receiver_reward}}')
                .replace(/{referral-link}/ig, '{{$referral_link}}')
                .replace(/{company-website}/ig, this.website) : ''
            },
            set: function (val) { return this.form.description = val}
          }
        },
        watch: {
          'form.twitter.message': function () {
            if (this.form.twitter.message.length >= 280) {
              return this.form.twitter.message = this.form.twitter.message.substring(0, 280)
            }
          }
        }
      })
    </script>
@endsection
