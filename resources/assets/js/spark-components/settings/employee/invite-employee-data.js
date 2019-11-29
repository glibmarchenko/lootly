Vue.component('invite-employee-data', {
  props: [],

  /**
   * The component's data.
   */
  data () {
    return {
      form: $.extend(true, new SparkForm({
        last_name: '',
        first_name: '',
        email: '',
      }), Spark.forms.updateShopify),
      webhook_url: null
    }
  },

  /**
   * Bootstrap the component.
   */
  mounted () {

  },

  methods: {
    /**
     * Update the user's shopify.
     */
    invite () {
      Spark.post('/settings/employee/invite', this.form)
        .then(() => {
          Bus.$emit()

        })
    },

  }
})
