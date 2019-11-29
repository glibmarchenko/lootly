import HomeGuest from './components/home-guest.vue'
import HomeLogin from './components/home-login.vue'

import pointsActions from './components/points-actions.vue'
import vipInfo from './components/vip-info.vue'
import referralReceiver from './components/referral-receiver.vue'
import getCoupon from './components/get-coupon.vue'
import showCoupon from './components/show-coupon.vue'
import myPoints from './components/my-points.vue'
import myRewards from './components/my-rewards.vue'
import howItWorks from './components/how-it-works.vue'
import referralEmail from './components/referral-email.vue'
import birthday from './components/birthday.vue'
import variableDiscount from './components/variable-discount.vue'
import vip from './components/vip.vue'

export const routes = [
  {path: '/widget', component: HomeGuest, name: 'login'},
  {path: '/widget/auth', component: HomeLogin, name: 'home'},
  {path: '/widget/points-actions/:tabIndex?', component: pointsActions, name: 'points-actions', props: true},
  {path: '/widget/vip-info/:id?', component: vipInfo, name: 'vip-info', props: true},
  {path: '/widget/referral-receiver/:referral?', component: referralReceiver, name: 'referral-receiver', props: true},
  {path: '/widget/get-coupon/:couponId?/:variableAmount?', component: getCoupon, name: 'get-coupon', props: true},
  {path: '/widget/show-coupon/:couponCode?', component: showCoupon, name: 'show-coupon', props: true},
  {path: '/widget/my-rewards', component: myRewards, name: 'my-rewards'},
  {path: '/widget/my-points/:tabIndex?', component: myPoints, name: 'my-points', props: true},
  {path: '/widget/how-it-works', component: howItWorks, name: 'how-it-works'},
  {path: '/widget/referral-email', component: referralEmail, name: 'referral-email', props: true},
  {path: '/widget/birthday', component: birthday, name: 'birthday'},
  {path: '/widget/vip', component: vip, name: 'vip'},
  {path: '/widget/variable-discount/:rewardId', component: variableDiscount, name: 'variable-discount', props: true},]

