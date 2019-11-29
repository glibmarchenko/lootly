var base = require('auth/register-stripe')

Vue.component('spark-register-stripe', {
  mixins: [base],

  data: function () {
    return {
      registerForm: $.extend(true, new SparkForm({
        stripe_token: '',
        plan: '',
        team: '',
        team_slug: '',
        token: '',
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        address: '',
        address_line_2: '',
        city: '',
        state: '',
        zip: '',
        country: 'US',
        vat_id: '',
        terms: false,
        coupon: null,
        invitation: null
      }), Spark.forms.register),
    }
  }
})
