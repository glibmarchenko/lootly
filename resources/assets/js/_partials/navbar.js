/*
* Navbar Vue scripts 
*/
if (document.querySelector('#main-navbar')) {
  var navbar = new Vue({
    el: '#main-navbar',
    data: {
      user: Spark.state.user,
      stores: Spark.state.teams,
      currentStore: Spark.state.currentTeam,
      user_name: Spark.state.user.first_name.charAt(),

      //user_name: '',
      announcement_count: '',
      created_announcement: '',
      //stores: '',
      announcements: '',
      //currentStore: '',
      auth_url: '',
      success: '',
    },
    created: function () {
      this.getStore()
      this.elm = $('#main-navbar').parent().find('#account-settings')
    },
    mounted () {
    },

    methods: {
      getStore: function () {
        /*axios.get('/settings/store/show').then(response => {
          this.stores = response.data.merchants
          this.currentStore = response.data.current_store
          this.user_name = this.currentStore.first_name.charAt()
        })*/
        axios.get('/notifications/recent').then(response => {
          this.announcements = response.data.notifications
          this.announcement_count = this.announcements.filter(item => {
            return !item.read
          }).length || 0

          this.replaceCreatedAt(this.announcements)

        })

      },
      removeAnnouncement: function (id) {
        axios.put('/notifications/read', {notifications: [id]}).then(response => {
          //this.announcements = response.data.announcement;
          $('#' + id).hide()

        }).catch(() => {

        })
      },
      replaceCreatedAt: function (announcements) {
        announcements.forEach(function (value) {
          value.created_at = moment(value.created_at).fromNow()
        })
      },
      updateMerchant: function (store) {
        var store_id = store.id
        axios.get('/current/merchant/' + store_id).then(response => {
          location.reload(true)
        })
      },
      account () {
        $('#create_account').modal('show')
        // this.$root.$emit('bv::show::modal', 'create_account')
      },

      // Connect/Install Shopify App
      update () {
        Spark.post('/settings/shopify', this.form)
          .then(() => {

            // Bus.$emit('updateShopify');
            this.connectShopify()

          })
      },

      connectShopify (data) {
        axios.post('/settings/shopify/connect', this.form)
          .then((response) => {
            this.auth_url = response.data.auth_url
            this.success = 'Shopify App install successfully'
          })
      },
    }
  })
}