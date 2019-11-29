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
                <div class="actions-block">
                    <h5 class="">VIP Tier</h5>
                    <div class="actions-list" :class="{'loading': !data.vip.ready}">
                        <div class="action-item vip-tier opened" v-for="(tier, index) in data.vip.data">
                            <span style="display: flex;">
                                <span>
                                    <img v-if="tier.image_url"
                                         :src="tier.image_url" class="pull-left"
                                         style="max-width: 40px;"/>
                                    <i v-else class="icon-vip pull-left"
                                       :style="[tier.default_icon_color ? {'color': tier.default_icon_color} : {}]"></i>
                                </span>
                                <div class="action-item-content">
                                    <p class="title">{{ tier.name }}</p>
                                    <p>{{ tier.requirement_text }}</p>
                                </div>
                            </span>
                            <div class="benefits-list">
                                <p class="title">Benefits</p>
                                <p>{{ getMultiplierText(tier.multiplier_text_default, tier.multiplier) }}</p>
                                <p v-for="benefit in tier.benefits">{{ benefit.benefits_discount_text }}</p>
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
      id: {
        default: null,
      }
    },
    data: function () {
      return {
        data: {
          welcome: {
            buttonText: 'Create an Account',
            login: 'Already have an account?',
            loginLinkText: 'Login'
          },
          makePurchasePoints: 1,
          vip: {
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
      }
      vm.loading = true;
      this.getMakePurchaseAction();
      this.getVIP();
      vm.loading = false;
    },
    methods: {
      getVIP: function () {
        const vm = this;
        vm.loading = true;
        // Get Tiers Data
        axios.post('/api/widget/tier/'+vm.id, this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let tiers = result.data.data
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
          vm.data.vip.ready = true;
        })
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
      getMultiplierText: function (text, points) {
        return text.replace(/{points-name}/g, points * this.data.makePurchasePoints == 1 ? this.$root.pointSettings.name : this.$root.pointSettings.plural_name).replace(/{currency}/g, this.$root.currency).replace(/{points}/g, parseInt(points) * this.data.makePurchasePoints)
      },
      postToIframe: function (message) {
        EventBus.$emit('messageFromIframe', message)
      }
    }
  }
</script>