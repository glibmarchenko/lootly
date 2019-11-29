@extends('tmp.app')

@section('title', 'Select Account')

@section('content')
    <div id="select-account-page" class="" :class="{'loading' : pageLoading}">

        <div class="row m-t-20 p-b-10 section-border-bottom">
            <div class="col-md-12">
                <h3 class="page-title m-t-0 color-dark">Select Store</h3>
            </div>
        </div>
        <b-alert :show="alert.dismissCountDown"
                 dismissible
                 :variant="alert.type"
                 @dismissed="alert.dismissCountdown=0"
                 @dismiss-count-down="countDownChanged" style="margin-top: 15px; margin-bottom: 0;" v-cloak>
            @{{alert.text}}
        </b-alert>
        <div class="row p-t-25 m-b-20 p-b-25 section-border-bottom" :class="{'loading' : loading}" v-cloak>
            <div class="col-md-12">
                <ul style="list-style: none;">
                    <li v-for="store in stores" style="cursor: pointer;padding: 5px 0;"
                        @click.prevent="selectStore(store)">
                        <div>
                            <span v-text="store.name"></span>
                            <span v-if="store.shopify_installed">(Connected)</span>
                        </div>
                    </li>
                    <li style="margin-top: 15px;"><a v-b-modal.create-account-modal><i class="icon-add"></i> Create New Store</a></li>
                </ul>
            </div>
        </div>
        <div style="text-align: center;padding-bottom: 20px;">
            <a href="/logout">Switch
                user</a>
        </div>
        @include('_partials._create-account', ['switchOnSuccess' => 1])
    </div>
@endsection
@section('scripts')
    <script>
      var page = new Vue({
        el: '#select-account-page',
        data: {
          stores: Spark.state.teams || [],
          currentStore: Spark.state.currentTeam || null,
          loading: false,
          pageLoading: false,
          alert: {
            type: '',
            text: '',
            dismissSecs: 5,
            dismissCountDown: 0
          },
        },
        created: function () {
          //this.getStores()
        },
        methods: {
          getStores: function () {
            this.loading = true
            let that = this
            axios.get('/settings/store/show').then(response => {
              that.stores = response.data.merchants
              that.currentStore = response.data.current_store
            }).catch((error) => {
              //
            }).then(() => {
              that.loading = false
            })
          },
          selectStore (store) {
            this.pageLoading = true
            axios.get('/current/merchant/' + store.id).then(response => {
              let redirectTo = '<?php echo (session('redirect_queue') && count(session('redirect_queue'))) ? session('redirect_queue')[0] : ''; ?>'
              if (redirectTo) {
                if(redirectTo.indexOf('?') < 0) {
                  location.href = redirectTo + '?merchant_id=' + store.id;
                }else{
                  location.href = redirectTo + '&merchant_id=' + store.id;
                }
              }else{
                location.href = '/';
              }
            }).catch(error => {
              this.pageLoading = false
              this.alert.type = 'danger'
              this.alert.text = 'Unexpected error during Lootly account select. Please, reload page and try again.'
              this.alert.dismissCountDown = this.alert.dismissSecs
            }).then(() => {

            })
          },
          countDownChanged: function (dismissCountDown) {
            this.alert.dismissCountDown = dismissCountDown
          },
        }
      })

    </script>
@endsection
