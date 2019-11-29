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
                    <a href="https://documenter.getpostman.com/view/4144738/SVmtxeUY?version=latest" target="_blank" class="btn btn-secondary btn-view-doc">
                        View Documentation
                    </a>
                    <button class="btn btn-save" @click.prevent="saveSetting">
                        Save
                    </button>
                </div>
            </div>
            <div class="row p-t-25 p-b-25 section-border-bottom">
                <div class="col-md-5 col-12">
                    <h5 class="bolder m-b-15">
                        API Status
                    </h5>
                </div>
                <div class="col-md-7 col-12">
                    <div class="well bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group m-b-0">
                                    <label class="light-font m-b-0 m-t-5">
                                        API Status is
                                        <span class="bold" v-if="app.apiStatus">Enabled</span>
                                        <span class="bold" v-if="! app.apiStatus">Disabled</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row p-t-25 p-b-25">
                <div class="col-md-5 col-12">
                    <h5 class="bolder m-b-15">API Settings</h5>
                    <p class="m-b-10">Select to enable or disable the Lootly API. By enabling the API all other eCommerce platform connections are disabled and manual code upload is now available.</p>
                </div>
                <div class="col-md-7 col-12">
                    <div class="well bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group m-b-5">
                                    <label class="light-font m-b-0 m-t-5">
                                        API Status
                                    </label>
                                </div>
                                <b-form-checkbox class="w-100 m-t-10" name="settings.status"
                                                 v-model="app.settings.status"
                                                 value="1"
                                                 unchecked-value="0">
                                    Enable API & Manual Upload
                                </b-form-checkbox>
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
                merchantId: Spark.state.currentTeam.id,
                app: {
                    name: 'Custom API',
                    settings: {
                        status: 0,
                    },
                    apiStatus: 0,
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
                    axios.get('/api/merchants/' + Spark.state.currentTeam.id + '/integrations/' + '{{ $id }}').then(result => {
                        let integration = result.data.data;
                        if (integration && integration.integration) {
                            this.app.settings.status = parseInt(integration.status);
                            this.app.apiStatus = parseInt(integration.status);
                        }
                    }).then(() => {
                        this.loading = false;
                    })
                },
                saveSetting: function () {
                    if (! this.saving) {
                        this.saving = true;

                        axios.put('/api/merchants/' + Spark.state.currentTeam.id + '/integrations/' + '{{ $id }}', this.app.settings).then(result => {
                            this.alert.text = this.app.name + ' settings saved successfully!';
                            this.alert.type = 'success';

                        }).then(() => {
                            this.saving = false;
                            this.alert.dismissCountDown = this.alert.dismissSecs;
                            this.app.apiStatus = parseInt(this.app.settings.status);

                        }).catch(error => {
                            clearErrors(this.$el);
                            console.log(error.response.data.errors);
                            showErrors(this.$el, error.response.data.errors);
                            this.alert.type = 'danger';
                            this.alert.text = error.response.data.message;
                        })
                    }
                },
                countDownChanged(dismissCountDown) {
                    this.alert.dismissCountDown = dismissCountDown;
                },
            }
        })
    </script>
@endsection
