<template>
    <section class="widget-wrapper" :class="{'loading': loading || !data.vip.ready || $root.innerLoading, 'scrolled': scrolled}" v-if="!isEmpty(data)">
        <div class="widget-top-bar"
             style="transition: all 0.15s ease-in;height: 0;padding: 0; overflow: hidden;"
             :style="{ 'background-color': $root.globalWidgetSettings.headerBackground, 'color': $root.globalWidgetSettings.headerBackgroundFontColor }">
            <span>{{data.welcome.header.subtitle}}</span>
            <button @click.prevent="postToIframe('close-widget')"
                    style="padding: 0 10px;margin-right: -5px;"
                    type="button"
                    class="close">×</button>
        </div>

        <div id="widgetLoginPage">
            <div class="widget-block welcome-block">
                <button @click.prevent="postToIframe('close-widget')"
                        type="button"
                        class="close close-top"
                        style="right: 5px"
                        :style="{'color': $root.globalWidgetSettings.primaryColor}">×
                </button>

                <div class="overlay-background"
                     :style="{ 'background-image': 'url(' + data.welcome.background + ')', opacity: data.welcome.opacity }"></div>
                <div class="overlay-content">
                    <div class="text-center">
                        <span v-if="data.welcome.logo">
                          <img :src="data.welcome.logo" style="max-height: 50px;">
                        </span>
                    </div>
                    <p :class="'text-'+data.welcome.position">{{ data.welcome.text }}</p>
                </div>
            </div>
            <div class="widget-block spending-block" 
                 v-show="!$root.hide_rewards" 
                 v-if="data.spending.show"
                 :class="{loading: !data.spending.ready}">
                <div class="spending-title border-bottom">
                    <p>Available Rewards</p>
                </div>
                <span v-for="(reward, index) in data.spending.data" v-if="!reward.isLimitReached && !(isMerchantIntegrationApi() && !reward.isAvailableRewardCoupons)">
                    <div class="actions-block">
                        <div class="actions-list">
                            <div class="action-item">
                                <img v-if="reward.custom_icon" :src="reward.icon" class="pull-left"
                                     style="max-width: 40px;"/>
                                <i v-else="!reward.custom_icon" class="pull-left" :class="reward.icon"></i>

                                <div class="action-item-content">
                                    <p class="title">{{ reward.title }}</p>
                                    <p>{{ reward.desc }}</p>
                                </div>
                                <a @click.prevent="redeemReward(reward)"
                                   class="btn get-coupon pull-right"
                                   :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}">
                                    Get Coupon
                                </a>
                            </div>
                        </div>
                    </div>
                </span>
                <div class="row">
                    <div class="col-6">
                        <a @click="hideReward()" class="bold">Hide Reward</a>
                    </div>
                    <div class="col-6 text-right">
                        <router-link to="/widget/my-rewards" replace>
                            {{ data.points.rewardButtonText }}
                        </router-link>
                    </div>
                </div>
            </div>
            <div class="widget-block points-block">
                <div class="balance-text">
                    <p>{{ data.points.balanceText.replace(/{points-name}/g, $root.pointSettings.plural_name) }}</p>
                    <h3>
                        {{ data.points.value
                        }} {{ data.points.value == 1 ? $root.pointSettings.name : $root.pointSettings.plural_name }}
                    </h3>
                </div>
                <div class="progress" v-if="data.points.showNextReward">
                    <div role="progressbar"
                         class="progress-bar"
                         aria-valuemin="0"
                         :aria-valuenow="data.points.value"
                         :aria-valuemax="data.points.total"
                         :style="{ width: data.points.value/data.points.total*100+'%', background: $root.globalWidgetSettings.primaryColor }"></div>
                </div>
                <p class="points-overview-text" v-if="data.points.showNextReward">
                    {{ data.points.value }} / {{ data.points.total }}
                </p>
                <p class="available-text" v-if="data.points.showNextReward">
                    {{ data.points.availableText }} {{ data.points.total }} {{ $root.pointSettings.plural_name }}
                </p>
                <h3 class="points-discount" v-if="data.points.showNextReward">
                    {{ data.points.discountText }}
                </h3>

                <router-link to="/widget/my-points/2" class="btn btn-block"
                             :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}" replace>
                    {{ data.points.earnButtonText.replace(/{points-name}/g, $root.pointSettings.plural_name) }}
                </router-link>

                <div class="row buttons-row">
                    <div class="col-6" style="padding-right: 7px;">
                        <router-link to="/widget/my-points/1"
                                     class="btn btn-block"
                                     :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}" replace>
                            {{ data.points.spendButtonText.replace(/{points-name}/g, $root.pointSettings.plural_name) }}
                        </router-link>
                    </div>
                    <div class="col-6" style="padding-left: 7px;">
                        <router-link to="/widget/my-rewards" class="btn rewards-btn btn-block" replace>
                            {{ data.points.rewardButtonText }}
                        </router-link>
                    </div>
                </div>
            </div>
            <div class="widget-block vip-block" v-if="data.vip.show && $root.vipSettings.programStatus">
                <div class="overlay-background"
                     :style="{ 'background-image': 'url(' + data.vip.background + ')', opacity: data.vip.opacity }"></div>
                <div class="overlay-content">
                    <div class="vip-title border-bottom">
                        <p>VIP Tiers</p>
                    </div>
                    <div class="actions-block">
                        <div class="vip-action" style="display: flex;">
                            <img v-if="data.vip.custom_icon"
                                 :src="data.vip.custom_icon" class="pull-left"
                                 style="max-width: 40px;"/>
                            <i v-else class="pull-left"
                               :class="[data.vip.icon]"
                               :style="[data.vip.icon_color ? {'color': data.vip.icon_color} : {}]"></i>
                            <div>
                                <p class="title">
                                    <b>{{ data.vip.type }}</b>
                                </p>
                                <p v-html="data.vip.desc"></p>
                            </div>
                        </div>
                    </div>
                    <div class="progress">
                        <div role="progressbar"
                             class="progress-bar"
                             aria-valuemin="0"
                             :aria-valuenow="data.vip.value"
                             :aria-valuemax="data.vip.total"
                             :style="{ width: data.vip.value/data.vip.total*100+'%', background: $root.globalWidgetSettings.primaryColor }"></div>
                    </div>
                    <p class="overview-text text-center">
                        <span>{{data.vip.symbol}}</span>{{data.vip.value}} / 
                        <span>{{data.vip.symbol}}</span>{{data.vip.total}}
                    </p>
                    <router-link to="/widget/vip" class="btn btn-block"
                                 :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}" replace>
                        {{ data.vip.buttonText }}
                    </router-link>
                </div>
            </div>
            <div class="widget-block referrals-block" v-if="$root.referralSettings.program_status && data.referrals.show">
                <div class="overlay-background"
                     :style="{ 'background-image': 'url(' + data.referrals.background + ')', opacity: data.referrals.opacity }"></div>
                <div class="overlay-content">
                    <div class="referrals-title border-bottom">
                        <p>Referrals</p>
                    </div>
                    <p class="referrals-desc">
                        {{ data.referrals.mainText }}
                    </p>
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
                    <div class="referrals-field-table">
                      <table class="">
                        <tr>
                          <td>
                            <input :value="customer.referral_link" id="referrals-field" class="form-control">
                          </td>
                          <td>
                            <button class="btn" 
                                    @click="copyClipboard"
                                    :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}">
                                <span>{{ data.referrals.copyButton }}</span>
                            </button>
                          </td>
                        </tr>
                      </table>
                    </div>  

                    <div class="row social-share-btns" v-if="sharing.show">
                        <div class="col-12 text-center">
                            <a v-for="share in sharing.data" @click.prevent="sharingAction(share)"
                               class="social-share" :class="[share.type]"></a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 text-center">
                            <router-link to="/widget/how-it-works" replace>
                                {{ data.referrals.linkText }}
                            </router-link>
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
          welcome: {
            text: 'Welcome back',
            header: {
              subtitle: '{company}',
            },
            position: 'left',
            logo: '',
            background: '',
            opacity: '0.5',
            ready: false
          },
          spending: {
            data: [],
            show: false,
            ready: false
          },
          points: {
            value: 0,
            total: 1,
            showNextReward: false,
            balanceText: 'Your {points-name} balance',
            availableText: 'Available at',
            earnButtonText: 'Earn more {points-name}',
            spendButtonText: 'Spend {points-name}',
            rewardButtonText: 'My Rewards',
            ready: false
          },
          vip: {
            value: 0,
            type: '',
            symbol: '',
            desc: '',
            icon: 'icon-vip',
            custom_icon: '',
            icon_color: '',
            buttonText: 'See Benefits',
            background: '',
            opacity: '',
            show: false,
            ready: false
          },
          referrals: {
            mainText: 'Tell your friends about us and earn rewards',
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
            copyButton: 'Copy',
            linkText: 'How our referral program works',
            background: '',
            opacity: '',
            show: false,
            ready: false
          },
          makePurchasePoints: 1
        },
        customer: {
          name: '',
          referral_slug: null,
          referral_link: null,
        },
        sharing: {
          data: [],
          ready: false,
          show: false,
        },
        referralReceiverRewards: [],
        scrolled: false,
        loading: true
      }
    },
    mounted: function () {
      document.getElementById('widgetLoginPage').addEventListener('scroll', this.updateScroll)
    },
    created: function () {
      const vm = this
      let query = this.$root.query
      if (this.$root.widgetSettings.widget['not-logged-in'].welcome.header.subtitle) {
        vm.data.welcome.header.subtitle = this.$root.widgetSettings.widget['not-logged-in'].welcome.header.subtitle
        this.data.welcome.header.subtitle = this.data.welcome.header.subtitle.replace('{company}', this.$root.merchant.name)
      }

      if (this.$root.widgetSettings.widget && this.$root.widgetSettings.widget['logged-in']) {
        let pointName = 'points'
        // Welcome settings
        if (this.$root.widgetSettings.widget['logged-in'].welcome) {
          let welcome_settings = this.$root.widgetSettings.widget['logged-in'].welcome
          if (welcome_settings.text) vm.data.welcome.text = welcome_settings.text
          if (welcome_settings.position) vm.data.welcome.position = welcome_settings.position
          if (welcome_settings.icon) vm.data.welcome.logo = welcome_settings.icon
          if (welcome_settings.background) vm.data.welcome.background = welcome_settings.background
          if (welcome_settings.background_opacity) {
            let opacity_value = parseInt(welcome_settings.background_opacity)
            if (!isNaN(opacity_value)) {
              vm.data.welcome.opacity = Math.round(opacity_value) / 100
            }
          }
        }
        // Points settings
        if (this.$root.widgetSettings.widget['logged-in'].points) {
          let points_settings = this.$root.widgetSettings.widget['logged-in'].points
          if (points_settings.balance_text) vm.data.points.balanceText = points_settings.balance_text
          if (points_settings.available_text) vm.data.points.availableText = points_settings.available_text
          if (points_settings.earn_button_text) vm.data.points.earnButtonText = points_settings.earn_button_text.replace('{point-name}', pointName)
          if (points_settings.spend_button_text) vm.data.points.spendButtonText = points_settings.spend_button_text.replace('{point-name}', pointName)
          if (points_settings.rewards_button_text) vm.data.points.rewardButtonText = points_settings.rewards_button_text
        }
        // VIP settings
        if (this.$root.widgetSettings.widget['logged-in'].vip) {
          let vip_settings = this.$root.widgetSettings.widget['logged-in'].vip
          if (vip_settings.button_text) vm.data.vip.buttonText = vip_settings.button_text
          if (vip_settings.background) vm.data.vip.background = vip_settings.background
          if (vip_settings.background_opacity) {
            let opacity_value = parseInt(vip_settings.background_opacity)
            if (!isNaN(opacity_value)) {
              vm.data.vip.opacity = Math.round(opacity_value) / 100
            }
          }
        }
        // Referrals settings
        if (this.$root.widgetSettings.widget['logged-in'].referrals) {
          let referrals_settings = this.$root.widgetSettings.widget['logged-in'].referrals
          if (referrals_settings.main_text) vm.data.referrals.mainText = referrals_settings.main_text
          if (referrals_settings.link_text) vm.data.referrals.linkText = referrals_settings.link_text
          if (referrals_settings.copy_button) vm.data.referrals.copyButton = referrals_settings.copy_button
          if (referrals_settings.receiver_text) vm.data.referrals.receiver.title = referrals_settings.receiver_text
          if (referrals_settings.sender_text) vm.data.referrals.sender.title = referrals_settings.sender_text
          if (referrals_settings.background) vm.data.referrals.background = referrals_settings.background
          if (referrals_settings.background_opacity) {
            let opacity_value = parseInt(referrals_settings.background_opacity)
            if (!isNaN(opacity_value)) {
              vm.data.referrals.opacity = Math.round(opacity_value) / 100
            }
          }
        }
      }

      this.getData()
    },
    computed: {
      merchant: function () {
        return this.$root.merchant || {}
      },
      sharingPlaceholders: function () {
        return {
          'company': this.merchant.name || '',
          'website': this.merchant.website || '',
          'sender-name': this.customer.name,
          'reward-name': this.referralReceiverRewards.length ? this.referralReceiverRewards[0].desc : '',
          'referral-link': this.customer.referral_link,
        }
      }
    },
    methods: {
      getData: function () {
        const vm = this
        this.loading = true

        // Get Customer Data
        axios.post('/api/widget/customer', this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let customer = result.data.data
            this.customer = customer
            let customerName = (customer.name) ? customer.name : ''

            vm.data.welcome.text = vm.data.welcome.text.replace('{customer-name}', customerName)
            if (customer.points) vm.data.points.value = parseInt(customer.points)

            if(vm.$root.vipSettings.requirementType == 'amount-spent') {
              vm.data.vip.symbol = vm.$root.currency;
              vm.data.vip.value = parseFloat(customer.total_spend)
            } else {
              vm.data.vip.symbol = '';
              if (customer.points_earned_in_year) vm.data.vip.value = parseFloat(customer.points_earned_in_year)
            }

            axios.get('/api/widget/merchants/' + this.$root.merchant.id + '/plan').then((response) => {

              let currentPlan = response.data.data.type

                // Get Make Purchase Action
              this.getMakePurchaseAction()

                // Get Rewards
              this.getRewards( currentPlan )

              // Get Tiers
              this.getTiers( currentPlan )              

            }).catch((error) => {
                  console.log(error)
            })

            // Get Sharing
            this.getSharing()
          }
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          vm.data.welcome.ready = true
        })

        setTimeout(() => {
          this.loading = false
        }, 2000)
      },
      getMakePurchaseAction: function() {
        const vm = this
        // Get Earning Actions Data
        axios.post('/api/widget/actions', this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let actions = result.data.data
            if (actions.length) {
              var action = actions.find(obj => obj.action.url == "make-a-purchase");
              if(action) this.data.makePurchasePoints = action.point_value;
            }
          }
        }).catch((error) => {
          console.log(error)
        })
      },            
      getRewards: function ( plan ) {
        const vm = this
        vm.data.plan = plan

        axios.post('/api/widget/rewards', this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let rewards = result.data.data
            if (rewards.length) {

              // Get Next Reward Goal
              let nextReward = rewards.find((item) => {
                return (parseInt(item.points_required) > vm.data.points.value && item.type_id === 1)
              })
              if (nextReward) {
                vm.data.points.total = nextReward.points_required
                vm.data.points.discountText = nextReward.reward_name
                vm.data.points.showNextReward = true
              }

              // Get Available Rewards
              vm.data.spending.data = rewards.filter((item) => {
                return (parseInt(item.points_required) <= vm.data.points.value && item.type_id === 1)
              }).map((item) => {
                return {
                  id: item.id,
                  title: item.reward_name,
                  // desc: item.reward_text,
                  desc: item.reward_text.replace(/{points-name}/g, item.points_required == 1 ? this.$root.pointSettings.name : this.$root.pointSettings.plural_name ),
                  icon: (item.reward_icon || (item.reward ? (item.reward.icon || '') : '')),
                  custom_icon: (item.reward_icon ? true : false),
                  type: (item.reward ? item.reward.slug : item.reward_type),
                  isLimitReached: vm.customer.rewards_spending_limits.find(object => object.id === item.id).is_limit_reached,
                  isAvailableRewardCoupons: vm.customer.reward_coupons.find(object => object.id === item.id).is_available,
                }
              })
              if (vm.data.spending.data.length) {
                vm.data.spending.show = true
              }

              if( vm.data.plan && vm.data.plan !== 'free') {

                  // Get Referral Receiver Rewards
                  let referral_receiver_rewards = rewards.filter((item) => {
                      return (item.type_id === 3)
                  }).map((item) => {
                      return {
                          id: item.id,
                          title: item.reward_name,
                          desc: item.reward_text
                              .replace(/{points-name}/g, item.points_required == 1 || item.reward_value == 1 ? this.$root.pointSettings.name : this.$root.pointSettings.plural_name)
                              .replace(/{currency}/g, this.$root.currency),
                          icon: (item.reward_icon || (item.reward ? (item.reward.icon || '') : '')),
                          custom_icon: (item.reward_icon ? true : false),
                          type: (item.reward ? item.reward.slug : item.reward_type),
                      }
                  })

                  if (referral_receiver_rewards.length) {
                      vm.data.referrals.receiver.title = referral_receiver_rewards[0].title
                      vm.data.referrals.receiver.data = referral_receiver_rewards
                      vm.referralReceiverRewards = referral_receiver_rewards
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
                          desc: item.reward_text.replace(/{points-name}/g, item.points_required == 1 || item.reward_value == 1 ? this.$root.pointSettings.name : this.$root.pointSettings.plural_name).replace(/{currency}/g, this.$root.currency),
                          icon: (item.reward_icon || (item.reward ? (item.reward.icon || '') : '')),
                          custom_icon: (item.reward_icon ? true : false),
                          type: (item.reward ? item.reward.slug : item.reward_type),
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
          }
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          vm.data.points.ready = true
          vm.data.spending.ready = true
          vm.data.referrals.ready = true
        })
      },
      sharingAction: function (item) {

        let message = item.message
        let link = this.sharingPlaceholders['referral-link'] || ''

        let linkInMessageBody = message ? (message.indexOf('{referral-link}') >= 0) : false

        let sharingPlatformTails = {
          'facebook': 'fb',
          'twitter': 'tw',
          'email': 'em'
        }

        for (let key in this.sharingPlaceholders) {
          if (message) {
            if (key === 'referral-link') {
              if (sharingPlatformTails[item.type]) {
                let rl = this.sharingPlaceholders['referral-link'] + (this.sharingPlaceholders['referral-link'].indexOf('?') < 0 ? '?' : '&') + 'fpl=' + sharingPlatformTails[item.type]
                message = message.replace('{' + key + '}', rl)
              } else {
                message = message.replace('{' + key + '}', this.sharingPlaceholders['referral-link'])
              }
            } else {
              message = message.replace('{' + key + '}', this.sharingPlaceholders[key])
            }
          }
        }

        switch (item.type) {
          case 'facebook':
            // modal
            let fblink = link + (link.indexOf('?') < 0 ? '?' : '&') + (sharingPlatformTails[item.type] ? ('fpl=' + sharingPlatformTails[item.type]) : '')

            window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURI(fblink), '', 'width=540, height=400')
            this.incrementSharesCounter('facebook')
            break
          case 'twitter':
            let twlink = link + (link.indexOf('?') < 0 ? '?' : '&') + (sharingPlatformTails[item.type] ? ('fpl=' + sharingPlatformTails[item.type]) : '')

            if (linkInMessageBody) {
              window.open('https://twitter.com/intent/tweet?text=' + encodeURI(message), '', 'width=630, height=450')
            } else {
              window.open('https://twitter.com/intent/tweet?url=' + encodeURI(twlink) + '&text=' + encodeURI(message), '', 'width=630, height=450')
            }
            this.incrementSharesCounter('twitter')
            break
          case 'email':
            this.$router.replace({
              name: 'referral-email',
              params: {placeholders: this.sharingPlaceholders, sharingData: item}
            })
            break
        }
      },
      getSharing: function () {
        axios.post('/api/widget/referrals/sharing', this.$root.query).then((response) => {
          if (response.data.data) {
            let sharingResponse = response.data.data
            this.sharing.data = []

            if (sharingResponse.email_status) {
              this.sharing.data.push({
                type: 'email',
                status: sharingResponse.email_status,
                subject: sharingResponse.email_subject,
                body: sharingResponse.email_body,
              })
            }

            if (sharingResponse.facebook_status) {
              this.sharing.data.push({
                type: 'facebook',
                status: sharingResponse.facebook_status,
                message: sharingResponse.facebook_message,
                image: sharingResponse.facebook_icon,
                image_name: sharingResponse.facebook_icon_name
              })
            }

            if (sharingResponse.twitter_status) {
              this.sharing.data.push({
                type: 'twitter',
                status: sharingResponse.twitter_status,
                message: sharingResponse.twitter_message,
                image: sharingResponse.twitter_icon,
                image_name: sharingResponse.twitter_icon_name
              })
            }

            this.sharing.show = true
          } else {
            this.sharing.show = false
          }
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          this.sharing.ready = true
        })
      },
      getTiers: function ( plan ) {

        const vm = this
        if( plan === 'ultimate' || plan === 'enterprise' ) {
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
                      // Get Current Tier
                      let currentTier = {
                          spend_value: 0
                      }
                      if (this.customer && vm.customer.tier_id) {
                          let currentTierFound = tiers.find((item) => {
                              return parseInt(item.id) === vm.customer.tier_id
                          })
                          if (currentTierFound) {
                              currentTier = currentTierFound
                          }
                      }
                      // Get Next Tier Goal
                      let nextTier = tiers.find((item) => {
                          return (parseInt(item.spend_value) > parseInt(currentTier.spend_value)) && (parseInt(item.id) !== currentTier.id)
                      })
                      if (nextTier) {
                          vm.data.vip.show = true
                          vm.data.vip.type = nextTier.name
                          vm.data.vip.total = nextTier.spend_value
                          vm.data.vip.icon_color = nextTier.default_icon_color
                          vm.data.vip.custom_icon = nextTier.image_url.trim() ? nextTier.image_url.trim() : ''
                          vm.data.vip.desc = nextTier.todo_text
                      }
                  }
              }
            }).catch((error) => {
                console.log(error)
            }).then(() => {
                vm.data.vip.ready = true
            })
        }
        vm.data.vip.ready = true
      },

      getMultiplierText: function (text, points) {
        return text.replace(/{points-name}/g, points == 1 ? this.$root.pointSettings.name : this.$root.pointSettings.plural_name).replace(/{currency}/g, this.$root.currency).replace(/{points}/g, points * this.data.makePurchasePoints)
      },
      hideReward: function () {
        this.$root.hide_rewards = true;
      },
      copyClipboard: function () {
        let referralsField = document.querySelector('#referrals-field')
        referralsField.select()
        document.execCommand('copy')

        /* unselect the text */
        window.getSelection().removeAllRanges()
      },
      isEmpty: function (obj) {
        for (let key in obj) {
          if (obj.hasOwnProperty(key))
            return false
        }
        return true
      },
      postToIframe: function (message) {
        EventBus.$emit('messageFromIframe', message)
      },
      redeemReward: function (reward) {
        const vm = this
        reward.RedeemError = false

        if (reward.type == 'variable-amount' || reward.type == "Variable amount") {
          vm.$router.replace('/widget/variable-discount/' + reward.id)
          return
        }

        if (!vm.$root.innerLoading) {
          vm.$root.innerLoading = true
          axios.post('/api/widget/rewards/' + reward.id + '/redeem', vm.$root.query).then((response) => {
            if (response.data && response.data.data) {
              let coupon = response.data.data

              vm.$router.replace('/widget/get-coupon/' + coupon.id)
            }
          }).catch((error) => {
            console.log(error)
            reward.RedeemError = true;
            vm.$root.innerLoading = false;
          })
        }
      },
      updateScroll: function (el) {
        var top = window.pageYOffset || document.getElementById('widgetLoginPage').scrollTop
        if (top > 50) {
          this.scrolled = true
        } else {
          this.scrolled = false
        }
      },
      incrementSharesCounter: function (sharedTo) {
        let formData = Object.assign({}, this.$root.query);
        formData.shared_to = sharedTo
        axios.post('/api/widget/customer/shares', formData).then((response) => {
          // OK
        }).catch((error) => {
          console.log(error)
        })
      },
      isMerchantIntegrationApi: function () {
          if (this.merchant.integrations) {
              for (let prop in this.merchant.integrations) {
                  if (this.merchant.integrations[prop]['is_api'] === true) {
                      return true;
                  }
              }
          }
          return false;
      },
    }
  }
