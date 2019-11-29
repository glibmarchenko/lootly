<template>
    <section class="widget-wrapper loot-home-guest" :class="{'loading': loading, 'scrolled': scrolled}">
        <button @click.prevent="postToIframe('close-widget')"
                type="button"
                class="close close-top"
                :style="{'color': $root.globalWidgetSettings.headerBackgroundFontColor}">×
        </button>

        <div id="lootGuestContents" class="widget-guest-contents">
            <div class="widget-fixed-panel"
                 :style="{ 'background-color': $root.globalWidgetSettings.headerBackground, 'color': $root.globalWidgetSettings.headerBackgroundFontColor }">
                <h3>{{data.welcome.header.subtitle}}</h3>
            </div>

            <div class="widget-header-panel"
                 :style="{ 'background-image': 'url(' + data.welcome.background + ')', opacity: data.welcome.opacity, 'background-color': $root.globalWidgetSettings.headerBackground, 'color': $root.globalWidgetSettings.headerBackgroundFontColor }">
                <p>{{data.welcome.header.title}}</p>
                <h3>{{data.welcome.header.subtitle}}</h3>
            </div>

            <div class="wrapper">
                <div class="widget-block">
                    <div class="">
                        <h5 class="intro-title" :class="'text-'+data.welcome.position">
                            {{ data.welcome.title }}
                        </h5>
                        <p :class="'text-'+data.welcome.position">
                            {{ data.welcome.subtitle }}
                        </p>
                    </div>
                    <div class="widget-content">
                        <a @click.prevent="$root.redirectAccountLink('signup')"
                           class="btn btn-block create-store-btn"
                           :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}">
                            {{ data.welcome.buttonText }}
                        </a>

                        <div class="user-links">
                            <span>{{ data.welcome.login }}</span>
                            <a @click.prevent="$root.redirectAccountLink('login')"
                               :style="{color: $root.globalWidgetSettings.linkColor}">
                              {{ data.welcome.loginLinkText }}
                           </a>
                        </div>
                    </div>
                </div>
                <div class="widget-block">
                    <div class="home-section-head">
                        <h5 class="intro-title" :class="'text-'+data.welcome.position">
                          {{ data.welcome.pointsRewardsTitle.replace(/{points-name}/g, $root.pointSettings.plural_name) }}
                        </h5>
                        <p :class="'text-'+data.welcome.position">
                          {{ data.welcome.pointsRewardsSubtitle.replace(/{points-name}/g, $root.pointSettings.plural_name) }}
                        </p>
                    </div>
                    <div class="widget-content">
                        <div class="actions-block points-rewards">
                            <div class="actions-list">
                                <router-link class="action-item" to="/widget/points-actions/1" replace>
                                    <i class="icon-points pull-left"></i>
                                    <div class="action-item-content">
                                        <p style="color: #222; font-size: 15px;">
                                            {{data.welcome.pointsRewardsEarningTitle.replace(/{points-name}/g, $root.pointSettings.plural_name)}}
                                        </p>
                                    </div>
                                    <span class="pull-right m-l-auto">
                                        <i class="toogle-arrow right"></i>
                                    </span>
                                </router-link>
                                <router-link class="action-item" to="/widget/points-actions/2" replace>
                                    <i class="icon-gift pull-left"></i>
                                    <div class="action-item-content">
                                        <p style="color: #222; font-size: 15px;">
                                            {{data.welcome.pointsRewardsSpendingTitle.replace(/{points-name}/g, $root.pointSettings.plural_name)}}
                                        </p>
                                    </div>
                                    <span class="pull-right m-l-auto">
                                        <i class="toogle-arrow right"></i>
                                    </span>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="widget-block" v-if="data.vip.data.length > 0">
                    <div class="home-section-head">
                        <h5 class="intro-title" :class="'text-'+data.welcome.position">{{data.welcome.vipTitle}}</h5>
                        <p :class="'text-'+data.welcome.position">{{data.welcome.vipSubtitle}}</p>
                    </div>
                    <div class="widget-content">
                        <div class="actions-block vip-tiers">
                            <div class="actions-list">
                                <span v-for="(tier, index) in data.vip.data">
                                    <router-link class="action-item" :to="'/widget/vip-info/'+tier.id" replace>
                                        <img v-if="tier.image_url"
                                             :src="tier.image_url" class="pull-left"
                                             style="max-width: 38px;"/>
                                        <i v-else class="icon-vip pull-left"
                                           :style="[tier.default_icon_color ? {'color': tier.default_icon_color} : {}]"></i>
                                        <div class="action-item-content">
                                            <p class="title"><b>{{ tier.name }}</b></p>
                                            <p>{{ tier.todo_text }}</p>
                                        </div>
                                        <span class="pull-right m-l-auto" >
                                            <i class="toogle-arrow right"></i>
                                        </span>
                                    </router-link>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="widget-block" v-if="$root.referralSettings.program_status && data.referrals.show">
                    <div class="home-section-head">
                        <h5 class="intro-title" :class="'text-'+data.welcome.position">{{data.welcome.referralTitle}}</h5>
                        <p :class="'text-'+data.welcome.position">{{data.welcome.referralSubtitle}}</p>
                    </div>
                    <div class="widget-content">
                        <div class="actions-block">
                            <div class="actions-list">
                                <div class="action-item" v-if="data.referrals.receiver.show">
                                    <img v-if="data.referrals.receiver.data[0].custom_icon"
                                         :src="data.referrals.receiver.data[0].icon" class="pull-left"
                                         style="max-width: 40px;"/>
                                    <i v-else="!data.referrals.receiver.data[0].custom_icon" class="pull-left"
                                       :class="data.referrals.receiver.data[0].icon"></i>

                                    <div class="action-item-content">
                                        <p class="title">{{ data.referrals.receiver.title }}</p>
                                        <p>{{ data.referrals.receiver.data[0].desc }}</p>
                                    </div>
                                </div>
                                <div class="action-item" v-if="data.referrals.sender.show">
                                    <img v-if="data.referrals.sender.data[0].custom_icon"
                                         :src="data.referrals.sender.data[0].icon" class="pull-left"
                                         style="max-width: 40px;"/>
                                    <i v-else="!data.referrals.sender.data[0].custom_icon" class="pull-left"
                                       :class="data.referrals.sender.data[0].icon"></i>

                                    <div class="action-item-content">
                                        <p class="title">{{ data.referrals.sender.title }}</p>
                                        <p>{{ data.referrals.sender.data[0].desc }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="height: 50px;" v-if="!$root.globalWidgetSettings.hideLootlyLogo"></div>
            </div>
            <div class="lootly-footer" style="margin-right: -20px;" v-if="!$root.globalWidgetSettings.hideLootlyLogo">
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
    data: function () {
      return {
        data: {
          welcome: {
            // New Fields
            header: {
              title: 'Welcome to',
              subtitle: '{company}'
            },
            title: 'Join our Rewards Program',
            subtitle: 'Access exciting perks, savings and rewards just by shopping with us!',
            login: 'Already have an account?',
            loginLinkText: 'Login',
            pointsRewardsTitle: 'Points & Rewards',
            pointsRewardsSubtitle: 'Earn {points-name} for completing actions, and turn your {points-name} into rewards.',
            pointsRewardsEarningTitle: 'Ways to earn',
            pointsRewardsSpendingTitle: 'Ways to spend',
            vipTitle: 'VIP Tiers',
            vipSubtitle: 'Gain access to exclusive rewards. Reach higher tiers for more exlucisve perks.',
            referralTitle: 'Referrals',
            referralSubtitle: 'Tell your friends about us and earn rewards',
            // End of new fields
            buttonText: 'Create an Account',
            position: 'center',
            background: '',
            opacity: '0.5'
          },
          vip: {
            data: [],
            show: false,
            ready: false
          },
          referrals: {
            receiver: {
              title: 'They will receive',
              data: [],
              show: false
            },
            sender: {
              title: 'You will receive',
              data: [],
              show: false
            },
            show: false,
            ready: false
          }
        },
        scrolled: '',
        loading: true
      }
    },
    mounted: function () {
      document.getElementById('lootGuestContents').addEventListener('scroll', this.updateScroll)
    },
    created: function () {
      const vm = this
      if (this.$root.widgetSettings.widget && this.$root.widgetSettings.widget['not-logged-in'] && this.$root.widgetSettings.widget['not-logged-in'].welcome) {
        let welcome_settings = this.$root.widgetSettings.widget['not-logged-in'].welcome
        if (welcome_settings.header.title) vm.data.welcome.header.title = welcome_settings.header.title
        if (welcome_settings.header.subtitle) vm.data.welcome.header.subtitle = welcome_settings.header.subtitle
        if (welcome_settings.title) vm.data.welcome.title = welcome_settings.title
        if (welcome_settings.subtitle) vm.data.welcome.subtitle = welcome_settings.subtitle
        if (welcome_settings.button_text) vm.data.welcome.buttonText = welcome_settings.button_text
        if (welcome_settings.login) vm.data.welcome.login = welcome_settings.login
        if (welcome_settings.loginLinkText) vm.data.welcome.loginLinkText = welcome_settings.loginLinkText

        if (welcome_settings.pointsRewardsTitle) vm.data.welcome.pointsRewardsTitle = welcome_settings.pointsRewardsTitle
        if (welcome_settings.pointsRewardsSubtitle) vm.data.welcome.pointsRewardsSubtitle = welcome_settings.pointsRewardsSubtitle
        if (welcome_settings.pointsRewardsEarningTitle) vm.data.welcome.pointsRewardsEarningTitle = welcome_settings.pointsRewardsEarningTitle
        if (welcome_settings.pointsRewardsSpendingTitle) vm.data.welcome.pointsRewardsSpendingTitle = welcome_settings.pointsRewardsSpendingTitle
        if (welcome_settings.vipTitle) vm.data.welcome.vipTitle = welcome_settings.vipTitle
        if (welcome_settings.vipSubtitle) vm.data.welcome.vipSubtitle = welcome_settings.vipSubtitle
        if (welcome_settings.referralTitle) vm.data.welcome.referralTitle = welcome_settings.referralTitle
        if (welcome_settings.referralSubtitle) vm.data.welcome.referralSubtitle = welcome_settings.referralSubtitle

        if (welcome_settings.position) vm.data.welcome.position = welcome_settings.position
        if (welcome_settings.background) vm.data.welcome.background = welcome_settings.background
        if (welcome_settings.background_opacity) {
          let opacity_value = parseInt(welcome_settings.background_opacity)
          if (!isNaN(opacity_value)) {
            vm.data.welcome.opacity = Math.round(opacity_value) / 100
          }
        }
      }

      if (this.$root.merchant) {
        if (this.$root.merchant.name && this.$root.merchant.name.trim() && this.data.welcome.header.subtitle.trim()) {
          this.data.welcome.header.subtitle = this.data.welcome.header.subtitle.replace('{company}', this.$root.merchant.name)
        }
      }

      this.getPlanAndGetData();

      vm.loading = false
    },
    methods: {
      getData: function () {
      },
      getPlanAndGetData: function () {
          axios.get('/api/widget/merchants/' + this.$root.merchant.id + '/plan').then((response) => {
              if (response.data && response.data.data && response.data.data.type) {
                  let currentPlan = response.data.data.type
                  if( currentPlan === 'ultimate' || currentPlan === 'enterprise' ) {
                      this.getVip();
                  }
                  if( currentPlan !== 'free' ) {
                      this.getReferrals();
                  }
              }
          }).catch((error) => {
              console.log(error)
          })
      },
      getReferrals: function () {
        // Get Rewards Data
        const vm = this
        axios.post('/api/widget/rewards', this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let rewards = result.data.data
            if (rewards.length) {
              // Get Referral Receiver Rewards
              let referral_receiver_rewards = rewards.filter((item) => {
                return (item.type_id === 3)
              }).map((item) => {
                return {
                  id: item.id,
                  title: item.reward_name,
                  desc: item.reward_text.replace(/{points-name}/g, item.points_required == 1 || item.reward_value == 1 ? this.$root.pointSettings.name: this.$root.pointSettings.plural_name ).replace(/{currency}/g, this.$root.currency),
                  icon: (item.reward_icon || (item.reward ? (item.reward.icon || '') : '')),
                  custom_icon: (item.reward_icon ? true : false)
                }
              })

              if (referral_receiver_rewards.length) {
                vm.data.referrals.receiver.title = referral_receiver_rewards[0].title
                vm.data.referrals.receiver.data = referral_receiver_rewards
                vm.data.referrals.receiver.show = true
                vm.data.referrals.show = true
              }

              // Get Referral Sender Rewards
              let referral_sender_rewards = rewards.filter((item) => {
                return (item.type_id === 2)
              }).map((item) => {
                return {
                  id: item.id,
                  title: item.reward_name,
                  desc: item.reward_text.replace(/{points-name}/g, item.points_required == 1 || item.reward_value == 1 ? this.$root.pointSettings.name: this.$root.pointSettings.plural_name ).replace(/{currency}/g, this.$root.currency),
                  icon: (item.reward_icon || (item.reward ? (item.reward.icon || '') : '')),
                  custom_icon: (item.reward_icon ? true : false)
                }
              })

              if (referral_sender_rewards.length) {
                vm.data.referrals.sender.title = referral_sender_rewards[0].title
                vm.data.referrals.sender.data = referral_sender_rewards
                vm.data.referrals.sender.show = true
                vm.data.referrals.show = true
              }
            }
          }
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          vm.data.referrals.ready = true
        })

      },
      getVip: function () {
        // Get Tiers Data
        const vm = this
        axios.post('/api/widget/tiers', this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let tiers = result.data.data
            if (tiers.length) {
              tiers = tiers.map(function(tier) {
                let text = ( tier.requirement_text.indexOf('earned') + 1 ) ? 'Earn ' : 'Spend ';
                text += tier.requirement_text.replace('earned ', '').replace('spent ', '').replace('the last ', '');
                tier['todo_text'] = text;
                return tier;
              });
              vm.data.vip.data = tiers
              if (vm.data.vip.data.length) {
                vm.data.vip.show = true
              }
            }
          }
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          vm.data.vip.ready = true
        })
      },
      postToIframe: function (message) {
        EventBus.$emit('messageFromIframe', message)
      },
      updateScroll: function (el) {
        var top = window.pageYOffset || document.getElementById('lootGuestContents').scrollTop
        if (top > 8) {
          this.scrolled = true
        } else {
          this.scrolled = false
        }
      }
    }
  }
</script>

<style scoped="">
    .vip-tiers .action-item:hover,
    .points-rewards .action-item:hover {
        background: #f7f7f7;
    }
    .vip-tiers .action-item p,
    .points-rewards .action-item p {
        font-weight: normal;
    }
    ::-webkit-scrollbar {
        width: 0px;  /* remove scrollbar space */
        background: transparent;  /* optional: just make scrollbar invisible */
    }
</style>
