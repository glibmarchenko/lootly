var base = require('settings/profile/update-contact-information')

Vue.component('spark-update-contact-information', {
  mixins: [base],

  data: function () {
    return {
      form: $.extend(true, new SparkForm({
        first_name: '',
        last_name: '',
        billing_email: ''
      }), Spark.forms.updateContactInformation)
    }
  },
  mounted: function () {
    this.form.first_name = this.user.first_name
    this.form.last_name = this.user.last_name
    this.form.billing_email = this.user.billing_email
  },
})
