var base = require('auth/register-braintree')

Vue.component('spark-register-braintree', {
  mixins: [base],

  data: function () {
    return {
      registerForm: $.extend(true, new SparkForm({
        braintree_type: '',
        braintree_token: '',
        plan: '',
        team: '',
        team_slug: '',
        token: '',
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        terms: false,
        coupon: null,
        invitation: null
      }), Spark.forms.register)
    }
  }
})
