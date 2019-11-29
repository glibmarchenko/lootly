/*
 |-----------------------
 | Widget widget Scripts
 |-----------------------
*/

window.axios = require('axios')
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;

import Vue from 'vue'
import VueRouter from 'vue-router'
import EventBus from './event-bus'
import { routes } from './routes'

Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
    routes
});

Vue.component('tab-reminder', require('./components/tab-reminder.vue'))

const app = new Vue({
    router,
    data: {
        query: {
            shop: {
                domain: findGetParameter('_shd') || '',
                signature: findGetParameter('_shs') || '',
            },
            customer: {
                id: findGetParameter('_cid') || '',
                signature: findGetParameter('_cs') || '',
            },
        },
        merchant: {},
        platform: {
            type: '',
            signupLink: '/account/register',
            loginLink: '/account/login'
        },
        isAuth: false,
        isLogin: false,
        fromRoute: '/widget',
        widgetOpened: false,
        viewportWidth: 1000,
        hide_rewards: false,
        form: {
            tab: {
                background: '#2b69d1',
                color: '#fff',
                icon: 'loot-tab-heart'
            },
            fontFamily: 'lato',
            primaryColor: '#2b69d1',
            secondaryColor: '#3d3d3d',
            fontColor: '#fff',
            linkColor: '#2b69d1'
        },
        globalWidgetSettings: {
            fontFamily: 'lato',
            primaryColor: '#2b69d1',
            secondaryColor: '#3d3d3d',
            headerBackground: '#2b69d1',
            headerBackgroundFontColor: '#ffffff',
            buttonColor: '#2b69d1',
            buttonFontColor: '#ffffff',
            fontColor: '#333',
            linkColor: '#2b69d1',
            hideLootlyLogo: false
        },
        widgetSettings: {
            tab: {
                rewards_visible: 1,
                enable_reminders: 0,
                position: "right",
                text: "Rewards",
                bg_color: "#2b69d1",
                font_color: "#FFFFFF",
                side_spacing: 30,
                bottom_spacing: 30,
                display_on: "desktop-mobile",
                desktop_layout: "icon-text",
                custom_icon: 0,
                icon: "loot-tab-heart",
                icon_name: "Heart"
            }
        },
        pointSettings: {},
        vipSettings: {
            programStatus: 0,
            requirementType: 'amount-spent'
        },
        referralSettings: {
            program_status: 0
        },
        currency: "$",
        innerLoading: false,
        loading: true,
        initialized: false,
        ready: false,
        active: false,
    },
    created: function() {
        this.initWidget()
    },
    methods: {
        initWidget: function() {
            this.loading = true
            axios.post('/api/widget/customer/authCheck', this.query).then((result) => {
                this.isAuth = true
                if (result.data.auth) this.isLogin = true
                if (result.data.merchant) this.merchant = result.data.merchant
                if (result.data.referral_settings) this.referralSettings = result.data.referral_settings
                if (result.data.merchant_platform) {
                    this.platform.type = result.data.merchant_platform
                    this.platformDefaultLinks()
                }

                this.getWidgetSettings();
                this.getVipSettings();
                return;

            }).catch(error => {
                console.log(error)
            }).then(() => {
                this.initialized = true
                this.authRedirect()
            })
        },
        getWidgetSettings: function() {

            // Get main data Like colors, buttons links ... etc
            let wSettings = new Promise((resolve, reject) => {
                axios.post('/api/widget/settings', this.query).then((result) => {
                    if (result.data && result.data.data) {
                        this.widgetSettings = result.data.data
                        if (result.data.data.branding) {
                            let branding_settings = result.data.data.branding
                            if (branding_settings.primary_color) this.globalWidgetSettings.primaryColor = branding_settings.primary_color
                            if (branding_settings.secondary_color) this.globalWidgetSettings.secondaryColor = branding_settings.secondary_color

                            if (branding_settings.header_bg) this.globalWidgetSettings.headerBackground = branding_settings.header_bg
                            if (branding_settings.header_bg_font_color) this.globalWidgetSettings.headerBackgroundFontColor = branding_settings.header_bg_font_color
                            if (branding_settings.button_color) this.globalWidgetSettings.buttonColor = branding_settings.button_color
                            if (branding_settings.button_font_color) this.globalWidgetSettings.buttonFontColor = branding_settings.button_font_color

                            if (branding_settings.link_color) this.globalWidgetSettings.linkColor = branding_settings.link_color
                            if (branding_settings.font) this.globalWidgetSettings.fontFamily = branding_settings.font
                            if (typeof branding_settings.remove_in_widget !== 'undefined' && branding_settings.remove_in_widget !== null) this.globalWidgetSettings.hideLootlyLogo = !!branding_settings.remove_in_widget

                            var styles = '<style>.loading{border-color:' + this.globalWidgetSettings.primaryColor + ';}'
                            if (branding_settings.custom_css) styles+= branding_settings.custom_css
                            styles += '</style>';
                            document.getElementById('app').insertAdjacentHTML('beforeend', styles)
                        }
                        if (result.data.data.signup_link) this.platform.signupLink = result.data.data.signup_link
                        if (result.data.data.login_link) this.platform.loginLink = result.data.data.login_link
                    }
                }).catch(error => {
                    console.log(error)
                    reject()
                }).then(() => {
                    resolve()
                })
            })

            let mSettings = new Promise((resolve, reject) => {
                axios.post('/api/widget/merchant-settings', this.query).then((result) => {
                    if (result.data) {
                        this.currency = result.data;
                    }
                }).catch(error => {
                    console.log(error)
                    reject()
                }).then(() => {
                    resolve()
                })
            })

            let pSettings = new Promise((resolve, reject) => {
                axios.post('/api/widget/point-settings', this.query).then((result) => {
                    if (result.data && result.data.data) {
                        this.pointSettings = result.data.data
                    }
                }).catch(error => {
                    console.log(error)
                    reject()
                }).then(() => {
                    resolve()
                })
            })

            return Promise.all([wSettings, pSettings]).then(() => {
                this.loading = false
                let tabSettings = this.widgetSettings.tab
                let sideSpacing = (tabSettings.side_spacing) ? tabSettings.side_spacing : '30'
                let bottomSpacing = (tabSettings.bottom_spacing) ? tabSettings.bottom_spacing : '30'
                let styles = `
                    #lootly-widget {
                        margin: 20px 30px ${bottomSpacing}px;
                        max-height: calc(100vh - ${bottomSpacing}px);
                        max-width: calc(100vw - ${sideSpacing}px);
                        width: 360px;
                        height: 650px;
                        bottom: 0;
                        right: 0;
                        left: auto;
                        top: auto;
                        position: fixed;
                        z-index: 999999999;
                        display: none;
                    }
                    #lootly-widget.widget-closed {
                        width: 305px;
                        height: 50px;
                    }
                    #lootly-widget.widget-closed.widget-has-rewards {
                        height: 175px;
                    }
                    #lootly-widget.widget-closed.widget-has-rewards.widget-reward-redeemed {
                        height: 360px;
                    }
                    #lootly-widget.widget-ready {
                        display: block !important;
                    }
                    #lootly-widget.widget-right {
                        margin-right: ${sideSpacing}px;
                        right: 0;
                    }
                    #lootly-widget.widget-left {
                        margin-left: ${sideSpacing}px;
                        left: 0;
                    }
                    #lootly-widget iframe {
                        width: 100%;
                        height: 100%;
                        border: none;
                        background: transparent;
                        box-shadow: none;
                        opacity: 1;
                        max-width: 100%;
                        margin: 0;
                    }
                    #lootly-widget:not(.widget-closed)[data-current-page^="/widget/referral-receiver"] {
                        height: 420px !important;
                    }
                    #lootly-widget.widget-closed[display-on="none"] {
                        height: 0 !important;
                    }
                    @media(max-width: 600px) {
                        #lootly-widget {
                            width: 60px;
                        }
                        #lootly-widget.widget-closed {
                            width: 50px;
                        }
                        #lootly-widget:not(.widget-closed) {
                            margin: 0;
                            width: 100%;
                            max-height: none;
                            height: 100% !important;
                            max-width: none;
                        }
                        #lootly-widget:not(.widget-closed)[data-current-page^="/widget/referral-receiver"] {
                            box-shadow: 0 0 10px 0 rgba(0,0,0,.12);
                        }                        
                        #lootly-widget.widget-closed.widget-has-rewards {
                            width: 100%;
                            max-width: 100%;
                        }
                        #lootly-widget[display-on="desktop-only"] {
                            display: none !important;
                        }
                        .lootly-widget-open {
                            overflow: hidden;
                        }
                    }
                `

                let widgetMessageData = {
                    styles: styles,
                    position: tabSettings.position || 'right',
                    display_on: tabSettings.display_on || 'desktop-mobile'
                }
                this.sendMessageFromWidget(JSON.stringify({ action: 'widget-ready', data: widgetMessageData }))
            }).catch((error) => {
                console.log(error)
            })

        },
        getVipSettings: function() {
            let _this = this;
            new Promise((resolve, reject) => {
                axios.post('/api/widget/vip-settings', this.query).then((result) => {
                    if (result.data) {
                        let vipSettings = result.data;
                        _this.vipSettings.programStatus = vipSettings.program_status == 'Enabled' ? 1 : 0;
                        if (vipSettings.requirement_type) _this.vipSettings.requirementType = vipSettings.requirement_type;
                    }
                }).catch(error => {
                    console.log(error.response)
                    reject()
                }).then(() => {
                    resolve()
                })
            })
        },
        authRedirect: function() {
            if (this.$router.currentRoute.name == 'login') {
                if (this.isLogin) {
                    router.replace({ path: '/widget/auth' })
                }
            }
        },
        platformDefaultLinks: function () {
            switch (this.platform.type) {
                case 'magento':
                    this.platform.signupLink = '/customer/account/create/';
                    this.platform.loginLink = '/customer/account/login/';
                    break;

                case 'woocommerce':
                    this.platform.signupLink = '/my-account/';
                    this.platform.loginLink = '/my-account/';
                    break;

                case 'bigcommerce':
                    this.platform.signupLink = '/login.php';
                    this.platform.loginLink = '/login.php?action=create_account';
                    break;

                default:
                    this.platform.signupLink = '/account/register';
                    this.platform.loginLink = '/account/login';
            }
        },
        redirectAccountLink: function(type) {
            var page = type == 'login' ? this.platform.loginLink : this.platform.signupLink;
            var protocol = 'https://'; // Default
            var shopUrl = this.merchant.website || this.$root.query.shop.domain;

            if( shopUrl.startsWith('stores/') ) {
                shopUrl = 'https://store-' + shopUrl.slice(7,17) + '.mybigcommerce.com/';
                page = 'login.php';
                if( type === 'signup' ) {
                    page += '?action=create_account';
                }
            } else {
                shopUrl = (shopUrl.indexOf('://') === -1) ? protocol + shopUrl : shopUrl;
            }

            window.top.location.href = shopUrl + page;
        },
        bindEvent: function(element, eventName, eventHandler) {
            if (element.addEventListener) {
                element.addEventListener(eventName, eventHandler, false)
            } else if (element.attachEvent) {
                element.attachEvent('on' + eventName, eventHandler)
            }
        },
        sendMessageFromWidget: function(message) {
            if(message == 'close-widget') this.widgetOpened = false;
            if(message == 'open-widget') this.widgetOpened = true;
            window.parent.postMessage(message, '*')
        },
        openReceiveReferralRewardPage: function(data) {
            let referralSlug = ''
            try {
                referralSlug = data.referral_slug || ''
            } catch (e) {}

            router.replace({ path: '/widget/referral-receiver/' + referralSlug })
            this.sendMessageFromWidget('open-widget');

        },
        openEarnPointsPage: function(data) {
            if(!this.isLogin) return this.redirectAccountLink('login')
            router.replace({ path: '/widget/my-points/2' })
            this.sendMessageFromWidget('open-widget')
        },
        openSpendPointsPage: function(data) {
            if(!this.isLogin) return this.redirectAccountLink('login')            
            router.replace({ path: '/widget/my-points/1' })
            this.sendMessageFromWidget('open-widget')
        },
        openMyRewardsPage: function(data) {
            if(!this.isLogin) return this.redirectAccountLink('login')            
            router.replace({ path: '/widget/my-rewards/' })
            this.sendMessageFromWidget('open-widget')
        },
        updateLoggedInWidget: function() {
            // Redirect to a new page then redirect back to home (There should be a better way to do that)
            router.replace({ path: '/widget/my-points/1'})
            setTimeout(function(){
                router.replace({ path: '/widget/auth'})
            }, 500)
        },
        toggleWidget: function() {
            this.widgetOpened = !this.widgetOpened;
            this.sendMessageFromWidget(this.widgetOpened ? 'open-widget' : 'close-widget')
        }
    },
    watch: {
        '$route': function() {
            this.authRedirect()
        }
    },
    mounted() {
        const vm = this
        this.sendMessageFromWidget('widget-init')
        EventBus.$on('messageFromIframe', function(message) {
            vm.sendMessageFromWidget(message)
        })

        this.bindEvent(window, 'message', function(e) {
            let type = e.data
            let data = {}
            try {
                let eventBody = JSON.parse(e.data)
                type = eventBody.action
                data = eventBody.data
            } catch (e) {}

            switch (type) {
                case 'receive-reward':
                    vm.openReceiveReferralRewardPage(data)
                    break
                case 'earn-points':
                    vm.openEarnPointsPage(data)
                    break
                case 'get-coupon':
                    vm.openSpendPointsPage(data)
                    break
                case 'viewport-width':
                    vm.viewportWidth = data['width'];
                    break
                case 'toggle-widget':
                    vm.toggleWidget()
                    break
            }
        })
    }

}).$mount('#app')

router.beforeEach((to, from, next) => {
    app.fromRoute = from.path
    next()
})

router.afterEach((to, from, next) => {
    app.sendMessageFromWidget(JSON.stringify({ action: 'add-current-page', data: to.path }))
})

function findGetParameter(parameterName) {
    var result = null,
        tmp = []
    var items = location.search.substr(1).split('&')
    for (var index = 0; index < items.length; index++) {
        tmp = items[index].split('=')
        if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1])
    }
    return result
}
