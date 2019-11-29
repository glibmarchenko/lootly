<template>
    <section class="widget-wrapper" :class="{'loading': loading}" v-cloak>
        <div class="widget-top-bar"
             :style="{ 'background-color': $root.globalWidgetSettings.headerBackground, 'color': $root.globalWidgetSettings.headerBackgroundFontColor }">
            <router-link :to="$root.fromRoute" replace>
                <i class="back-icon"></i>
            </router-link>

            <button type="button" class="close" @click.prevent="$root.sendMessageFromWidget('close-widget')">×</button>
        </div>
        <div class="main-full-block">
            <div class="alert" v-if="form.alert.show"
                 :class="{'alert-success' : (form.alert.type == 'success'), 'alert-danger' : (form.alert.type == 'danger')}"
                 v-text="form.alert.message"></div>
            <div id="referral-email-form" class="widget-block">
                <div class="section-title border-bottom">
                    <p>Tell your friends and be rewarded</p>
                </div>
                <div class="form-group">
                    <input class="form-control" :class="{'error' : form.errors.name}" placeholder="Name"
                           v-model="form.data.name">
                    <span v-if="form.errors.name" v-text="form.errors.name[0]" class="danger-error"></span>
                    <input class="form-control" :class="{'error' : form.errors.email}" placeholder="Email"
                           v-model="form.data.email">
                    <span v-if="form.errors.email" v-text="form.errors.email[0]" class="danger-error"></span>
                    <input class="form-control" :class="{'error' : form.errors.subject}" placeholder="Subject"
                           v-model="form.data.subject">
                    <span v-if="form.errors.subject" v-text="form.errors.subject[0]" class="danger-error"></span>
                    <textarea class="form-control" :class="{'error' : form.errors.body}"
                              v-model="form.data.body"></textarea>
                    <span v-if="form.errors.body" v-text="form.errors.body[0]" class="danger-error"></span>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button class="btn btn-block inline-input-btn" @click="sendEmail"
                                :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}">
                            Send Email
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="lootly-footer">
            <a href="/" target="_blank">
                <img src="/images/logos/logo-inner.png" style="width: 100px; margin: auto; padding-top: 15px;">
            </a>
        </div>
    </section>
</template>
<script>
  export default {
    props: {
      placeholders: {
        type: Object,
        default: function () {return {}}
      },
      sharingData: {
        type: Object,
        default: function () {return {}}
      }
    },
    data: function () {
      return {
        form: {
          data: {
            name: '',
            email: '',
            subject: '{sender-name} just sent you a {reward-name} at {company}',
            body: '{receiver-name}, \n{sender-name} just sent you a coupon for {reward-name} off your next order at {company}.'
          },
          errors: {},
          alert: {
            show: false,
            type: '',
            message: ''
          }
        },
        loading: true
      }
    },
    created: function () {
      if (this.sharingData.subject) {
        this.form.data.subject = this.sharingData.subject || ''
      }

      if (this.sharingData.body) {
        this.form.data.body = this.sharingData.body || ''
      }

      let link = this.placeholders['referral-link'] || ''
      let linkInMessageBody = this.form.data.body ? (this.form.data.body.indexOf('{referral-link}') >= 0) : false

      for (let key in this.placeholders) {
        if (this.form.data.subject) {
          this.form.data.subject = this.form.data.subject.replace('{' + key + '}', this.placeholders[key])
        }
        if (this.form.data.body) {
          this.form.data.body = this.form.data.body.replace('{' + key + '}', this.placeholders[key])
        }
      }

      this.loading = false
    },
    computed: {
      username: function () {
        return this.placeholders ? (this.placeholders['sender-name'] || 'Someone') : 'Someone'
      }
    },
    methods: {
      sendEmail: function () {
        if (!this.loading) {
          this.form.alert.show = false
          this.form.errors = {}
          this.loading = true
          let formData = {...this.$root.query, ...this.form.data}
          axios.post('/api/widget/customer/referral-email', formData).then((res) => {
            this.form.alert.show = true
            this.form.alert.type = 'success'
            this.form.alert.message = 'Email was successfully sent!'
            this.incrementSharesCounter('email')
            this.$router.replace({
              name: 'home'
            })
          }).catch((err) => {
            if (err.response.status === 422) {
              if (err.response.data.errors) {
                this.form.errors = err.response.data.errors
              }
            } else {
              if (err.response.data.message) {
                this.form.alert.show = true
                this.form.alert.type = 'danger'
                this.form.alert.message = err.response.data.message
              }
            }
          }).then(() => {
            this.loading = false
          })
        }
        this.alert = true
        // @todo: send data
      },
      incrementSharesCounter: function (sharedTo) {
        let formData = Object.assign({}, this.$root.query)
        formData.shared_to = sharedTo
        axios.post('/api/widget/customer/shares', formData).then((response) => {
          // OK
        }).catch((error) => {
          console.log(error)
        })
      }
    }
  }
</script>

<style lang="scss" scoped>
    .section-title {
        margin-bottom: 0px;
        font-weight: bold;

        & p {
            margin-bottom: 15px;
        }
    }

    .alert {
        margin: 15px 15px 5px;
    }

    textarea {
        min-height: 130px;
    }
</style>