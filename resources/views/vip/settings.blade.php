@extends('layouts.app')

@section('title', 'VIP Settings')

@section('content')
    <div id="vip-settings" class="loader m-t-20 m-b-10" v-cloak>
        <b-alert v-cloak
                 :show="alert.dismissCountDown"
                 dismissible
                 :variant="alert.type"
                 @dismissed="alert.dismissCountdown=0"
                 @dismiss-count-down="countDownChanged">
            @{{alert.text}}
        </b-alert>
        <form id="">
            <div class="row m-t-20 p-b-10 section-border-bottom">
                <div class="col-md-6 col-12">
                    <h3 class="page-title m-t-0 color-dark">VIP Settings</h3>
                </div>

                <div class="col-md-6 col-12 text-right ">
                    <button class="btn btn-save" @click.prevent="saveSetting">Save</button>
                </div>
            </div>
            <div :class="{'row': true, 
                          'p-t-25': true, 
                          'p-b-25': true, 
                          'section-border-bottom': true,
                          'loading': loading}">
                <div class="col-md-5 col-12">
                    <h5 class="bolder m-b-15">Program Status</h5>
                    <p class="m-b-0">Select to enable or disable the VIP program for your store.</p>
                </div>
                <div class="col-md-7 col-12">
                    <div class="well bg-white">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group m-b-0">
                                    <label class="light-font m-b-0 m-t-5">
                                        Your VIP program is 
                                        <span class="bold" v-text="temp.program_status"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <a @click="toogleProgramStatus" v-cloak>
                                <span v-if="temp.program_status == 'Disabled'">
                                    <span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
                                </span>
                                    <span v-else>
                                    <span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div :class="{'row': true, 
                          'p-t-25': true, 
                          'p-b-25': true,
                          'd-none': loading}">
                <div class="col-md-5 col-12">
                    <h5 class="bolder m-b-15">Tier Options</h5>
                    <p class="m-b-10">Select the requirement for a customer to gain access to any tier.</p>
                    <p>You can also specify the earning period for a customer to maintain their tier status. As long as
                        the customer is spending enough or earning enough points within this time period, they will keep
                        their VIP status</p>
                </div>
                <div class="col-md-7 col-12">
                    <div class="well bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group m-b-5">
                                    <label class="light-font m-b-0 m-t-5">
                                        Requirement Type
                                    </label>
                                </div>
                                <b-form-select v-model="temp.requirement_type">
                                    <option value="amount-spent">Amount Spent</option>
                                    <option value="points-earned">Points Earned</option>
                                </b-form-select>
                            </div>
                        </div>
                        <div class="row m-t-10 m-b-10">
                            <div class="col-md-12">
                                <div class="form-group m-b-5">
                                    <label class="light-font m-b-0 m-t-5">
                                        Earning Rolling Period
                                    </label>
                                </div>
                                <b-form-select v-model="temp.rolling_period">
                                    <option value="1-month">1 Month</option>
                                    <option value="2-month">2 Months</option>
                                    <option value="3-month">3 Months</option>
                                    <option value="4-month">4 Months</option>
                                    <option value="5-month">5 Months</option>
                                    <option value="6-month">6 Months</option>
                                    <option value="7-month">7 Months</option>
                                    <option value="8-month">8 Months</option>
                                    <option value="9-month">9 Months</option>
                                    <option value="10-month">10 Months</option>
                                    <option value="11-month">11 Months</option>
                                    <option value="1-year">1 Year</option>
                                    <option value="2-year">2 Years</option>
                                    <option value="0">No Earning Period</option>
                                </b-form-select>
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
            el: "#vip-settings",
            data: {

                temp: {
                    program_status: 'Disabled',
                    requirement_type: 'amount-spent',
                    rolling_period: '1-year'
                },
                alert: {
                    type: '',
                    text: '',
                    dismissSecs: 5,
                    dismissCountDown: 0
                },
                loading: true,
            },
            created: function () {
                this.getData();
            },
            methods: {
                getData: function () {
                    axios.get('{{ route("vip.settings.data") }}').then((response) => {
                        // if (response.data.vipSetting.length === 0) {
                        if (!response.data.vipSetting) {

                        } else {
                            this.temp = response.data.vipSetting;
                        }
                        this.loading = false;
                    }).catch((error) => {
                        clearErrors(this.$el);
                        this.loading = false;
                        this.alert.text = 'Error getting data';
                        this.alert.type = 'danger';
                        this.alert.dismissCountDown = this.alert.dismissSecs;
                        console.log("!!!error!!!", error);
                    });
                },
                saveSetting() {
                    this.loading = true;
                    axios.post('/vip/settings/edit', this.temp).then(response => {
                        this.alert.text = 'Settings saved successfully';
                        this.alert.type = 'success';
                        this.alert.dismissCountDown = this.alert.dismissSecs;
                        this.loading = false;
                    }).catch(errors => {
                        this.loading = false;
                        clearErrors(this.$el);
                        showErrors(this.$el, errors.response.data.errors);
                    });
                },
                toogleProgramStatus: function () {
                    if (this.temp.program_status == 'Disabled') {
                        this.temp.program_status = 'Enabled';
                    } else {
                        this.temp.program_status = 'Disabled';
                    }
                },
                countDownChanged(dismissCountDown) {
                    this.alert.dismissCountDown = dismissCountDown
                },
            }
        })
    </script>
@endsection
