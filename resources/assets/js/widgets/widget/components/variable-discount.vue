<template>
    <section class="widget-wrapper">
        <div class="loading-overlay" :class="{'d-none': !loading}">
            <div class="loading"></div>
        </div>
        <div class="widget-top-bar" :style="{ 'background-color': $root.globalWidgetSettings.headerBackground, 'color': $root.globalWidgetSettings.headerBackgroundFontColor }">
            <router-link to="/widget" replace>
                <i class="back-icon"></i>
            </router-link>

            <button type="button" class="close" @click.prevent="$root.sendMessageFromWidget('close-widget')">×</button>
        </div>

        <div class="widget-contents">
            <div class="widget-block points-overview">
                <div class="text-center">
                    <div class="balance-text">
                        <p>{{ data.points.balanceText.replace(/{points-name}/g, $root.pointSettings.plural_name) }}</p>
                        <h3>
                            <b>{{ data.points.value }} {{ data.points.value == 1 ? $root.pointSettings.name : $root.pointSettings.plural_name }}</b>
                        </h3>
                    </div>

                    <img v-if="reward.reward_icon != null" :src="reward.reward_icon" class="reward-icon">
                    <i class="icon-coin" v-else></i>

                    <p class="discount-title">
                        <b>{{ reward.reward_name }}</b>
                    </p>
                    <p class="discount-desc">
                      {{ $root.currency }}{{ variable.discountPerPoints }} for every {{ variable.points }} points spent
                    </p>
                    <div class="slider-contianer">
                        <VueSlideBar v-if="range.length" 
                                     v-model="variable.value"
                                     :data="range"
                                     :processStyle="{ backgroundColor: $root.form.primaryColor }"
                                     :tooltipStyles="{ backgroundColor: $root.form.primaryColor, borderColor: $root.form.primaryColor }">
                            <template slot="tooltip" slot-scope="tooltip">
                                <span class="vue-slider-tooltip"></span>
                            </template>
                        </VueSlideBar>
                    </div>
                </div>
                <p class="progress-text">
                    {{variable.value}} Points = {{ $root.currency }}{{discountValue}} off coupon
                </p>
            </div>
            <div class="widget-block redeem-block">
                <a @click.prevent="redeemVariableReward()" 
                   class="btn btn-block"
                   :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}">
                    Redeem
                </a>
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
  import VueSlideBar from 'vue-slide-bar'

  export default {
    components: {
      VueSlideBar
    },
    props: {
      rewardId: {
        default: null,
      }
    },
    data: function () {
      return {
        date: {},
        variable: {
          value: 0,
          points: 1,
          discountPerPoints: '1'
        },
        data: {
          points: {
            value: 0,
            balanceText: 'Your point balance',
            ready: false
          },
        },
        reward: {},
        customer: {},
        range: [],
        loading: true,
      }
    },
    created: function () {
      var vm = this
      var token = this.$root.form.token
      //Call Login data with Token or Store_ID or whatever from $root

      if (this.$root.widgetSettings.widget && this.$root.widgetSettings.widget['logged-in']) {
        // Points settings
        if (this.$root.widgetSettings.widget['logged-in'].points) {
          let points_settings = this.$root.widgetSettings.widget['logged-in'].points
          if (points_settings.balance_text) vm.data.points.balanceText = points_settings.balance_text
        }
      }

      axios.post('/api/widget/customer', this.$root.query).then((result) => {
        if (result.data && result.data.data) {
          let customer = result.data.data
          this.customer = customer
          if (customer.points) vm.data.points.value = parseInt(customer.points)

          // Get Reward
          vm.getReward()

        }
      }).catch((error) => {
        console.log(error)
        vm.$router.replace('/widget')
      }).then(() => {
        vm.data.points.ready = true
      })

      setTimeout(function() {
        vm.loading = false
      }, 2000)

    },
    computed: {
      discountValue: function () {
        return this.variable.discountPerPoints * this.variable.value / this.variable.points
      }
    },
    methods: {
      getReward: function () {
        const vm = this
        axios.post('/api/widget/rewards/' + vm.rewardId, vm.$root.query).then((response) => {
          if (response.data && response.data.data) {
            let reward = response.data.data
            vm.reward = reward
            if (reward.reward && reward.reward.slug == 'variable-amount') {
              let points_required = parseInt(reward.points_required)
              vm.variable.points = parseInt(points_required)
              let reward_value = parseInt(reward.reward_value)
              vm.variable.discountPerPoints = parseInt(reward_value)
              let variable_point_max = parseInt(reward.variable_point_max)
              let variable_point_min = parseInt(reward.variable_point_min)

              let range = []
              for (let i = variable_point_min; i <= variable_point_max; i += points_required) {
                range.push(i)
              }
              vm.range = range
              vm.variable.value = range.length ? range[0] : 0
            } else {
              vm.$router.replace('/widget')
            }
          } else {
            vm.$router.replace('/widget')
          }
        }).catch((error) => {
          console.log(error)
          vm.$router.replace('/widget')
        }).then(() => {
          vm.loading = false
        })
      },
      redeemVariableReward: function () {
        const vm = this
        vm.reward.RedeemError = false

        if (!vm.loading) {
          vm.loading = true
          vm.$root.innerLoading = true
          let discountAmount = vm.discountValue;
          let formData = vm.$root.query
          formData.points = vm.variable.value

          axios.post('/api/widget/rewards/' + vm.rewardId + '/redeem', formData).then((response) => {
            if (response.data && response.data.data) {
              let coupon = response.data.data

              vm.$router.replace('/widget/get-coupon/' + coupon.id + '/' + discountAmount)
            }
          }).catch((error) => {
            console.log(error)
            vm.reward.RedeemError = true
          }).then(() => {
            vm.loading = false
          })
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
    .loading-overlay {
        background: #fff;
        display: flex;
        align-items: center;
        position: relative;
        z-index: 999999;
        height: 100%;
    }
    .reward-icon {
        max-width: 60px;
        margin-bottom: 10px;
        max-height: 75px;
    }
    .points-overview {
        & .balance-text {
            margin-bottom: 20px;
            margin-top: 10px;

            & p {
                margin: 0;
                color: #333;
            }
            & h3 {
                font-weight: bold;
            }
        }
        & i {
            display: block;
            margin-bottom: 20px;
            font-size: 46px;
            color: inherit;
        }
        & .discount-title {
            font-size: 19px;
            margin-bottom: 3px;
            color: #333;
        }
        & .discount-desc {
            font-size: 15px;
            color: #333;
            margin-bottom: 0;
        }
        & .progress-text {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 5px;
            font-size: 15px;
        }
    }
    .slider-container {
        width: 95%;
        margin: auto;
    }
    .vue-slide-bar-component {
        padding-top: 30px !important;
    }
    .redeem-block {
        padding-top: 25px;
        padding-bottom: 20px;
        border-top: 1px solid #e6e8f0;
    }
</style>