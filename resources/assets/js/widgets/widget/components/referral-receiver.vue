<template>
    <section class="widget-wrapper" :class="{'loading': $root.innerLoading}">
        <div class="widget-flex">
            <div class="overlay-background"
                 :style="{ 'background-image': 'url(' + data.referralReceiver.background + ')', opacity: data.referralReceiver.opacity }"></div>
            <button @click.prevent="postToIframe('close-widget')" type="button" class="close close-top">×</button>

            <div class="widget-block">
                <div class="">
                    <h5 class="intro-title" style="max-width: 300px;">
                        {{ data.referralReceiver.text }}
                    </h5>
                </div>
                <div class="widget-content" style="margin: auto 0;">
                    <form id="get-my-coupon-form" @submit.prevent="getCoupon">
                        <div v-if="form.errors.length" class="errors alert alert-danger">
                            <p v-for="error in form.errors">{{ error }}</p>
                        </div>
                        <input type="email" class="form-control" placeholder="Enter your email" v-model="form.email">
                        <button type="submit" class="btn btn-block" :disabled="form.loading"
                                :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}">
                            {{ data.referralReceiver.buttonText }}
                        </button>
                    </form>
                </div>
                <div class="lootly-footer" v-if="!$root.globalWidgetSettings.hideLootlyLogo" style="position: unset;">
                    <a href="/" target="_blank">
                        <img src="/images/logos/logo-inner.png" style="width: 100px; margin: auto; padding-top: 15px;">
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
  import EventBus from '../event-bus'

  export default {
    props: {
      referral: {
        default: null,
      }
    },
    data: function () {
      return {
        data: {
          referralReceiver: {
            text: '',
            buttonText: 'Get My Coupon',
            background: '',
            opacity: ''
          }
        },
        form: {
          email: '',
          referral_slug: this.referral,
          errors: [],
          loading: false
        },
        loading: true,
        email: ''
      }
    },
    created: function () {
      var vm = this
      //Call Login data with Token or Store_ID or whatever from $root

      if (this.$root.widgetSettings.widget && this.$root.widgetSettings.widget['not-logged-in']) {
        if (this.$root.widgetSettings.widget['not-logged-in'].referral_receiver) {
          let referrals_settings = this.$root.widgetSettings.widget['not-logged-in'].referral_receiver
          if (referrals_settings.text) vm.data.referralReceiver.text = referrals_settings.text
          if (referrals_settings.button_text) vm.data.referralReceiver.buttonText = referrals_settings.button_text
          if (referrals_settings.background) vm.data.referralReceiver.background = referrals_settings.background
          if (referrals_settings.background_opacity) {
            let opacity_value = parseInt(referrals_settings.background_opacity)
            if (!isNaN(opacity_value)) {
              vm.data.referralReceiver.opacity = Math.round(opacity_value) / 100
            }
          }
        }
      }

      vm.getData()
    },
    methods: {
      getData: function () {
        const vm = this
        vm.loading = true

        let refSlug = vm.referral
        if (!refSlug.trim()) {
          vm.$router.replace({path: '/widget'})
          return
        }

        // Get Referral Reward Data
        axios.post('/api/widget/referral/' + vm.referral + '/reward', this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let reward = result.data.data
            let customerName = reward.customer_name || 'Someone'
            let rewardText = reward.reward_text || ''
            vm.data.referralReceiver.text = vm.data.referralReceiver.text.replace('{referral-name}', customerName)
            vm.data.referralReceiver.text = vm.data.referralReceiver.text.replace('{referral-discount}', rewardText)
          }
        }).catch((error) => {
          console.log(error.response)
          vm.$router.replace('/widget')
        }).then(() => {
          vm.loading = false
        })

      },
      getCoupon: function () {
        const vm = this
        const f = document.getElementById('get-my-coupon-form')

        if (!vm.form.loading) {
          vm.form.loading = true
          vm.$root.innerLoading = true;

          let formData = JSON.parse(JSON.stringify(this.$root.query))
          formData.email = vm.form.email
          formData.referral_slug = vm.form.referral_slug

          // Get Coupon Data
          axios.post('/api/widget/referral/' + vm.referral + '/coupon', formData).then((result) => {
            vm.form.errors = []
            if (result.data && result.data.data) {
              let coupon = result.data.data
              vm.$router.replace('/widget/show-coupon/' + coupon.coupon_code)
            }
          }).catch((error) => {
            console.log(error.response.data.errors)
            vm.form.errors = []
            vm.$root.innerLoading = false;
            try {
              if (error.response.data.errors) {
                let errors = error.response.data.errors
                for (let error_key in errors) {
                  vm.form.errors.push(errors[error_key][0])
                }
              }
            } catch (e) {}
          }).then(() => {
            vm.form.loading = false;
          })
        }
      },
      postToIframe: function (message) {
        EventBus.$emit('messageFromIframe', message)
      }
    }
  }
</script>
<style scoped>
    .btn {
        margin-top: 15px;
        margin-bottom: 20px;
    }

    #get-my-coupon-form .errors > p {
        margin: 0;
    }
</style>