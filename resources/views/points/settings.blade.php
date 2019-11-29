@extends('layouts.app')

@section('title', 'Points Settings')

@section('content')
    <div id="points-settings" class="loader m-t-20 m-b-10" :class="{'loading': loading}" v-cloak>

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
                    <h3 class="page-title m-t-0 color-dark">Points Settings</h3>
                </div>
                <div class="col-md-6 col-12">
                    <save-button class="text-right" :saving="saving" @event="saveSetting"></save-button>
                </div>
            </div>
            <div class="row p-t-25 p-b-25 section-border-bottom">
                <div class="col-md-5 col-12">
                    <h5 class="bolder m-b-15">Program Status</h5>
                    <p class="m-b-0">Select to enable or disable the points program for your store.</p>
                </div>
                <div class="col-md-7 col-12">
                    <div class="well">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group m-b-0">
                                    <label class="light-font m-b-0 m-t-5">
                                        Your points program is currently
                                        <span class="bold" v-text="temp.status"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <a @click="toogleProgramStatus" v-cloak>
                                                    <span v-if="temp.status=='Enabled'">
                                    <span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
                                                    </span>
                                    <span v-else>
                                   <span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
                                                         </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row p-t-25 p-b-25 section-border-bottom">
                <div class="col-md-5 col-12">
                    <h5 class="bolder m-b-15">Points Branding</h5>
                    <p class="m-b-0">Customize the name of your points program.</p>
                </div>
                <div class="col-md-7 col-12">
                    <div class="well">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group m-b-0">
                                    <label class="bold m-b-10">
                                        Point Name (Singular)
                                    </label>
                                    <input class="form-control"
                                           :placeholder="temp.placeholder ? temp.placeholder : 'Point'"
                                           @change="changeName()"
                                           name="name"
                                           v-model="temp.name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group m-b-0">
                                    <label class="bold m-b-10">
                                        Point Name (Plural)
                                    </label>
                                    <input class="form-control"
                                           :placeholder="temp.placeholder ? temp.placeholder : 'Points'"
                                           name="plural_name"
                                           v-model="temp.plural_name">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row p-t-25 p-b-25">
                <div class="col-md-5 col-12">
                    <h5 class="bolder m-b-15">Points Expiration</h5>
                    <p class="m-b-5">Set an expiration for your points program.</p>
                    <p class="m-b-0">
                        If a customer does not earn or spend any points after
                        this time period, they will lose their points.
                    </p>
                </div>
                <div class="col-md-7 col-12">
                    <!-- User doesn't have access  -->
                    @if(!$have_expiration_permissions)
                        <no-access :loading="loading"
                            title="{{$expiration_upsell->upsell_title}}" 
                            desc="{{$expiration_upsell->upsell_text}}" 
                            icon="{{$expiration_upsell->upsell_image}}" 
                            plan="{{$expiration_upsell->getMinPlan()->name}}"></no-access>
                    @else
                        <!-- User has access  -->
                        <span>
                            <div class="well">
                                <div class="row section-border-bottom p-b-10 m-b-15">
                                    <div class="col-md-8">
                                        <div class="form-group m-b-0">
                                            <label class="light-font m-b-0 m-t-5">
                                                Points Expiration is
                                                <span class="bold" v-text="temp.experient_status"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <a @click="tooglePointsExpiration" v-cloak>
                                            <span v-if="temp.experient_status=='Enabled'">
                                                <span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
                                            </span>
                                            <span v-else>
                                                <span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="bold m-b-10">
                                                Points Expire After
                                            </label>
                                            <b-form-select class="mb-3" name="experient_after" v-model="temp.experient_after">
                                                <option value="6" :selected="temp.experient_after == 6">6 Months</option>
                                                <option value="12" :selected="temp.experient_after == 12">1 Year</option>
                                                <option value="18" :selected="temp.experient_after == 18">1.5 Years</option>
                                                <option value="24" :selected="temp.experient_after == 24">2 Years</option>
                                            </b-form-select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="well m-t-20">
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group m-b-0">
                                            <label class="bold m-b-0 m-t-5">
                                                Reminder Email
                                                <span v-cloak>
                                                <span v-if="temp.reminder_status == 'Enable'">

                                                    <span class="badge badge-pill badge-success m-l-20 p-l-15 p-r-15">Enabled</span>
                                                </span>
                                                <span v-else>
                                                    <span class="badge badge-pill badge-danger m-l-20 p-l-15 p-r-15">Disabled</span>
                                                </span>
                                            </span>
                                            </label>
                                        </div>
                                        <p class="m-t-5">Send a reminder to your customers <span
                                                    v-text="(temp.reminder_day==1) ? temp.reminder_day +  ' day' : temp.reminder_day +  ' days'"></span>
                                            prior to expiration .</p>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <a v-b-modal.reminder-email class="bolder color-blue f-s-15">Edit</a>
                                    </div>
                                </div>
                            </div>
                            <div class="well m-t-20">
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group m-b-0">
                                            <label class="bold m-b-0 m-t-5">
                                                Final Chance Email
                                                <span v-cloak>
                                                <span v-if="temp.final_reminder_status == 'Enable'">
                                                    <span class="badge badge-pill badge-success m-l-20 p-l-15 p-r-15">Enabled</span>
                                                </span>
                                                <span v-else>
                                                    <span class="badge badge-pill badge-danger m-l-20 p-l-15 p-r-15">Disabled</span>
                                                </span>
                                            </span>
                                            </label>
                                        </div>
                                        <p class="m-t-5">Send a final reminder to your customers <span
                                                    v-text="(temp.final_reminder_day==1) ? temp.final_reminder_day + ' day' : temp.final_reminder_day + ' days' "></span>
                                            prior to expiration.</p>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <a v-b-modal.final-chance class="bolder color-blue f-s-15">Edit</a>
                                    </div>
                                </div>
                            </div>
                        </span>
                    @endif

                </div>
            </div>
        </form>
        <!-- Reminder Email Modal -->
        <b-modal class="custom-modal" hide-footer id="reminder-email" title="Reminder Email" v-cloak>
            <div class="row m-b-10 m-t-10">
                <div class="col-md-3">
                    <label class="light-font">Enable Email:</label>
                </div>
                <div class="col-md-9 p-l-0">
                    <b-form-checkbox id="ch1" v-model="temp.reminder_status" value="Enable" unchecked-value="Disable">
                        Yes, send a reminder email
                    </b-form-checkbox>
                </div>
            </div>
            <div class="row m-b-10">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="light-font">Days prior to expiration to send reminder email</label>
                        <input class="form-control" v-model="temp.reminder_day">
                    </div>
                </div>
            </div>
            <div class="row m-t-10 p-b-5 p-t-20 border-top">
                <div class="col-md-4 offset-md-4">
                    <button @click="saveReminderEmail" class="btn btn-block btn-success btn-glow">Save</button>
                </div>
            </div>
        </b-modal>

        <!-- Final Chance Email Modal -->
        <b-modal class="custom-modal" hide-footer id="final-chance" title="Final Chance Email" v-cloak>
            <div class="row m-b-10 m-t-10">
                <div class="col-md-3">
                    <label class="light-font">Enable Email:</label>
                </div>
                <div class="col-md-9 p-l-0">
                    <b-form-checkbox id="ch2" v-model="temp.final_reminder_status" value="Enable"
                                     unchecked-value="Disable">
                        Yes, send a Final Chance Email
                    </b-form-checkbox>
                </div>
            </div>
            <div class="row m-b-10">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="light-font">Days prior to expiration to send reminder email</label>
                        <input class="form-control" v-model="temp.final_reminder_day">
                    </div>
                </div>
            </div>
            <div class="row m-t-10 p-b-5 p-t-20 border-top">
                <div class="col-md-4 offset-md-4">
                    <button @click="saveFinalChance" class="btn btn-block btn-success btn-glow">Save</button>
                </div>
            </div>
        </b-modal>
    </div>
