<template>
    <section class="widget-wrapper">
        <div class="widget-top-bar" :style="{ 'background-color': $root.globalWidgetSettings.headerBackground, 'color': $root.globalWidgetSettings.headerBackgroundFontColor }">
            <router-link to="/widget" replace>
                <i class="back-icon"></i>
            </router-link>
            <button @click.prevent="postToIframe('close-widget')" type="button" class="close">×</button>
        </div>
        <div class="main-full-block">
            <div class="widget-block vip-tier" v-if="data.currentTier.show" v-cloak>
                <div class="actions-block">
                    <h5 class="">VIP Tier</h5>
                    <div class="current-tier">
                      <span>
                          <img v-if="data.currentTier.custom_icon"
                               :src="data.currentTier.data.image_url"
                               style="max-width: 40px;"/>
                          <i v-else class="icon-vip"
                             :style="[data.currentTier.data.default_icon_color ? {'color': data.currentTier.data.default_icon_color} : {}]"></i>                      
                      </span>
                      <span class="ml-3">
                        <p class="title">{{ data.currentTier.data.name }}</p>
                        <p class="vip-joined-date" v-if="data.currentTier.joinedOn">
                            Joined on {{ data.currentTier.joinedOn }}</p>
                      </span>
                    </div>
                    <div class="benefits-list">
                        <p class="title">Benefits</p>
                        <p>{{ getMultiplierText(data.currentTier.data.multiplier_text_default, data.currentTier.data.multiplier) }}</p>
                        <p v-for="benefit in data.currentTier.data.benefits">{{ benefit.benefits_discount_text }}</p>
                    </div>
                </div>
            </div>
            <div class="widget-block all-tiers">
                <div class="actions-block" :class="{'loading loading-inner': !data.tiers.ready}">
                    <h5 class="">All Tiers</h5>
                    <div class="actions-list">
                        <div class="action-item vip-tier" v-for="(tier, index) in data.tiers.data" :class="{opened: index == 0}">
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
                                <a class="pull-right" onclick="this.closest('.action-item').classList.toggle('opened')">
                                    <i class="toogle-arrow right"></i>
                                </a>
                            </span>
                            <div class="benefits-list">
                                <p class="title">Benefits</p>
                                <p>{{ getMultiplierText(tier.multiplier_text_default, tier.multiplier) }}</p>
                                <p v-for="benefit in tier.benefits">{{ benefit.benefits_discount_text }}</p>
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
          customer: {},
          currentTier: {
            data: {},
            joinedOn: null,
            ready: false,
            show: false,
            custom_icon: false
          },
          tiers: {
            data: [],
            ready: false
          },
          makePurchasePoints: 1
        },
        loading: true
      }
    },
    created: function () {
      var vm = this
      //Call Login data with Token or Store_ID or whatever from $root

      this.getData()

      vm.loading = false

    },
    methods: {
      getData: function () {
        const vm = this

        axios.post('/api/widget/customer', this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let customer = result.data.data
            vm.data.customer = customer

            if (vm.data.customer.tier_history && vm.data.customer.tier_history.length) {
              vm.data.currentTier.joinedOn = vm.data.customer.tier_history[0].joined_human_date
            }

            // Get Make Purchase Action
            this.getMakePurchaseAction()
            // Get Tiers
            this.getTiers()
          }
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          vm.loading = false
        })
      },
      getTiers: function () {
        const vm = this
        axios.post('/api/widget/tiers', this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let tiers = result.data.data
            if (tiers.length) {
              // Get Current Tier
              if (vm.data.customer && vm.data.customer.tier_id) {
                let currentTier = tiers.find((item) => {
                  return parseInt(item.id) === vm.data.customer.tier_id
                })
                if (currentTier) {
                  if (currentTier.image_url && currentTier.image_url.trim()) {
                    vm.data.currentTier.custom_icon = true
                  }
                  vm.data.currentTier.data = currentTier
                  vm.data.currentTier.show = true
                }
              }
              vm.data.tiers.data = tiers
            }
          }
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          vm.data.tiers.ready = true
          vm.data.currentTier.ready = true
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
        return text.replace(/{points-name}/g, points * this.data.makePurchasePoints == 1 ? this.$root.pointSettings.name : this.$root.pointSettings.plural_name).replace(/{currency}/g, this.$root.currency).replace(/{points}/g, points * this.data.makePurchasePoints)
      },
      postToIframe: function (message) {
        EventBus.$emit('messageFromIframe', message)
      }
    }
  }
</script>

<style lang="scss" scoped>
    .current-tier {
      display: flex;
      align-items: center;
      & .title {
        font-size: 15px !important;
        color: #222 !important;
        font-weight: bold;
      }
      & .vip-joined-date {
          font-size: 15px;
          color: #545454;
      }      
    }
    .vip-tier {
        & .vip-title {
            font-size: 23px;
            font-weight: bold;
            color: #222222;
            margin-top: 20px;
            margin-bottom: 5px;
        }
        & .icon-trophy {
            font-size: 46px;
            color: inherit;
            margin: 20px 0;
            display: block;
        }
        & .benefits-list {
            /*display: block;*/
            & .title {
                margin-bottom: 2px;
            }
        }
    }

    .vip-tier[class^="icon-"], .vip-tier[class*=" icon-"] {
        font-size: 46px;
        color: inherit;
        margin: 20px 0;
        display: block;
    }

    .all-tiers {
        /*margin-top: 5px;*/
        padding-top: 15px;
        padding-bottom: 20px;
        border-top: 1px solid #e6e8f0;
    }

    .actions-block {
        margin-bottom: 5px;
        & > h5 {
            padding-bottom: 13px;
        }
    }
</style>