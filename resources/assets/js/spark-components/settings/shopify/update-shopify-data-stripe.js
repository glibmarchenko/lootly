Vue.component('spark-update-sopify', {
  props: ['user', 'user_detail'],

  /**
   * The component's data.
   */
  data () {
    return {
      form: $.extend(true, new SparkForm({
        shop_domain: '',
        api_key: '',
        api_secret: '',
        user_id: ''
      }), Spark.forms.updateShopify),
      webhook_url: null
    }
  },

  /**
   * Bootstrap the component.
   */
  mounted () {
    this.form.user_id = this.user.id

  },

  methods: {
    /**
     * Update the user's shopify.
     */
    update () {
      Spark.post('/settings/shopify', this.form)
        .then(() => {

          // Bus.$emit('updateShopify');
          this.connectShopify()

        })
    },
    connectShopify (data) {
      Spark.post('/settings/shopify/connect', this.form)
        .then((data) => {
          this.webhook_url = data.auth_url

        })
    },
    createWebhook () {
      Spark.post('/settings/shopify/webhook', this.form)
        .then((data) => {

        })
    },
    getAllUsers () {
      Spark.post('/settings/shopify/getEmployee', this.form)
        .then((data) => {

        })
    }

  }
})
