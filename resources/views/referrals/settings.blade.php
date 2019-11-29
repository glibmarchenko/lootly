@extends('layouts.app')

@section('title', 'Referrals Settings')

@section('content')
    <div id="referrals-settings" class="loader m-t-20 m-b-10" :class="{'loading': loading}" v-cloak >
        <b-alert v-cloak
                 :show="alert.dismissCountDown"
                 dismissible
                 :variant="alert.type"
                 @dismissed="alert.dismissCountdown=0"
                 @dismiss-count-down="countDownChanged">
            @{{alert.text}}
        </b-alert>
        <form id="">
            {{--{{ csrf_field() }}--}}

            <div class="row m-t-20 p-b-10 section-border-bottom">
                <div class="col-md-6 col-12">
                    <h3 class="page-title m-t-0 color-dark">Referrals Settings</h3>
                </div>

                <div class="col-md-6 col-12 text-right ">
                    <span v-if="saving" class="i-loading"></span>
                    <button v-show="!saving" class="btn btn-save" @click.prevent="saveSetting">Save</button>
                </div>
            </div>
            <div class="row p-t-25 p-b-25 section-border-bottom">
                <div class="col-md-5 col-12">
                    <h5 class="bolder m-b-15">Program Status</h5>
                    <p class="m-b-0">Select to enable or disable the referrals program for your store.</p>
                </div>
                <div class="col-md-7 col-12">
                    <div class="well">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group m-b-0">
                                    <label class="light-font m-b-0 m-t-5">
                                        Your referrals program is currently
                                        <span class="bold" v-text="temp.programStatus_text"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 text-right" name="program_status">
                                <a @click="toogleProgramStatus" v-cloak>
                                <span v-if="temp.programStatus == 0">
                                    <span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
                                </span>
                                    <span v-else>
                                    <span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
                                </span>
                                </a>
                            </div>
                            {{--<span class="alert-danger form-control" v-if="errors.programStatus">--}}
                            {{--@{{ errors.programStatus[0] }}--}}
                            {{--</span>--}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row p-t-25 p-b-25 section-border-bottom">
                <div class="col-md-5 col-12">
                    <h5 class="bolder m-b-15">Landing Page</h5>
                    <p class="m-b-10">Enter in the URL that you would like to send your referred customers to.</p>
                </div>
                <div class="col-md-7 col-12">
                    <div class="well">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group m-b-5">
                                    <label class="light-font m-b-0 m-t-5">
                                        URL
                                    </label>
                                </div>
                                <input class="form-control" type="text" v-model="temp.url"
                                       placeholder="e.g. http://www.your-website.com">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row p-t-25 p-b-25">
                <div class="col-md-5 col-12">
                    <h5 class="bolder m-b-15">Custom Referral Domain</h5>
                    <p class="m-b-10">By default Lootly uses it's own domain for your referral links. You can customize this by entering in your own domain name here.</p>
                </div>
                <div class="col-md-7 col-12">
                    <!-- User doesn't have access  -->
                    @if(!$have_domain_permissions)
                        <no-access :loading="loading"
                            title="{{$domain_upsell->upsell_title}}"
                            desc="{{$domain_upsell->upsell_text}}"
                            icon="{{$domain_upsell->upsell_image}}"
                            plan="{{$domain_upsell->getMinPlan()->name}}"></no-access>
                    @else
                        <!-- User has access  -->
                        <div class="well">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group m-b-0">
                                        <label class="light-font m-b-0 m-t-5">
                                            Custom Referral Domain is
                                            <span class="bold" v-text="temp.customDomainStatus_text"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 text-right" name="referral_domain">
                                    <a @click="toogleCustomDomainStatus" v-cloak>
                                    <span v-if="temp.customDomainStatus == 0">
                                        <span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
                                    </span>
                                        <span v-else>
                                        <span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
                                    </span>
                                    </a>
                                </div>
                            </div>
                            <div class="row m-t-5">
                                <div class="col-md-12">
                                    <div class="form-group m-b-5">
                                        <label class="light-font m-b-0 m-t-5">
                                            URL
                                        </label>
                                    </div>
                                    <input class="form-control" type="text" v-model="temp.customDomain"
                                        placeholder="e.g. http://www.your-website.com">
                                    <p class="m-t-10">
                                        To ensure this is setup correctly, please refer to our <a class="color-blue"
                                                                                                href="">setup guide</a>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        var page = new Vue({
            el: "#referrals-settings",
            data: {
                access: false,
                temp: {
                    programStatus: '',
                    customDomainStatus: '',
                    programStatus_text:'Disabled',
                    customDomainStatus_text:'',
                    customDomain: '',
                    url: ''
                },
                alert: {
                    type: '',
                    text: '',
                    dismissSecs: 5,
                    dismissCountDown: 0
                },
                errors: '',
                text: '',
                loading: true,
                saving: false
            },
            created: function () {
                this.getData();
            },
            methods: {
                getData: function () {
                    axios.get('/settings/referral/data').then(response => {
                        if (response.data.referral) {
                            let data=response.data.referral
                            this.temp.programStatus=data.program_status;
                            this.temp.customDomainStatus=data.referral_domain_status;
                            this.temp.url=data.referral_link;
                            this.temp.customDomain=data.referral_domain;
                            if(data.referral_domain_status == 0)
                            {
                                this.temp.customDomainStatus_text='Disabled'
                            } else {
                                this.temp.customDomainStatus_text='Enabled'
                            }
                            if(data.program_status == 0)
                            {
                                this.temp.programStatus_text='Disabled'
                            } else {
                                this.temp.programStatus_text='Enabled'
                            }
                        }
                        
                        this.loading = false;
                    }).catch((error) => {
                        console.log(error)
                        this.text = '';
                        clearErrors(this.$el);
                        showErrors(this.$el, error.response.data.errors);

                    });
                },
                saveSetting() {
                    this.saving = true;
                    axios.post('/settings/referral/edit', this.temp).then(() => {
                        this.alert.text = 'Settings saved successfully';
                        this.alert.type = 'success';
                        this.alert.dismissCountDown = this.alert.dismissSecs;
                        this.saving = false;
                    }).catch(errors => {
                        clearErrors(this.$el);
                        showErrors(this.$el, errors.response.data.errors);
                    });
                },

                toogleProgramStatus: function () {
                    if (this.temp.programStatus == 0) {
                        this.temp.programStatus = 1;
                        this.temp.programStatus_text='Enabled'
                    } else {
                        this.temp.programStatus = 0;
                        this.temp.programStatus_text='Disabled'
                    }
                },
                toogleCustomDomainStatus: function () {
                    if (this.temp.customDomainStatus == 0) {
                        this.temp.customDomainStatus = 1;
                        this.temp.customDomainStatus_text='Enabled'

                    } else {
                        this.temp.customDomainStatus = 0;
                        this.temp.customDomainStatus_text='Disabled'
                    }
                },
                countDownChanged(dismissCountDown) {
                    this.alert.dismissCountDown = dismissCountDown
                },

            },

        })
    </script>
@endsection