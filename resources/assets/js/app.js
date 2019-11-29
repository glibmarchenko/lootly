/*
 |--------------------------------------------------------------------------
 | Main App Scripts
 |--------------------------------------------------------------------------
*/

require('./bootstrap');
window.Vue = require('vue');

import BootstrapVue from 'bootstrap-vue';
import Chart from 'chart.js'
import moment from 'moment';
import momentTZ from 'moment-timezone';
import swal from 'sweetalert';

require('./forms/form');
require('./forms/errors');

window.Bus = new Vue();
window['moment'] = moment;
Vue.use(BootstrapVue);

/*
 | Global for all Pages 
 | i.e. Navbar, Sidebar, Components .. etc
*/

Vue.component('create-account', require('./components/create-account.vue'))
Vue.component('sortable-table', require('./components/sortable-table.vue'))
Vue.component('custom-table', require('./components/custom-table.vue'))
Vue.component('canvas-line-chart', require('./components/canvas-line-chart.vue'))
Vue.component('input-number', require('./components/input-number.vue'))
Vue.component('no-access', require('./components/no-access.vue'))
Vue.component('save-button', require('./components/save-button.vue'))
Vue.component('widget-preview', require('./components/widget-preview.vue'))
Vue.component('update-payment-method', require('./components/update-payment-method.vue'))


/* Points & referrals Previews */
Vue.component('points-reward-preview', require('./components/points/points-reward-preview.vue'))
Vue.component('referrals-reward-preview', require('./components/referrals/referrals-reward-preview.vue'))
Vue.component('referrals-reward-notify-preview', require('./components/referrals/referrals-reward-notify-preview.vue'))
Vue.component('referrals-overview-rewards', require('./components/referrals/referrals-overview-rewards.vue'))
Vue.component('referrals-reward-details', require('./components/referrals/referrals-reward-details.vue'))
Vue.component('referrals-reward-design', require('./components/referrals/referrals-reward-design.vue'))
Vue.component('referrals-reward-coupon-expiration', require('./components/referrals/referrals-reward-coupon-expiration.vue'))
Vue.component('points-link-back', require('./components/points-link-back.vue'))
Vue.component('custom-modal', require('./components/custom-modal.vue'))


/* Rewards Page Components */
require('./widgets/rewards-page/_components');

require('./global');

if (process.env.MIX_APP_ENV === 'production') {
    Vue.config.devtools = false;
    Vue.config.debug = false;
    Vue.config.silent = true;
}
