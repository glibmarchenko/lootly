Vue.component('spark-update-store', {

  /**
   * The component's data.
   */
  data () {
    return {
      merchants: null,

    }
  },
  created: function () {
    this.getData()
  },

  methods: {
    getData: function () {
      const comp = this
      axios.get('http://104.131.55.81/current/store/show').then(response => {
        comp.merchants = response.data.merchants
      })

    },
    updateMerchant: function () {
      // console.log(this.merchant);
      var merchant_id = this.merchant.id
      axios.get('http://104.131.55.81/current/merchant/' + merchant_id).then(response => {

      })
    }

  }
})
