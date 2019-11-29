<template>
    <div class="lootly-widget">
        <section class="widget-wrapper loot-home-guest" :class="{'loading': loading, 'scrolled': scrolled}" :style="{fontFamily: branding.font}">
            <button @click.prevent="postToIframe('close-widget')" type="button" class="close close-top" style="color: #fff;">×</button>
            <div id="lootGuestContents" class="widget-guest-contents">
                <div class="widget-fixed-panel"
                     :style="{'background-color': branding.headerBackground, 'color': branding.headerBackgroundFontColor }">
                       <h3>{{ welcome.header.subtitle.replace(/{company}/g, company) }}</h3>
                     </div>

                <div class="widget-header-panel"
                     :style="{ 'background-image': 'url(' + welcome.background + ')', opacity: opacityFormat(welcome.background_opacity), 'background-color': branding.headerBackground, 'color': branding.headerBackgroundFontColor }">
                       <p>{{welcome.header.title}}</p>
                       <h3>{{ welcome.header.subtitle.replace(/{company}/g, company) }}</h3>
                     </div>

                <div class="wrapper">
                    <div class="widget-block">
                        <div class="">
                            <h5 class="intro-title" :class="'text-'+welcome.position">
                                {{ welcome.title }}
                            </h5>
                            <p :class="'text-'+welcome.position">
                                {{ welcome.subtitle }}
                            </p>
                        </div>
                        <div class="widget-content">
                            <a href="" @click.prevent="postToIframe('account-register')" class="btn btn-block create-store-btn"
                               :style="{ background: branding.buttonColor, color: branding.buttonFontColor}">
                                {{ welcome.buttonText }}
                            </a>

                            <div class="user-links">
                                <span>{{welcome.login}}</span>
                                <a @click.prevent="postToIframe('account-login')" :style="{color: branding.linkColor}">
                                  {{welcome.loginLinkText}}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="widget-block">
                        <div class="home-section-head">
                            <h5 class="intro-title" :class="'text-'+welcome.position">
                              {{welcome.pointsRewardsTitle.replace(/{points-name}/g, points.plural )}}
                            </h5>
                            <p :class="'text-'+welcome.position">{{welcome.pointsRewardsSubtitle.replace(/{points-name}/g, points.plural) }}</p>
                        </div>
                        <div class="widget-content">
                            <div class="actions-block points-rewards">
                                <div class="actions-list">
                                    <a class="action-item">
                                        <i class="icon-points pull-left"></i>
                                        <div class="action-item-content">
                                            <p style="color: #222; font-weight: normal;">
                                              {{welcome.pointsRewardsEarningTitle.replace(/{points-name}/g, points.plural)}}
                                            </p>
                                        </div>
                                        <span class="pull-right m-l-auto">
                                            <i class="toogle-arrow right" :style="{color: branding.primaryColor}"></i>
                                        </span>
                                    </a>
                                    <a class="action-item">
                                        <i class="icon-points pull-left"></i>
                                        <div class="action-item-content">
                                            <p style="color: #222; font-weight: normal;">
                                              {{welcome.pointsRewardsSpendingTitle.replace(/{points-name}/g, points.plural)}}
                                            </p>
                                        </div>
                                        <span class="pull-right m-l-auto">
                                            <i class="toogle-arrow right" :style="{color: branding.primaryColor}"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget-block" v-if="data.vip.data.length > 0">
                        <div class="home-section-head">
                            <h5 class="intro-title" :class="'text-'+welcome.position">{{welcome.vipTitle}}</h5>
                            <p :class="'text-'+welcome.position">{{welcome.vipSubtitle}}</p>
                        </div>
                        <div class="widget-content">
                            <div class="actions-block vip-tiers">
                                <div class="actions-list">
                                    <span v-for="(tier, index) in data.vip.data">
                                        <a class="action-item">
                                            <img v-if="tier.image_url"
                                                 :src="tier.image_url" class="pull-left"
                                                 style="max-width: 38px;"/>
                                            <i v-else class="icon-vip pull-left"
                                               :style="[tier.default_icon_color ? {'color': tier.default_icon_color} : {}]"></i>
                                            <div class="action-item-content">
                                                <p class="title"><b>{{ tier.name }}</b></p>
                                                <p>Spend {{$root.currency}}{{ tier.spend_value }} in 1 Year</p>
                                            </div>
                                            <span class="pull-right m-l-auto" >
                                                <i class="toogle-arrow right" :style="{color: branding.primaryColor}"></i>
                                            </span>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget-block" v-if="data.referrals.receiver.show || data.referrals.sender.show">
                        <div class="home-section-head">
                            <h5 class="intro-title" :class="'text-'+welcome.position">{{welcome.referralTitle}}</h5>
                            <p :class="'text-'+welcome.position">{{welcome.referralSubtitle}}</p>
                        </div>
                        <div class="widget-content">
                            <div class="actions-block">
                                <div class="actions-list">
                                    <div class="action-item" v-if="data.referrals.receiver.show">
                                        <img v-if="data.referrals.receiver.custom_icon"
                                             :src="data.referrals.receiver.icon" class="pull-left"
                                             style="max-width: 40px;"/>
                                        <i v-else="!data.referrals.receiver.custom_icon" class="pull-left"
                                           :class="data.referrals.receiver.icon"></i>

                                        <div class="action-item-content">
                                            <p class="title">{{ data.referrals.receiver.title }}</p>
                                            <p>{{ data.referrals.receiver.desc }}</p>
                                        </div>
                                    </div>
                                    <div class="action-item" v-if="data.referrals.sender.show">
                                        <img v-if="data.referrals.sender.custom_icon"
                                             :src="data.referrals.sender.icon" class="pull-left"
                                             style="max-width: 40px;"/>
                                        <i v-else="!data.referrals.sender.custom_icon" class="pull-left"
                                           :class="data.referrals.sender.icon"></i>

                                        <div class="action-item-content">
                                            <p class="title">{{ data.referrals.sender.title }}</p>
                                            <p>{{ data.referrals.sender.desc }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lootly-footer" style="position: unset;margin-top: 0;" v-if="!branding.hideLootlyLogo">
                <a href="/" target="_blank">
                    <img src="/images/logos/logo-inner.png" style="width: 100px; margin: auto; padding-top: 15px;">
                </a>
            </div>
        </section>
    </div>
</template>

<script>
  export default {
    props: {
      company: {
        type: [String],
        default: '{company}'
      },
      branding: {
        default: function() {
            return {
              fontFamily: 'lato',
              primaryColor: '#2b69d1',
              secondaryColor: '#3d3d3d',
              // New branding styles
              headerBackground: '#2b69d1',
              headerBackgroundFontColor: '#ffffff',
              buttonColor: '#2b69d1',
              buttonFontColor: '#ffffff',
              tabColor: '#2b69d1',
              tabFontColor: '#ffffff',
              // End
              fontColor: '#fff',
              linkColor: '#2b69d1',
              hideLootlyLogo: false
            }
        }
      },
      welcome: {
        default: function() {
            return {
              // New Fields
              header: {
                  title: 'Welcome to',
                  subtitle: '{company}'
              },
              title: 'Join our Rewards Program',
              subtitle: 'Access existing perks, savings and rewards just by shopping with us!',
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
              background_opacity: '1'
            }
        }
      },
      points: {
        default: function() {
            return {
              name: 'Point',
              plural: 'Points'
            }
        }
      }
    },
    data: function () {
      return {
        data: {
          vip: {
            data: [],
            show: false,
            ready: false
          },
          referrals: {
            receiver: {
              title: 'They will receive',
              desc: '',
              icon: '',
              custom_icon: '',
              show: false
            },
            sender: {
              title: 'You will receive',
              desc: '',
              icon: '',
              custom_icon: '',
              show: false
            },
            show: false,
            ready: false
          },
        },
        scrolled: false,
        loading: true
      }
    },
    mounted: function () {
      document.getElementById('lootGuestContents').addEventListener('scroll', this.updateScroll);
    },
    created: function () {
      const vm = this
      this.getData()
      vm.loading = false;
    },
    methods: {
      getData: function () {
        // Get Tiers Data
        const vm = this

        axios.get('/vip/tiers/get').then((result) => {
          if (result.data && result.data.tiers) {
            let tiers = result.data.tiers
            if (tiers.length) {
              // Get All Tiers
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
        });

        axios.get('/referrals/rewards/get').then((result) => {

          var senderReward = result.data.senderReward;
          if (senderReward) {
            vm.data.referrals.sender.title = senderReward.reward_name
            vm.data.referrals.sender.desc = senderReward.reward_text
            vm.data.referrals.sender.icon = (senderReward.reward_icon || (senderReward.reward ? (senderReward.reward.icon || '') : ''))
            vm.data.referrals.sender.custom_icon = (senderReward.reward_icon ? true : false)
            vm.data.referrals.sender.show = true
          }

          var receiverReward = result.data.receiverReward;
          if (receiverReward) {
            vm.data.referrals.receiver.title = receiverReward.reward_name
            vm.data.referrals.receiver.desc = receiverReward.reward_text
            vm.data.referrals.receiver.icon = (receiverReward.reward_icon || (receiverReward.reward ? (receiverReward.reward.icon || '') : ''))
            vm.data.referrals.receiver.custom_icon = (receiverReward.reward_icon ? true : false)
            vm.data.referrals.receiver.show = true
          }

        }).catch((error) => {
          this.errors = error.response
        });

      },
      opacityFormat: function (val) {
        return parseInt(val.replace('%', ''))/100;
      },
      updateScroll: function(el) {
        var top  = document.getElementById('lootGuestContents').scrollTop;
        if(top > 8) {
          this.scrolled = true;
        } else {
          this.scrolled = false;
        }
      }
    }
  }
</script>

<style scoped="" lang="scss">
    .widget-fixed-panel {
        position: absolute !important;
    }
    .widget-guest-contents {
      max-height: 500px;
    }
    .widget-guest-contents .wrapper {
        overflow: unset !important;
    }
    .action-item:hover {
        background: #f7f7f7;
        cursor: pointer;
    }
    ::-webkit-scrollbar {
        width: 0px;  /* remove scrollbar space */
        background: transparent;  /* optional: just make scrollbar invisible */
    }
</style>