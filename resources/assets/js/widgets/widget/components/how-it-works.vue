<template>
    <section class="widget-wrapper" :class="{'loading': loading}">
        <div class="widget-top-bar" :style="{ 'background-color': $root.globalWidgetSettings.headerBackground, 'color': $root.globalWidgetSettings.headerBackgroundFontColor }">
            <router-link to="/widget" replace>
                <i class="back-icon"></i>
            </router-link>

            <button @click.prevent="postToIframe('close-widget')" type="button" class="close">×</button>
        </div>
        <div class="main-full-block">
            <div class="widget-block instructions-block">
                <div class="section-title border-bottom">
                    <p>{{data.howItWorks.title}}</p>
                </div>
                <p class="section-desc" :class="'text-'+data.howItWorks.position">
                    {{data.howItWorks.text}}
                </p>

                <div class="actions-block">
                    <div class="actions-list" :class="{'loading-btn': !data.referrals.receiver.show || !data.referrals.sender.show }">
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
                                 :src="data.referrals.sender.data[0].icon" class="pull-left" style="max-width: 40px;"/>
                            <i v-else="!data.referrals.sender.data[0].custom_icon" class="pull-left"
                               :class="data.referrals.sender.data[0].icon"></i>

                            <div class="action-item-content">
                                <p class="title">{{ data.referrals.sender.title }}</p>
                                <p>{{ data.referrals.sender.data[0].desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8 inline-input">
                        <input :value="customer.referral_link" id="referrals-field" class="form-control">
                    </div>
                    <div class="col-4">
                        <button class="btn btn-block inline-input-btn" @click="copyClipboard"
                                :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}">Copy
                        </button>
                    </div>
                </div>
                <div class="row social-share-btns" v-if="sharing.show">
                    <div class="col-12 text-center">
                        <a v-for="share in sharing.data" @click.prevent="sharingAction(share)"
                           class="social-share" :class="[share.type]"></a>
                    </div>
                </div>
            </div>
            <div class="widget-block activity-block">
                <div class="section-title border-bottom">
                    <p>My Referral Activity</p>
                </div>
                <div>
                    <p class="pull-left">Clicks</p>
                    <p class="pull-right" v-text="data.activity.clicks"></p>
                </div>
                <div>
                    <p class="pull-left">Purchases</p>
                    <p class="pull-right" v-text="data.activity.purchases"></p>
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
          },
          howItWorks: {
            title: 'How It Works',
            text: 'Your referral link gives your friend access to a coupon to immediately save on their first purchase. When they make a purchase using your code, you will be rewarded as well.',
            position: 'left'
          },
          activity: {
            clicks: 0,
            purchases: 0,
            show: false,
            ready: false
          }
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
        loading: true
      }
    },
    created: function () {
      var vm = this
      let query = this.$root.query
      //Call Login data with Token or Store_ID or whatever from $root

      if (this.$root.widgetSettings.widget && this.$root.widgetSettings.widget['logged-in']) {
        // Referrals settings
        if (this.$root.widgetSettings.widget['logged-in'].referrals) {
          let referrals_settings = this.$root.widgetSettings.widget['logged-in'].referrals
          if (referrals_settings.receiver_text) vm.data.referrals.receiver.title = referrals_settings.receiver_text
          if (referrals_settings.sender_text) vm.data.referrals.sender.title = referrals_settings.sender_text
        }

        let how_it_works = this.$root.widgetSettings.widget['logged-in'].how_it_works
        if (how_it_works.title) vm.data.howItWorks.title = how_it_works.title;
        if (how_it_works.text) vm.data.howItWorks.text = how_it_works.text;
        if (how_it_works.position) vm.data.howItWorks.position = how_it_works.position;
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
        vm.loading = true

        // Get Customer Data
        axios.post('/api/widget/customer', this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let customer = result.data.data
            this.customer = customer

            // Get Rewards
            this.getRewards()

            // Get Sharing
            this.getSharing()

            // Get Referral Activity
            this.getReferralActivity()
          }
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          vm.loading = false
        })

      },
      getRewards: function () {
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
                  desc: item.reward_text,
                  icon: (item.reward_icon || (item.reward ? (item.reward.icon || '') : '')),
                  custom_icon: (item.reward_icon ? true : false)
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
                  desc: item.reward_text,
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
      sharingAction: function (item) {

        let message = item.message
        let link = this.sharingPlaceholders['referral-link'] || ''

        let linkInMessageBody = message ? (message.indexOf('{referral-link}') >= 0) : false

        let sharingPlatformTails = {
          'facebook': 'fb',
          'twitter': 'tw',
          'email': 'em',
          'google': 'gp'
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

            window.open('https://www.facebook.com/sharer/sharer.php?u=' + fblink, '', 'width=540, height=400')
            this.incrementSharesCounter('facebook')
            break
          case 'twitter':
            let twlink = link + (link.indexOf('?') < 0 ? '?' : '&') + (sharingPlatformTails[item.type] ? ('fpl=' + sharingPlatformTails[item.type]) : '')

            if (linkInMessageBody) {
              window.open('https://twitter.com/intent/tweet?text=' + message, '', 'width=630, height=450')
            } else {
              window.open('https://twitter.com/intent/tweet?url=' + twlink + '&text=' + message, '', 'width=630, height=450')
            }
            this.incrementSharesCounter('twitter')
            break
          case 'google':
            // modal
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

            if (sharingResponse.google_status) {
              this.sharing.data.push({
                type: 'google',
                status: sharingResponse.google_status,
                message: sharingResponse.google_message,
                image: sharingResponse.google_icon,
                image_name: sharingResponse.google_icon_name
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
      getReferralActivity: function () {
        axios.post('/api/widget/customer/referral-activity', this.$root.query).then((response) => {
          if (response.data.data) {
            this.data.activity.clicks = response.data.data.clicks || 0
            this.data.activity.purchases = response.data.data.purchases || 0
          }
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          this.data.activity.ready = true
        })
      },
      copyClipboard: function () {
        let referralsField = document.querySelector('#referrals-field')
        referralsField.select()
        document.execCommand('copy')

        /* unselect the text */
        window.getSelection().removeAllRanges()

      },
      postToIframe: function (message) {
        EventBus.$emit('messageFromIframe', message)
      },
      incrementSharesCounter: function (sharedTo) {
        let formData = Object.assign({}, this.$root.query);
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
    .instructions-block {
        padding-top: 20px;
        padding-bottom: 15px;
        border-top: 1px solid #e6e8f0;

        & .section-title {
            margin-bottom: 5px;
            font-weight: bold;

            & p {
                margin-bottom: 10px;
            }
        }
        & .section-desc {
            margin-top: 10px;
            margin-bottom: 5px;
        }
        & .actions-block {
            margin-top: 0;
        }
        & .social-share-btns {
            margin-top: 25px;
            margin-bottom: 10px;

            & .social-share {
                display: inline-block;
            }
        }
    }

    .activity-block {
        padding-top: 20px;
        padding-bottom: 5px;
        border-top: 1px solid #e6e8f0;
        & p {
            font-size: 15px;
        }
        & .section-title {
            margin-bottom: 15px;
            font-weight: bold;

            & p {
                margin-bottom: 10px;
                font-size: 16px;
            }
        }
    }
</style>
