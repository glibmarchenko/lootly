<template>
    <section class="widget-wrapper" :class="{'loading': $root.innerLoading}">
        <div class="widget-top-bar"
             :style="{ 'background-color': $root.globalWidgetSettings.headerBackground, 'color': $root.globalWidgetSettings.headerBackgroundFontColor }">
            <router-link to="/widget" replace>
                <i class="back-icon"></i>
            </router-link>
            <button @click.prevent="postToIframe('close-widget')" type="button" class="close">×</button>
        </div>
        <div class="main-full-block">
            <div class="widget-block points-block">
                <div class="balance-text">
                    <p>{{ data.points.balanceText.replace(/{points-name}/g, $root.pointSettings.plural_name) }}</p>
                    <h3>
                      {{ data.points.value }} {{ data.points.value == 1? $root.pointSettings.name : $root.pointSettings.plural_name }}
                    </h3>
                </div>
            </div>
            <div class="widget-block">
                <p>
                    <span v-if="tabIndex == 1">
                      {{ data.points.redeemTabText.replace(/{points-name}/g, $root.pointSettings.plural_name) }}
                    </span>
                    <span v-else>
                      {{ data.points.earnTabText.replace(/{points-name}/g, $root.pointSettings.plural_name) }}
                    </span>
                </p>

                <div class="row">
                    <div class="col-6">
                        <div class="tab-link" @click="changeTab(1)" :class="tabIndex == 1 ? 'active' : '' ">
                            {{ data.points.rewardsTabButton.replace(/{points-name}/g, $root.pointSettings.plural_name) }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="tab-link" @click="changeTab(2)" :class="tabIndex != 1 ? 'active' : '' ">
                            {{ data.points.earnTabButton.replace(/{points-name}/g, $root.pointSettings.plural_name) }}
                        </div>
                    </div>
                </div>

                <div class="actions-block" v-if="tabIndex == 1">
                    <div class="actions-list" :class="{'loading loading-inner': !data.rewards.ready}">
                        <div class="action-item"
                             :class="{'grayed-out' : !reward.isRedeem, 'redeem-error' : reward.RedeemError}"
                             v-for="reward in data.rewards.data" v-if="!reward.isLimitReached">
                            <img v-if="reward.custom_icon" :src="reward.icon" class="pull-left"
                                 style="max-width: 40px;"/>
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
                              <span v-if="reward.isRedeem">
                                <a @click.prevent="redeemReward(reward)" 
                                   class="btn"
                                   :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}">
                                  Redeem
                                </a>
                              </span>
                                <span v-else>
                                <p class="redeem-msg" v-text="reward.redeemMsg"></p>
                              </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="actions-block earning-actions-block" v-else>
                    <div class="actions-list" :class="{'loading loading-inner': !data.earnPoints.ready}">
                        <div class="action-item"
                             :class="{'grayed-out' : action.isCompleted, 'processing': action.loading}"
                             v-for="action in data.earnPoints.data">
                            <img v-if="action.custom_icon" :src="action.icon" class="pull-left"
                                 style="max-width: 40px;"/>
                            <i v-else="!action.custom_icon" class="pull-left" :class="action.icon"></i>
                            <div class="action-item-content">
                                <p class="title">
                                    {{ action.title }}
                                </p>
                                <p>
                                    {{ action.desc }}
                                </p>
                            </div>
                            <div class="pull-right">
                                <div v-if="!action.isCompleted">
                                    <span v-if="action.button_text">
                                        <router-link to="/widget/birthday" class="btn"
                                                     v-if="action.type == 'Celebrate a Birthday'"
                                                     :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}" replace>
                                            <span v-text="buttonText(action.type)"></span>
                                        </router-link>
                                        <a @click.prevent="clickOnActionButton(action)" 
                                           class="btn" 
                                           target="_blank"
                                           :href="action.link"
                                           :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}"
                                           v-else>
                                            <span v-text="action.button_text"></span>
                                        </a>
                                    </span>
                                </div>
                                <div v-else>
                                    <span>
                                        <p class="redeem-msg">Completed</p>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="widget-block activity-block" v-if="data.activity.data.length > 0">
                <div class="activity-title border-bottom">
                    <p>
                        {{ data.points.pointsActivityTitle.replace(/{points-name}/g, $root.pointSettings.plural_name) }}
                    </p>
                </div>
                <div class="activity-action" v-for="action in data.activity.data">
                    <p v-text="action.title"></p>
                    <div class="ml-auto">
                      <span class="badge badge-pill"
                            :class="parseInt(action.points) >= 0 ? 'badge-success' : 'badge-danger'">
                              {{ action.points }}
                      </span>
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
    props: {
      tabIndex: {
        default: 1,
      }
    },
    data: function () {
      return {
        data: {
          points: {
            value: 0,
            balanceText: 'Your point balance',
            redeemTabText: 'Redeem your {points-name} for great discounts',
            rewardsTabButton: 'Rewards',
            earnTabText: 'Earn {points-name} for completing actions, and turn your points into rewards.',
            earnTabButton: 'Earn {points-name}',
            pointsNeededText: 'You need {#} more {points-name}',
            pointsActivityTitle: 'My {points-name} Activity',            
            ready: false
          },
          earnPoints: {
            data: [],
            show: false,
            ready: false
          },
          rewards: {
            data: [],
            show: false,
            ready: false
          },
          activity: {
            data: [],
            show: false,
            ready: false
          }
        },
        customer: {},
        loading: true,
        intervalTimer: null
      }
    },
    created: function () {
      const vm = this
      let query = this.$root.query

      if (this.$root.widgetSettings.widget && this.$root.widgetSettings.widget['logged-in']) {
        // Points settings
        if (this.$root.widgetSettings.widget['logged-in'].points) {
          let points_settings = this.$root.widgetSettings.widget['logged-in'].points
          if (points_settings.balance_text) vm.data.points.balanceText = points_settings.balance_text

          if (points_settings.redeem_tab_text) vm.data.points.redeemTabText = points_settings.redeem_tab_text
          if (points_settings.rewards_tab_button) vm.data.points.rewardsTabButton = points_settings.rewards_tab_button
          if (points_settings.earn_tab_text) vm.data.points.earnTabText = points_settings.earn_tab_text
          if (points_settings.earn_tab_button) vm.data.points.earnTabButton = points_settings.earn_tab_button

          if (points_settings.points_needed_text) vm.data.points.pointsNeededText = points_settings.points_needed_text
          if (points_settings.points_activity_title) vm.data.points.pointsActivityTitle = points_settings.points_activity_title
        }
      }
      //Call Login data with Token or Store_ID or whatever from $root

      vm.getData()

    },
    methods: {
      getData: function () {
        const vm = this

        // Get Customer Data
        axios.post('/api/widget/customer', this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let customer = result.data.data
            this.customer = customer
            if (customer.points) vm.data.points.value = parseInt(customer.points)

            // Get Rewards
            vm.getRewards()

            // Get Actions
            vm.getActions()

            // Get Points Activity
            vm.getPointActivity()

          }
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          vm.data.points.ready = true
          vm.loading = false
        })

      },
      getActions: function () {
        const vm = this

        // Get Customer Actions Data
        axios.post('/api/widget/customer/actions', this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let actions = result.data.data
            if (actions.length) {

              // Get All Actions
              vm.data.earnPoints.data = actions.reduce(( earnings, item ) => {

                //Exclude Create Account earning for logged in customers
                if( item.action.url !== 'create-account' ){
                  earnings.push({
                    id: item.id,
                    slug: (item.action ? (item.action.url ? item.action.url : '') : ''),
                    type: (item.action ? (item.action.name ? item.action.name : '') : ''),
                    title: item.action_name || item.action.name || '',
                    desc: item.reward_text,
                    //desc: item.reward_default_text.replace(/{points-name}/g, item.point_value == 1 ? this.$root.pointSettings.name: this.$root.pointSettings.plural_name ),
                    icon: (item.action_icon || (item.action ? (item.action.icon || '') : '')),
                    custom_icon: (item.action_icon ? true : false),
                    link: item.link || '',
                    text_message: item.share_message || '',
                    point_value: parseInt(item.point_value),
                    button_text: (item.action ? (item.action.action_btn_text ? item.action.action_btn_text : '') : ''),
                    isCompleted: item.action.url == 'celebrate-birthday' ? (vm.customer.birthday ? true : false) : (item.is_completed || false),
                    loading: false
                  });
                }
                return earnings;
              }, []);

              if (vm.data.earnPoints.data.length) {
                vm.orderEarningActions();
                vm.data.earnPoints.show = true
              }
            }
          }
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          vm.data.earnPoints.ready = true
        })
      },
      orderEarningActions: function() {
        this.data.earnPoints.data.sort(function (a, b) {
            return (b.isCompleted === a.isCompleted)? 0 : b.isCompleted ? -1 : 1;
        });
      },
      checkIsActionCompleted: function (action, completed_actions) {
        try {
          switch (action.action.url) {
            case 'celebrate-birthday':
            case 'make-a-purchase':
              return false
              break
            case 'goal-spend':
              return false
              break
            case 'goal-orders':
              return false
              break
            default:
              if (completed_actions.indexOf(action.id) > -1) {
                return true
              }
              return false
              break
          }
        } catch (e) {
          //
        }
      },
      getRewards: function () {
        const vm = this

        // Get All Rewards
        axios.post('/api/widget/rewards', this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let rewards = result.data.data
            if (rewards.length) {
              // Get Rewards
              vm.data.rewards.data = rewards.filter((item) => {
                return item.type_id === 1
              }).sort((a, b) => {
                if (parseInt(a.points_required) < parseInt(b.points_required)) return -1
                if (parseInt(a.points_required) > parseInt(b.points_required)) return 1
                return 0
              }).map((item) => {
                return {
                  id: item.id,
                  title: item.reward_name,
                  desc: item.reward_text,
                  //desc: item.reward_default_text.replace(/{points-name}/g, item.point_value == 1 ? this.$root.pointSettings.name: this.$root.pointSettings.plural_name ),
                  type: (item.reward ? item.reward.slug : item.reward_type),
                  icon: (item.reward_icon || (item.reward ? (item.reward.icon || '') : '')),
                  custom_icon: (item.reward_icon ? true : false),
                  isRedeem: (parseInt(item.points_required) <= vm.data.points.value),
                  isLimitReached: vm.customer.rewards_spending_limits.find(object => object.id === item.id).is_limit_reached,
                  redeemMsg: (parseInt(item.points_required) - vm.data.points.value > 0) ? vm.data.points.pointsNeededText.replace(/{#}/g, parseInt(parseInt(item.points_required) - vm.data.points.value)).replace(/{points-name}/g, vm.$root.pointSettings.plural_name) : ''
                }
              })

              if (vm.data.rewards.data.length) {
                vm.data.rewards.show = true
              }

            }
          }
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          vm.data.rewards.ready = true
        })
      },
      getPointActivity: function () {
        const vm = this

        // Get 4 latest point records
        axios.post('/api/widget/customer/points-activity', this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let points = result.data.data
            if (points.length) {
              // Get Points
              vm.data.activity.data = points.map((item) => {
                return {
                  id: item.id,
                  title: (item.title.trim() ? item.title.trim() : (item.reason.trim() ? item.reason.trim() : '')),
                  points: item.point_value,
                }
              })

              if (vm.data.activity.data.length) {
                vm.data.activity.show = true
              }

            }
          }
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          vm.data.activity.ready = true
        })
      },
      changeTab: function (index) {
        this.tabIndex = index
      },
      toggleAction: function (el) {
        alert(el.parentNode)
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
      postToIframe: function (message) {
        EventBus.$emit('messageFromIframe', message)
      },
      redeemReward: function (reward) {
        const vm = this
        reward.RedeemError = false

        if (reward.type == 'variable-amount') {
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
            reward.RedeemError = true
          })
        }
      },
      clickOnActionButton: function (action) {
        const vm = this
        let childWindow = null

        switch (action.slug) {
          case 'facebook-like':
            childWindow = window.open('/embedded/facebook-like?href='+action.link, '', 'width=355, height=200')
            // checking window closing
            this.intervalTimer = setInterval(() => {
              if (childWindow.closed) {
                clearInterval(this.intervalTimer)

                // crediting points
                this.completeAction(action)
              }
            }, 500)

            break

          case 'facebook-share':
            childWindow = window.open('https://www.facebook.com/sharer/sharer.php?u=' + action.link, '', 'width=540, height=400')
            // checking window closing
            this.intervalTimer = setInterval(() => {
              if (childWindow.closed) {
                clearInterval(this.intervalTimer)

                // crediting points
                this.completeAction(action)
              }
            }, 500)
            break

          case 'twitter-follow':
            childWindow = window.open('https://twitter.com/intent/follow?screen_name=' + action.link, '_blank')

            // crediting points
            this.completeAction(action)
            break

          case 'twitter-share':
            childWindow = window.open('https://twitter.com/intent/tweet?url=' + action.link + '&text=' + action.text_message, '', 'width=630, height=450')
            // checking window closing
            this.intervalTimer = setInterval(() => {
              if (childWindow.closed) {
                clearInterval(this.intervalTimer)

                // crediting points
                this.completeAction(action)
              }
            }, 500)
            break

          case 'instagram-follow':
            childWindow = window.open('https://www.instagram.com/' + action.link + '/', '_blank')

            // crediting points
            this.completeAction(action)
            break

          case 'read-content':
            childWindow = window.open(action.link, '_blank')

            // crediting points
            this.completeAction(action)
            break

          case 'trustspot-review':
            action.loading = true
            setTimeout(() => {
              action.loading = false
            }, 2000)
            break
        }
      },
      completeAction: function (action) {
        if (!action.loading) {
          action.loading = true
          axios.post('/api/widget/actions/' + action.id + '/complete', this.$root.query).then((response) => {
            if (response.data.data) {
              action.isCompleted = true
              let point = response.data.data
              this.data.points.value += point.point_value
              this.orderEarningActions()
            }
          }).catch((error) => {
            console.log(error)
          }).then(() => {
            action.loading = false
          })
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
    .points-block {
        text-align: center;
        padding-bottom: 0;

        & .balance-text {
            margin-bottom: 0;

            & p {
                margin: 0;
                color: #333;
            }
            & h3 {
                font-weight: bold;
            }
        }
    }

    .activity-block {
        padding-top: 20px;
        padding-bottom: 15px;
        border-top: 1px solid #e6e8f0;

        & .activity-title {
            margin-bottom: 10px;
            font-weight: bold;

            & p {
                margin-bottom: 10px;
            }
        }
        & .activity-action {
            margin-top: 10px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;

            & p {
               margin-bottom: 0;
            }
            & .badge {
                padding: 6px 15px;
                font-size: 13px;
                font-weight: 700;
                min-width: 55px;

                &.badge-success {
                    background: #1cc04a;
                }
                &.badge-danger {
                    background: #fc5257;
                }
            }
        }
    }

    .grayed-out {
        background: #f3f3f3;
    }

    .earning-actions-block .grayed-out {
        opacity: 0.7;
    }

    .processing {
        opacity: 0.7;
        pointer-events: none;
    }

    .btn {
        padding: 7px;
        height: 37px;
        margin-top: 5px;
        font-size: 14px;
        min-width: 81px;
    }

    .redeem-msg {
        width: 60px;
        text-align: center;
        font-size: 12px;
        line-height: 18px;
        padding-right: 7px;
        width: auto;
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
