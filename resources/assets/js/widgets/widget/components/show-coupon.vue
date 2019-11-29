<template>
    <section class="widget-wrapper" :class="{'loading': $root.innerLoading}">
        <div class="widget-top-bar" :style="{ 'background-color': $root.globalWidgetSettings.headerBackground, 'color': $root.globalWidgetSettings.headerBackgroundFontColor }">
            <button @click.prevent="postToIframe('close-widget')" type="button" class="close">×</button>
        </div>

        <div class="widget-contents">
            <div class="widget-block get-coupon">
                <div class="get-coupon-head text-center" v-if="coupon.show">
                    <p class="title">
                        <b>Congratulations!</b>
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
                        <button class="btn btn-block inline-input-btn" 
                                @click="copyClipboard"
                                :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}">Copy
                        </button>
                    </div>
                </div>
                <p class="copy-desc" v-if="coupon.show">
                    Copy this coupon code and use it on your next purchase with us. The code has also been sent to your email.
                </p>
                <a @click.prevent="postToIframe('close-widget')" 
                   class="btn btn-block" 
                   :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}">
                    Continue Shopping
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
      couponCode: {
        default: null,
      }
    },
    data: function () {
      return {
        coupon: {
          data: {},
          ready: false,
          show: false
        },
        loading: true,
      }
    },
    created: function () {
      const vm = this
      this.$root.innerLoading = true;
      if (vm.couponCode) {
        axios.post('/api/widget/coupons/'+vm.couponCode, this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let coupon = result.data.data

            vm.coupon.data = {
              discount: (coupon.merchant_reward ? (coupon.merchant_reward.reward_display_name ? coupon.merchant_reward.reward_display_name : coupon.merchant_reward.reward_name) : ''),
              coupon: coupon.coupon_code ? coupon.coupon_code : vm.couponCode,
              icon: (coupon.merchant_reward ? (coupon.merchant_reward.reward_icon || (coupon.merchant_reward.reward ? (coupon.merchant_reward.reward.icon || '') : '')) : ''),
              custom_icon: (coupon.merchant_reward ? (coupon.merchant_reward.reward_icon ? true : false) : false)
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
      postToIframe: function(message) {
        EventBus.$emit('messageFromIframe', message)
      }
    }
  }
</script>

<style lang="scss" scoped>
    .get-coupon {
        margin-bottom: 10px;
        & .discount-text {
            font-size: 17px;
            margin-bottom: 25px;
        }

        & .get-coupon-head {
            & .title {
                font-size: 20px;
                margin-top: 10px;
                margin-bottom: 20px;
            }
            & i {
                display: block;
                margin-bottom: 20px;
                font-size: 70px;
                color: inherit;
            }

        }
        & .copy-desc {
            color: #464646;
            margin-top: 20px;
            margin-bottom: 30px;
        }
    }

    .lootly-footer {
        margin-top: 15px;
    }
</style>