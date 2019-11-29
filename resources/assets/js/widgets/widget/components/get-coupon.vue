<template>
    <section class="widget-wrapper" :class="{'loading': $root.innerLoading}">
        <div class="widget-top-bar" :style="{ 'background-color': $root.globalWidgetSettings.headerBackground, 'color': $root.globalWidgetSettings.headerBackgroundFontColor }">
            <router-link :to="$root.fromRoute" replace>
                <i class="back-icon"></i>
            </router-link>
            
            <button @click.prevent="postToIframe('close-widget')" 
                    type="button" 
                    class="close" 
                    :style="{'color': $root.globalWidgetSettings.headerBackgroundFontColor}">×</button>
        </div>

        <div class="widget-contents">
            <div class="widget-block main-block get-coupon">
                <div class="get-coupon-head text-center" v-if="coupon.show">
                    <p class="title">
                        <b>{{ data.title }}!</b>
                    </p>
                    <img v-if="coupon.data.custom_icon" :src="coupon.data.icon" style="max-width: 70px;"/>
                    <i v-else="!coupon.data.custom_icon" :class="coupon.data.icon"></i>
                    <p class="discount-text">{{coupon.data.discount}}</p>
                </div>

                <div class="row" v-if="coupon.show">
                    <div class="col-8 inline-input">
                        <input v-model="coupon.data.coupon" id="coupon-field" class="form-control">
                    </div>
                    <div class="col-4">
                        <button class="btn btn-block inline-input-btn" @click="copyClipboard"
                                :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}">
                            {{ data.copy_button }}
                        </button>
                    </div>
                </div>
                <p class="copy-desc" v-if="coupon.show">
                    {{ data.body_text }}
                </p>
                <a @click.prevent="postToIframe('close-widget')" class="btn btn-block"
                   :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}">
                    {{ data.button_text }}
                </a>
            </div>

            <div class="lootly-footer" v-if="!$root.globalWidgetSettings.hideLootlyLogo">
                <a href="/" target="_blank">
                    <img src="/images/logos/logo-inner.png" style="width: 100px; margin: auto; padding-top: 15px;">
                </a>
            </div>
        </div>
    </section>
</template>

<script>
  import EventBus from '../event-bus'

  export default {
    props: {
      couponId: {
        default: null,
      },
      variableAmount: {
        default: null,
      }
    },
    data: function () {
      return {
        data: {
          title: 'Congratulations',
          copy_button: 'Copy',
          body_text: 'Copy this coupon code and use it on your next purchase with us. The code has also been sent to your email.',
          button_text: 'Continue Shopping'
        },
        coupon: {
          data: {},
          ready: false,
          show: false
        },
        loading: true,
      }
    },
    created: function () {
      const vm = this;

      if (this.$root.widgetSettings.widget && this.$root.widgetSettings.widget['logged-in']) {
        if (this.$root.widgetSettings.widget['logged-in'].coupon) {
          let settings = this.$root.widgetSettings.widget['logged-in'].coupon
          if (settings.title) vm.data.title = settings.title
          if (settings.copy_button) vm.data.copy_button = settings.copy_button
          if (settings.body_text) vm.data.body_text = settings.body_text            
          if (settings.button_text) vm.data.button_text = settings.button_text
        }
      }

      if (vm.couponId) {
        axios.post('/api/widget/customer/rewards/' + vm.couponId, this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let reward = result.data.data

            vm.coupon.data = {
              discount: '',
              coupon: reward.coupon_code,
              icon: (reward.merchant_reward ? (reward.merchant_reward.reward_icon || (reward.merchant_reward.reward ? (reward.merchant_reward.reward.icon || '') : '')) : ''),
              custom_icon: (reward.merchant_reward ? (reward.merchant_reward.reward_icon ? true : false) : false)
            }

            if(reward.merchant_reward) {
              if(reward.merchant_reward.reward_type == 'Variable amount' && vm.variableAmount) {
                vm.coupon.data.discount = reward.merchant_reward.rewardDefaultName.replace(/{reward-value}/g, vm.variableAmount).replace(/{min-value}/g, reward.merchant_reward.order_minimum ? vm.$root.currency+reward.merchant_reward.order_minimum : '').replace(/{currency}/g, vm.$root.currency)
              } else {
                vm.coupon.data.discount = reward.merchant_reward.reward_display_name ? reward.merchant_reward.reward_display_name : reward.merchant_reward.reward_name
              }
            }

            vm.coupon.show = true
          }
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          vm.coupon.ready = true
          vm.$root.innerLoading = false
        })

      } else {
        vm.$root.innerLoading = false
      }
    },
    methods: {
      copyClipboard: function () {
        let couponField = document.querySelector('#coupon-field')
        couponField.select()
        document.execCommand('copy')

        /* unselect the text */
        window.getSelection().removeAllRanges()

      },
      postToIframe: function (message) {
        EventBus.$emit('messageFromIframe', message)
      }
    }
  }
</script>

<style lang="scss" scoped>
    .lootly-footer {
        margin-top: 15px;
    }
</style>