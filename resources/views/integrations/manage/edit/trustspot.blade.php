@extends('layouts.app')

@section('title', 'Manage Integrations')

@section('content')
    <div id="integration-settings" class="m-t-20 m-b-10">
        <b-alert v-cloak
                 :show="alert.dismissCountDown"
                 dismissible
                 :variant="alert.type"
                 @dismissed="alert.dismissCountdown=0"
                 @dismiss-count-down="countDownChanged">
            @{{alert.text}}
        </b-alert>

        <form id="" :class="{ 'loading' : loading }" v-cloak>
            <div class="row m-t-20 p-b-10 section-border-bottom">
                <div class="col-md-12 m-b-15">
                    <a href="{{ route('integrations.manage') }}" class="bold f-s-15 color-blue">
                        <i class="arrow left blue"></i>
                        <span class="m-l-5">Apps</span>
                    </a>
                </div>
                <div class="col-md-6 col-6">
                    <h3 class="page-title m-t-0 color-dark">
                        <span v-text="app.name"></span>
                    </h3>
                </div>
                <div class="col-md-6 col-6 text-right ">
                    <button class="btn btn-save" @click.prevent="saveSetting">Save</button>
                </div>
            </div>
            <div class="row p-t-25 p-b-25 section-border-bottom">
                <div class="col-md-5 col-12">
                    <h5 class="bolder m-b-15">
                        <span v-text="app.name"></span> Connection
                    </h5>
                </div>
                <div class="col-md-7 col-12">
                    <div class="well bg-white">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group m-b-0">
                                    <label class="light-font m-b-0 m-t-5">
                                        <span v-text="app.name"></span> is
                                        <span class="bold" v-if="app.status == 1">Connected</span>
                                        <span class="bold" v-if="app.status == 0">Not connected</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <a @click="toogleStatus" v-cloak>
                                    <span v-if="wasConnected == true">
                                        <span v-if="app.status == 0">
                                            <span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
                                        </span>
                                        <span v-else>
                                            <span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
                                        </span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row p-t-25 p-b-25">
                <div class="col-md-5 col-12">
                    <h5 class="bolder m-b-15">API Credentials</h5>
                    <p class="m-b-10">Input your TrustSpot API Credentials, in order to be notified when a customer writes a review for your business.</p>
                </div>
                <div class="col-md-7 col-12">
                    <div class="well bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group m-b-5">
                                    <label class="light-font m-b-0 m-t-5">
                                        TrustSpot API Key
                                    </label>
                                </div>
                                <input class="form-control" placeholder=""
                                    v-model="app.credentials.api_key">
                            </div>
                        </div>
                        <div class="row m-t-10 m-b-10">
                            <div class="col-md-12">
                                <div class="form-group m-b-5">
                                    <label class="light-font m-b-0 m-t-5">
                                        TrustSpot Secret Key
                                    </label>
                                </div>
                                <input type="password" class="form-control" placeholder=""
                                    v-model="app.credentials.secret_key">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
      var page = new Vue({
        el: '#integration-settings',
        data: {
          loading: true,
          saving: false,
          wasConnected: false,
          app: {
            name: '',
            status: 0,
            credentials: {
                api_key: '',
                secret_key: '',
            },
          },
          alert: {
            type: '',
            text: '',
            dismissSecs: 5,
            dismissCountDown: 0
          },
        },
        created: function () {
          this.getData()
        },
        methods: {
          getData: function () {
            this.loading = true
            axios.get('/api/merchants/' + Spark.state.currentTeam.id + '/integrations/' +{{$id}}).then(result => {
              try {
                let integration = result.data.data
                this.app.name = integration.integration.title
                this.app.status = integration.status
                if(integration.settings){
                  if(integration.settings.credentials){
                    this.app.credentials = integration.settings.credentials;
                  }
                }
                if(integration.status){
                    this.wasConnected = true;
                }
              } catch (e) {}
            }).catch(error => {

            }).then(() => {
                if(!this.app.name){
                    this.app.name = 'TrustSpot';
                }
                this.loading = false
            })
          },
          saveSetting () {
            if (!this.saving) {
              this.saving = true
              axios.put('/api/merchants/' + Spark.state.currentTeam.id + '/integrations/' +{{$id}}, this.app).then(result => {
                this.alert.text = this.app.name + ' settings saved successfully!'
                this.alert.type = 'success'
              }).catch(error => {
                clearErrors(this.$el)
                console.log(error.response.data.errors)
                showErrors(this.$el, error.response.data.errors)
                this.alert.type = 'danger'
                this.alert.text = error.response.data.message
              }).then(() => {
                this.saving = false
                this.alert.dismissCountDown = this.alert.dismissSecs
              })
            }
          },
          countDownChanged (dismissCountDown) {
            this.alert.dismissCountDown = dismissCountDown
          },
          toogleStatus () {
            this.app.status ? this.app.status = 0 : this.app.status = 1;
          }
        }
      })
    </script>
@endsection