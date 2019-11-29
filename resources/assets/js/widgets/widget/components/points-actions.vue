<template>
    <section class="widget-wrapper" :class="{'loading': loading}">
        <div class="widget-top-bar" :style="{ 'background-color': $root.globalWidgetSettings.headerBackground, 'color': $root.globalWidgetSettings.headerBackgroundFontColor }">
            <router-link to="/widget" replace>
                <i class="back-icon"></i>
            </router-link>
            <button @click.prevent="postToIframe('close-widget')" 
                    type="button" 
                    class="close" 
                    :style="{'color': $root.globalWidgetSettings.headerBackgroundFontColor}">×</button>
        </div>
        <div class="widget-contents">
            <div class="widget-block main-block">
                <span v-if="tabIndex == 1">
                    <h5 class="main-title">
                        {{ data.waysToEarn.title.replace(/{points-name}/g, $root.pointSettings.plural_name ) }}
                    </h5>
                    <p :class="'text-'+data.waysToEarn.position" style="margin-bottom: 0">
                        {{ data.waysToEarn.text.replace(/{points-name}/g, $root.pointSettings.plural_name ) }}
                    </p>
                </span>
                <span v-else>
                    <h5 class="main-title">
                        {{ data.waysToSpend.title.replace(/{points-name}/g, $root.pointSettings.plural_name ) }}
                    </h5>
                    <p :class="'text-'+data.waysToSpend.position" style="margin-bottom: 0">
                        {{ data.waysToSpend.text.replace(/{points-name}/g, $root.pointSettings.plural_name ) }}
                    </p>
                </span>

                <div class="actions-block" v-if="tabIndex == 1">
                    <div class="actions-list" :class="{'loading': !data.earning.ready}">
                        <div class="action-item" v-for="(action, index) in data.earning.data">
                            <img v-if="action.custom_icon" :src="action.icon" class="pull-left"
                                 style="max-width: 40px;"/>
                            <i v-else="!action.custom_icon" class="pull-left" :class="action.icon"></i>

                            <div class="action-item-content">
                                <p class="title">{{ action.title }}</p>
                                <p>{{ action.desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="actions-block" v-if="tabIndex == 2">
                    <div class="actions-list" :class="{'loading': !data.spending.ready}">
                        <div class="action-item" v-for="(reward, index) in data.spending.data">
                            <img v-if="reward.custom_icon" :src="reward.icon" class="pull-left"
                                 style="max-width: 40px;"/>
                            <i v-else="!reward.custom_icon" class="pull-left" :class="reward.icon"></i>

                            <div class="action-item-content">
                                <p class="title">{{ reward.title }}</p>
                                <p>{{ reward.desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <a @click.prevent="$root.redirectAccountLink('signup')"
                   class="btn btn-block create-store-btn"
                   :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}">
                   {{ data.welcome.buttonText }}
                </a>
                <div class="user-links">
                    <span>{{data.welcome.login}}</span>
                    <a @click.prevent="$root.redirectAccountLink('login')"
                       :style="{color: $root.globalWidgetSettings.linkColor}">
                       {{ data.welcome.loginLinkText }}
                    </a>
                </div>
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
      tabIndex: {
        default: 1,
      }
    },
    data: function () {
      return {
        data: {
          welcome: {
            buttonText: 'Create a Store Account',
            login: 'Aleady have an account?',
            loginLinkText: 'Login'
          },
          waysToEarn: {
            title: 'Earn {points-name}',
            text: 'Earn more {points-name} for completing different actions with our rewards program.',
            position: 'left'
          },
          waysToSpend: {
            title: 'Earn Rewards.',
            text: 'Redeem your {points-name} into awesome rewards.',
            position: 'left'
          },
          spending: {
            data: [],
            show: false,
            ready: false
          },
          earning: {
            data: [],
            show: false,
            ready: false
          },
        },
        loading: true,
      }
    },
    created: function () {
      const vm = this
      let query = this.$root.query
      //Call Login data with Token or Store_ID or whatever from $root

      if (this.$root.widgetSettings.widget && this.$root.widgetSettings.widget['not-logged-in']) {
        let welcome_settings = this.$root.widgetSettings.widget['not-logged-in'].welcome
        if (welcome_settings.button_text) vm.data.welcome.buttonText = welcome_settings.button_text
        if (welcome_settings.login) vm.data.welcome.login = welcome_settings.login
        if (welcome_settings.loginLinkText) vm.data.welcome.loginLinkText = welcome_settings.loginLinkText

        let ways_to_earn = this.$root.widgetSettings.widget['not-logged-in'].ways_to_earn
        if (ways_to_earn.title) vm.data.waysToEarn.title = ways_to_earn.title;
        if (ways_to_earn.text) vm.data.waysToEarn.text = ways_to_earn.text;
        if (ways_to_earn.position) vm.data.waysToEarn.position = ways_to_earn.position;

        let ways_to_spend = this.$root.widgetSettings.widget['not-logged-in'].ways_to_spend
        if (ways_to_spend.title) vm.data.waysToSpend.title = ways_to_spend.title;
        if (ways_to_spend.text) vm.data.waysToSpend.text = ways_to_spend.text;
        if (ways_to_spend.position) vm.data.waysToSpend.position = ways_to_spend.position;

      }

      this.loading = true;

      if(this.tabIndex == 1) {
          this.getEarnings();
      } else {
          this.getSpendings();
      } 

      this.loading = false;
    },
    methods: {
      getEarnings: function() {
        const vm = this
        // Get Earning Actions Data
        axios.post('/api/widget/actions', this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let actions = result.data.data
            if (actions.length) {
              actions.sort((a, b) => parseFloat(a.point_value) - parseFloat(b.point_value));
              // Get All Actions
              vm.data.earning.data = actions.map((item) => {
                return {
                  id: item.id,
                  title: item.action_name || item.action.name || '',
                  desc: item.reward_text,
                  icon: (item.action_icon || (item.action ? (item.action.icon || '') : '')),
                  custom_icon: (item.action_icon ? true : false)
                }
              })
              if (vm.data.earning.data.length) {
                vm.data.earning.show = true
              }
            }
          }
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          vm.data.earning.ready = true
        });

      },
      getSpendings: function() {
        const vm = this
        axios.post('/api/widget/rewards', this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let rewards = result.data.data
            if (rewards.length) {
              rewards.sort((a, b) => parseFloat(a.points_required) - parseFloat(b.points_required));
              // Get All Rewards
              vm.data.spending.data = rewards.filter((item) => {
                return (item.type_id === 1)
              }).map((item) => {
                return {
                  id: item.id,
                  title: item.reward_name,
                  desc: item.reward_text,
                  icon: (item.reward_icon || (item.reward ? (item.reward.icon || '') : '')),
                  custom_icon: (item.reward_icon ? true : false)
                }
              })
              if (vm.data.spending.data.length) {
                vm.data.spending.show = true
              }
            }
          }
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          vm.data.spending.ready = true
        })
      },
      postToIframe: function (message) {
        EventBus.$emit('messageFromIframe', message)
      }
    }
  }
</script>