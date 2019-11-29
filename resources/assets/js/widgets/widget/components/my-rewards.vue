<template>
    <section class="widget-wrapper">
        <div class="widget-top-bar" :style="{ 'background-color': $root.globalWidgetSettings.headerBackground, 'color': $root.globalWidgetSettings.headerBackgroundFontColor }">
            <router-link to="/widget" replace>
                <i class="back-icon"></i>
            </router-link>

            <button @click.prevent="postToIframe('close-widget')" type="button" class="close">×</button>
        </div>
        <div class="main-full-block">
            <div class="widget-block">
                <div class="section-title border-bottom">
                    <p>{{ data.rewards.rewardsTitle }}</p>
                </div>
                <span :class="{'d-none': !data.rewards.ready}">
                  <p class="section-desc" v-if="data.rewards.data.length > 0">
                      {{ data.rewards.rewardsText }}
                  </p>
                  <p v-else>
                      {{ data.rewards.noRewardsText }}
                  </p>
                </span>

                <div class="actions-block">
                    <div class="actions-list" :class="{'loading loading-inner': !data.rewards.ready}">
                        <div class="action-item" v-for="reward in data.rewards.data">
                            <img v-if="reward.custom_icon" :src="reward.icon" class="pull-left" style="max-width: 40px;"/>
                            <i v-else="!reward.custom_icon" class="pull-left" :class="reward.icon"></i>
                            <div class="action-item-content">
                                <p class="title">
                                    <b>{{ reward.title }}</b>
                                </p>
                                <p>
                                    {{ reward.desc }}
                                </p>
                            </div>
                            <div class="pull-right">
                                <span>
                                  <a class="btn"
                                     @click="viewCoupon(reward.id)"
                                     :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}">
                                      {{ data.rewards.rewardViewButton }}
                                  </a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="lootly-footer" v-if="!$root.globalWidgetSettings.hideLootlyLogo">
            <a href="/" target="_blank">
                <img src="/images/logos/logo-inner.png" style="width: 100px; margin: auto; padding-top: 15px;">
            </a>
        </div>
    </section>
</template>

<script>
  import EventBus from '../event-bus'

  export default {
    data: function () {
      return {
        data: {
          rewards: {
            data: [],
            rewardsTitle: 'My Rewards',
            rewardsText: ' All of your earned rewards are below.',
            noRewardsText: 'You don\'t have any earned rewards yet.',
            rewardViewButton: 'View',
            ready: false
          }
        },
        loading: true
      }
    },
    created: function () {
      var vm = this

      if (this.$root.widgetSettings.widget && this.$root.widgetSettings.widget['logged-in']) {
        if (this.$root.widgetSettings.widget['logged-in'].points) {
          let settings = this.$root.widgetSettings.widget['logged-in'].points
          if (settings.rewards_title) vm.data.rewards.rewardsTitle = settings.rewards_title
          if (settings.rewards_text) vm.data.rewards.rewardsText = settings.rewards_text
          if (settings.reward_view_button) vm.data.rewards.rewardViewButton = settings.reward_view_button            
          if (settings.no_rewards_text) vm.data.rewards.noRewardsText = settings.no_rewards_text
        }
      }

      vm.getData()

    },
    methods: {
      changeTab: function (index) {
        this.tabIndex = index
      },
      toggleAction: function (el) {
        alert(el.parentNode)
      },
      viewCoupon: function(id) {
          this.$root.innerLoading = true;
          this.$router.replace('/widget/get-coupon/' + id)
      },
      buttonText: function (type) {

        if (type == 'Facebook Like' || type == 'Twitter Like') {
          return 'Like us'
        } else if (type == 'Facebook Share' || type == 'Twitter Share') {
          return 'Share'
        } else if (type == 'Celebrate a Birthday') {
          return 'Enter date'
        } else if (type == 'Read Content') {
          return 'View Link'
        } else {
          return ''
        }

      },
      getData: function () {
        const vm = this

        axios.post('/api/widget/customer/rewards', this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let rewards = result.data.data
            if (rewards.length) {
              // Get Rewards
              vm.data.rewards.data = rewards.map((item) => {
                return {
                  id: item.id,
                  title: (item.merchant_reward ? (item.merchant_reward.reward_display_name ? item.merchant_reward.reward_display_name : item.merchant_reward.reward_name) : ''),
                  desc: (item.merchant_reward ? item.merchant_reward.reward_text : ''),
                  icon: (item.merchant_reward ? (item.merchant_reward.reward_icon || (item.merchant_reward.reward ? (item.merchant_reward.reward.icon || '') : '')) : ''),
                  custom_icon: (item.merchant_reward ? (item.merchant_reward.reward_icon ? true : false) : false),
                }
              })
            }
          }
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          vm.data.rewards.ready = true
          vm.loading = false
        })
      },
      postToIframe: function(message) {
        EventBus.$emit('messageFromIframe', message)
      }
    }
  }
</script>

<style lang="scss" scoped>
    .section-title {
        margin-bottom: 5px;
        font-weight: bold;

        & p {
            margin-bottom: 10px;
        }
    }
    .section-desc {
        margin-top: 10px;
        margin-bottom: 5px;
    }
    .btn {
        padding: 8px;
        height: 37px;
        margin-top: 5px;
        font-size: 14px;
        min-width: 81px;
    }
</style>