</script>

<style lang="scss" scoped>
    #widgetLoginPage {
      overflow-y: auto;
      height: calc(100% - 52px);
    }
    .referrals-field-table {
      width: 100%;
      overflow: hidden;
      & table {
        width: 100%;
      }
      & input {
        height: 38px;
        width: calc(100% - 10px);
        margin-right: 10px;
        min-width: 60px;
      }
      & .btn {
        padding: 8px;
        height: auto;
        width: 100%;
      }
    }
    .scrolled .widget-top-bar {
        height: 60px !important;
        padding: 17px 15px 15px !important;
    }

    .welcome-block {
        margin: 0;
        padding-top: 25px;
        & img {
            margin-bottom: 10px;
        }
        & p {
            color: #49545a;
            margin-bottom: 10px;
        }
    }

    .spending-block {
        padding-bottom: 23px;
        margin-bottom: 10px;
        &:not(.loading) {
          border-bottom: 1px solid #e6e8f0;
        }
        & .spending-title {
            margin-bottom: 5px;
            font-weight: bold;

            & p {
                margin-bottom: 10px;
            }
        }
        & a {
            font-weight: normal;
        }
        & .actions-block {
            margin-top: 0;
            margin-bottom: 5px;
        }
        & .get-coupon {
            height: 38px;
            margin-top: 4px;
            padding-top: 9px;
            font-size: 13px;
        }
    }

    .points-block {
        text-align: center;
        & .balance-text {
            margin-bottom: 30px;
            & p {
                margin: 0;
                color: #333;
            }
            & h3 {
                font-weight: bold;
            }
        }
        & .progress {
            height: 14px;
        }
        & .points-overview-text {
            margin-top: 12px;
            font-size: 15px;
        }
        & .available-text {
            margin: 0;
        }
        & .points-discount {
            font-size: 25px;
            font-weight: bold;
            margin-top: 5px;
            margin-bottom: 30px;
        }
        & .buttons-row {
            margin-top: 10px;
            margin-bottom: 10px;
        }
        & .rewards-btn {
            background-color: #fff;
            border: 1px solid #c7cdd1;
            color: #5c6870;
            box-shadow: 0 1px 2px #dde1e3;
        }
    }

    .vip-block {
        padding-top: 20px;
        padding-bottom: 25px;
        border-top: 1px solid #e6e8f0;
        & .vip-title {
            margin-bottom: 5px;
            font-weight: bold;

            & p {
                margin-bottom: 10px;
            }
        }
        & .actions-block {
            margin-bottom: 10px;
            & .vip-action {
                overflow: hidden;
                padding: 10px 0px;
                & i {
                    margin-top: 10px;
                    font-size: 24px;
                    margin-right: 0;
                }
                & div {
                    float: left;
                    margin-left: 14px;
                }
            }
        }
        & .progress {
            height: 14px;
        }
        & .overview-text {
            margin-top: 12px;
            font-size: 15px;
        }
    }

    .referrals-block {
        padding-top: 20px;
        padding-bottom: 25px;
        border-top: 1px solid #e6e8f0;

        & .referrals-title {
            margin-bottom: 5px;
            font-weight: bold;
            & p {
                margin-bottom: 10px;
            }
        }
        & .referrals-desc {
            margin-top: 10px;
            margin-bottom: 5px;
        }
        & .actions-block {
            margin-top: 0;
            & .action-item {
                background: #fff;
            }
        }
        & .social-share-btns {
            margin-top: 25px;
            margin-bottom: 10px;

            & .social-share {
                display: inline-block;
            }
        }
    }

    .lootly-footer {
        margin-top: 0;
    }

    .actions-list {
        .action-item {
            &.redeem-error {
                border-color: #ff7575;
                -webkit-box-shadow: 5px 8px 15px #ffcacb;
                box-shadow: 5px 8px 15px #ffcacb;

                .btn {
                    background: #fc5257 !important;
                }
            }
        }
    }
</style>