@endsection

@section('scripts')
    <script>
        var page = new Vue({
            el: "#points-settings",
            data: {
                access: false,
                temp: {
                    currency: '',
                    placeholder: '',
                    status: 'Enabled',
                    name: 'Point',
                    plural_name: 'Points',
                    experient_status: 'Disabled',
                    reminder_status: 'Disable',
                    final_reminder_status: 'Disable',
                    reminder_day: 30,
                    final_reminder_day: 1,
                    experient_after: 12,
                },
                alert: {
                    type: '',
                    text: '',
                    dismissSecs: 5,
                    dismissCountDown: 0
                },
                loading: true,
                saving: false
            },
            created: function () {
                this.getData();
            },
            methods: {
                getData: function () {
                    axios.get('/settings/point/data').then(response => {
                        if (response.data.point.length != 0) {
                            this.temp = response.data.point[0];
                            this.temp.placeholder = response.data.point[0].name;
                        }
                        this.loading = false;
                    });
                },
                saveSetting() {
                    this.saving = true;
                    axios.put('/settings/point/settings', this.temp).then(() => {
                        this.alert.text = 'Settings saved successfully';
                        this.alert.type = 'success';
                        this.alert.dismissCountDown = this.alert.dismissSecs;
                        this.saving = false;
                    }).catch((error) => {
                        this.text = '';
                        this.saving = false;
                        clearErrors(this.$el);
                        showErrors(this.$el, error.response.data.errors);
                    });
                },
                toogleProgramStatus: function () {
                    if (this.temp.status == 'Disabled') {
                        this.temp.status = 'Enabled';
                    } else {
                        this.temp.status = 'Disabled';
                    }
                },
                tooglePointsExpiration: function () {
                    if (this.temp.experient_status == 'Disabled') {
                        this.temp.experient_status = 'Enabled';
                    } else {
                        this.temp.experient_status = 'Disabled';
                    }
                },
                saveReminderEmail: function () {

                    axios.put('/settings/point/reminde', this.temp)
                        .then((response) => {

                            this.reminderEmail = this.temp.reminder_status;
                            this.reminderDays = response.data.response.reminder_day;

                        });
                    this.$root.$emit('bv::hide::modal', 'reminder-email')
                },
                saveFinalChance: function () {
                    axios.put('/settings/point/final/reminde', this.temp)
                        .then((response) => {
                            this.finalChanceEmail = this.temp.final_reminder_status;
                            this.finalChanceDays = this.temp.final_reminder_day;
                        });
                    this.$root.$emit('bv::hide::modal', 'final-chance')
                },
                countDownChanged(dismissCountDown) {
                    this.alert.dismissCountDown = dismissCountDown
                },

                changeName: function(){
                    this.temp.plural_name = this.temp.name+'s';
                },
            },
            watch: {
                'temp.plural_name': function () {
                    this.temp.plural_name  =  this.temp.plural_name .charAt(0).toUpperCase() +  this.temp.plural_name.substr(1);
                },
                'temp.name': function () {
                    this.temp.name  =  this.temp.name .charAt(0).toUpperCase() +  this.temp.name.substr(1);
                }
            }
        })
    </script>
@endsection