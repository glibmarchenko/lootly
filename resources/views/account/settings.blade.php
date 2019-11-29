@extends('layouts.app')
@section('title', 'Account Settings')


@section('content')
    <div id="account-settings" class="loader m-t-20 m-b-10" v-cloak>
        <span :class="loading ? 'loading m-t-50' : ''">     
            <b-alert :show="alert.dismissCountDown"
                     dismissible
                     :variant="alert.type"
                     @dismissed="alert.dismissCountdown=0"
                     @dismiss-count-down="countDownChanged">
                @{{alert.text}}
            </b-alert>

            <form role="form">
                {{-- {{ csrf_field() }}--}}

                <div class="row m-t-20 p-b-10 section-border-bottom">
                    <div class="col-md-6 col-12">
                        <h3 class="page-title m-t-0 color-dark">Account Settings</h3>
                    </div>
                    <div class="col-md-6 col-12">
                        <save-button class="text-right" :saving="saving" button="Save"
                                     @event="saveAccountSettings"></save-button>
                    </div>
                </div>

                <div class="row p-t-25 p-b-25 section-border-bottom">
                    <div class="col-md-5 col-12">
                        <h5 class="bolder">Account Information</h5>
                    </div>
                    <div class="col-md-7 col-12">
                        <div class="well bg-white">
                            <span class="loading-spinner" v-cloak>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="light-font">First Name</label>
                                            <input type="text" class="form-control" name="first_name"
                                                   v-model="form.user.first_name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="light-font">Last Name</label>
                                            <input type="text" class="form-control" name="last_name"
                                                   v-model="form.user.last_name">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="light-font">Email Address</label>
                                            <input class="form-control" name="email" v-model="form.user.email">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="light-font">Billing Email Address</label>
                                            <input class="form-control" name="billing_email"
                                                   v-model="form.user.billing_email">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="light-font">Password</label>
                                            <input class="form-control" type="password" name="password"
                                                   v-model="form.user.password">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="light-font">Confirm Password</label>
                                            <input class="form-control" type="password" name="password_confirmation"
                                                   v-model="form.user.password_confirmation">
                                        </div>
                                    </div>
                                </div>
                            </span>
                        </div>
                    </div>
                </div>
                <div v-if="form.merchant.id" class="row p-t-25 p-b-25 section-border-bottom"
                     :class="{'loading': merchant_loading}" v-cloak>
                    <div class="col-md-5 col-12">
                        <h5 class="bolder">Store Details</h5>
                    </div>
                    <div class="col-md-7 col-12">
                        <div class="well bg-white">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="light-font">Store Name</label>
                                        <input class="form-control" name="merchant.name" v-model="form.merchant.name">
                                        <input type="hidden" class="form-control" v-model="form.merchant.id"
                                               name="merchant.id"
                                               value="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="light-font">Store URL</label>
                                        <input class="form-control" v-model="form.merchant.website" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="light-font">Currency</label>
                                        <select name="merchant.currency_id" class="form-control"
                                                v-model="form.merchant.currency_id">

                                            <option :value="null" selected="" disabled>Select a Currency</option>

                                            <option :value="currency.id" v-for="currency in currencies"
                                                    :selected="user && user.current_merchant && user.current_merchant.data.currency_id == currency.id">
                                                @{{currency.display_type}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="light-font">Currency Display Type</label>
                                        <select name="merchant.currency_display_sign" class="form-control"
                                                v-model="form.merchant.currency_display_sign">

                                            <option :value="item.value"
                                                    v-for="item in currencyDisplayOptions"
                                                    :selected="merchant && !!merchant.currency_display_sign == !!item.value">
                                                @{{item.text}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="light-font">Language</label>
                                        <select name="merchant.language" class="form-control"
                                                v-model="form.merchant.language">
                                            <option :value="language.name" v-for="language in languages"
                                                    :selected="merchant && merchant.language == language.name">
                                                @{{language.name}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="form.merchant.id" class="row p-t-25 p-b-25 section-border-bottom"
                     :class="{'loading': merchant_loading}" v-cloak>
                    <div class="col-md-5 col-12">
                        <h5 class="bolder">Employee Access</h5>
                        <p class="m-t-15 m-b-0">Invite other users to access your Lootly account.</p>
                        <p class="m-t-0">Only Full access users can remove employees from the account.</p>
                    </div>
                    @if(!$has_employee_access_permissions)
                        <div class="col-md-7 col-12">
                            <no-access :loading="loading"
                                       title="{{$upsell->upsell_title}}"
                                       desc="{{$upsell->upsell_text}}"
                                       icon="{{$upsell->upsell_image}}"
                                       plan="{{$upsell->getMinPlan()->name}}"></no-access>
                        </div>
                    @else
                        <div class="col-md-7 col-12">
                            <div class="well bg-white">
                                <div class="row">
                                    <label class="bolder col-md-6 f-s-15">Accounts</label>
                                    <div class="col-md-6 text-right">
                                        <a @click="addEmployee" class="bolder color-blue f-s-15">Add Employee</a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-group-icon m-t-10 col-md-12">
                                        <div class="input-icon">
                                            <span class=""><i class="fa fa-search" aria-hidden="true"></i></span>
                                        </div>
                                        <input v-model="search" type="text" class="form-control"
                                               placeholder="Search Employee Name ...">
                                    </div>
                                </div>
                                <div class="search-enteries">
                                    <div class="search-entery" v-for="employee in filteredEmployees">
                                        <div class="overflow">
                                        <span class="employee-name pull-left">
                                            <a href="javascript:void(0)" class="color-blue"
                                               @click="editEmployee(employee)">
                                                <span v-text="getEmployeeName(employee)"></span>
                                            </a>
                                        </span>
                                            <span class="pull-right"
                                                  v-text="employee.role == 'owner' ? 'Full Access' : (employee.role == 'member' ? 'Limited Access' : '')"></span>
                                        </div>
                                        <p class="status" v-text="employee.statusText"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="row p-t-25 p-b-25 section-border-bottom">
                    <div class="col-md-5 col-12">
                        <h5 class="bolder">Account Notifications</h5>
                        <p class="m-t-15 m-b-0">Lootly will routinely contact you about updates to your account.</p>
                        <p class="m-t-0">in addition a summary of your program.</p>
                    </div>
                    <div class="col-md-7 col-12">
                        <div class="well bg-white">
                            <div class="row">
                                <div v-for="(user_notification_type, index) in user_notification_types"
                                     v-show="user_notification_type.slug !== 'no_reward_codes_available' || isIntegrationApi()"
                                     class="col-md-12  m-b-10"
                                >
                                    <b-form-checkbox
                                        :id="'ch'+index"
                                        :value="1"
                                        :unchecked-value="0"
                                        v-model="form.user.notifications[user_notification_type.slug]"
                                        :name="'notifications.'+user_notification_type.slug"
                                    >
                                        <span v-text="user_notification_type.title"></span>
                                    </b-form-checkbox>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="form.merchant.id" class="row p-t-25 p-b-15" :class="{'loading': merchant_loading}" v-cloak>
                    <div class="col-md-5 col-12">
                        <h5 class="bolder">API Keys</h5>
                        <p class="m-t-15 m-b-0">For custom integrations the following API Key & API Secret will be required.</p>
                    </div>
                    <div class="col-md-7 col-12">
                        <div class="well bg-white">
                            <div class="form-group m-b-20">
                                <label class="">API Key</label>
                                <div class="row">
                                    <div class="col-md-8">
                                        <input id="apiKey" placeholder="API Key" class="form-control" name="api.key"
                                               v-model="form.merchant.api.key" readonly>
                                    </div>
                                    <div class="col-md-2 p-l-0">
                                        <button type="button" class="btn btn-copy w-100" onclick="copyText('#apiKey')">Copy</button>
                                    </div>
                                    <div class="col-md-2 p-l-0">
                                        <button type="button" class="btn btn-blue w-100" style="min-width: initial"
                                                @click="resetKey('key')">Reset</button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-b-5">
                                <label class="">API Secret</label>
                                <div class="row">
                                    <div class="col-md-8">
                                        <input id="apiSecret" placeholder="API Secret" class="form-control"
                                               name="api.secret" v-model="form.merchant.api.secret" readonly>
                                    </div>
                                    <div class="col-md-2 p-l-0">
                                        <button type="button" class="btn btn-copy w-100"
                                                onclick="copyText('#apiSecret')">Copy</button>
                                    </div>
                                    <div class="col-md-2 p-l-0">
                                        <button type="button" class="btn btn-blue w-100" style="min-width: initial"
                                                @click="resetKey('secret')">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Add/Edit Employee Modal -->
            <b-modal class="custom-modal" hide-footer id="add-employee" :title="modalName" v-cloak>
                <b-alert :show="employeeError" variant="danger"><b>Error: </b>@{{ employee_errors }}</b-alert>
                <input v-model="employee.id" type="hidden">
                <form role="form">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="light-font">Employee Name</label>
                                <input class="form-control" v-model="employee.name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="light-font">Employee Email</label>
                                <input class="form-control" v-model="employee.email">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="light-font m-t-5">Access Type:</label>
                        </div>
                        <div class="col-md-9">
                            <b-form-radio-group id="role" v-model="employee.role" name="radioSubComponent">
                                <b-form-radio value="member" name="role">Limited Access (main menu
                                    pages only)
                                </b-form-radio>
                                <b-form-radio value="owner" name="role">Full Access (all pages except
                                    billing)
                                </b-form-radio>
                            </b-form-radio-group>
                        </div>
                    </div>
                    <div class="row m-t-10 p-b-10 p-t-20 border-top" v-if="modalName == 'Edit Employee'">
                        <div class="col-md-4 text-center"
                             v-bind:class="[modalName == 'Edit Employee' ? 'offset-md-2' : 'offset-md-4']">
                            <span v-if="saving" class="i-loading"></span>
                            <button v-show="!saving" @click.prevent="saveEditEmployee(employee)"
                                    class="btn btn-block btn-blue btn-lg">Save
                            </button>
                        </div>
                        <div class="col-md-4 text-center" v-if="modalName == 'Edit Employee'">
                            <span v-if="deleting" class="i-loading"></span>
                            <button v-show="!deleting" @click.prevent="deleteEmployee(employee)"
                                    class="btn btn-block btn-danger btn-glow"> Delete </button>
                        </div>
                    </div>
                    <div class="row m-t-10 p-b-10 p-t-20 border-top" v-else>
                        <div class="col-md-4 text-center"
                             v-bind:class="[modalName == 'Edit Employee' ? 'offset-md-2' : 'offset-md-4']">
                            <span v-if="saving" class="i-loading"></span>
                            <button v-show="!saving" @click.prevent="saveEmployee"
                                    class="btn btn-block btn-blue btn-lg">Save</button>
                        </div>
                    </div>
                </form>
            </b-modal>
        </span>
    </div>
@endsection

@section('scripts')

    <script>
      function copyText (id) {
        el = $(id)
        el.select()
        document.execCommand('copy')
      }

      var page = new Vue({
        el: '#account-settings',
        data: {
          loading: true,
          saving: false,
          deleting: false,
          alert: {
            type: '',
            text: '',
            dismissSecs: 5,
            dismissCountDown: 0
          },
          user: {},
          merchant_loading: true,
          form: {
            user: {
              first_name: null,
              last_name: null,
              email: null,
              billing_email: null,
              password: null,
              confirm_password: null,
              notifications: {}
            },
            merchant: {
              id: null,
              website: '',
              name: null,
              currency_id: null,
              currency_display_sign: null,
              language: null,
              api: {
                key: null,
                key_hash: null,
                secret: null,
                secret_hash: null
              }
            },
          },
          currencies: [],
          languages: [],
          user_notification_types: [],
          employees: [],
          employee: {id: '', name: '', email: '', role: '', status: ''},

          errors: '',
          merchant: {},
          employee_errors: '',
          employeeError: false,
          search: '',
          modalName: '',
          webhook_url: null,
        },
        created: function () {
          this.currencies = JSON.parse('<?php echo isset($currencies) ? addslashes(json_encode($currencies)) : '[]' ?>')
          this.languages = JSON.parse('<?php echo isset($languages) ? addslashes(json_encode($languages)) : '[]' ?>')
          this.user_notification_types = JSON.parse('<?php echo isset($user_notification_types) ? addslashes(json_encode($user_notification_types)) : '[]' ?>')
          for (let i = 0; i < this.user_notification_types.length; i++) {
            this.form.user.notifications[this.user_notification_types[i].slug] = this.user_notification_types[i].active_by_default ? 1 : 0
          }

          this.getAccountSettings()
        },
        mounted () {
        },
        methods: {
          getAccountSettings: function () {
            axios.get('/api/user/settings').then((result) => {
              if (result.data && result.data.data) {
                let userSettings = result.data.data

                this.form.user.first_name = userSettings.first_name || null
                this.form.user.last_name = userSettings.last_name || null
                this.form.user.email = userSettings.email || null
                this.form.user.billing_email = userSettings.billing_email || null
                this.form.user.password = null
                this.form.user.password_confirmation = null

                if (userSettings.notifications && userSettings.notifications.data && userSettings.notifications.data.length) {
                  let userNotifications = userSettings.notifications.data
                  for (let formNotification in this.form.user.notifications) {
                    for (let userNotification of userNotifications) {
                      if (userNotification.slug === formNotification) {
                        this.form.user.notifications[formNotification] = userNotification.active ? 1 : 0
                      }
                    }
                  }
                }
              } else {
                throw new Error('No user\'s settings data received!')
              }
            }).catch((error) => {
              console.log(error)
            }).then(() => {
              this.loading = false
            })
            axios.get('/api/merchants/' + Spark.state.currentTeam.id + '/settings').then((result) => {
              if (result.data && result.data.data) {
                let merchantSettings = result.data.data
                this.form.merchant.id = merchantSettings.id || null
                this.form.merchant.website = merchantSettings.website || null
                this.form.merchant.name = merchantSettings.name || null
                this.form.merchant.currency_id = merchantSettings.currency_id || null
                this.form.merchant.currency_display_sign = +!!merchantSettings.currency_display_sign
                this.form.merchant.language = merchantSettings.language || null
                this.form.merchant.integrations = merchantSettings.integrations || null

                if (merchantSettings.details && merchantSettings.details.data) {
                  this.form.merchant.api.key = merchantSettings.details.data.api_key || null
                  this.form.merchant.api.secret = merchantSettings.details.data.api_secret || null
                }

                this.getEmployees(merchantSettings.id)

              } else {
                throw new Error('No merchant\'s settings data received!')
              }
            }).catch((error) => {
              console.log(error)
            }).then(() => {
              this.merchant_loading = false
            })
          },
          isIntegrationApi: function () {
            let integrations = this.form.merchant.integrations;

            if (integrations) {
                for (let prop in integrations) {
                    if (integrations[prop]['is_api'] === true) {
                        return true;
                    }
                }
            }
            return false;
          },
          saveAccountSettings: function () {
            if (!this.saving) {
              this.saving = true
              if(this.form.user.password == ""){
                this.form.user.password = null;
              }
              axios.put('/api/user/settings', this.form).then(() => {
                this.alert.text = 'Settings saved successfully'
                this.alert.type = 'success'
                this.alert.dismissCountDown = this.alert.dismissSecs
              }).catch((error) => {
                clearErrors(this.$el)
                showErrors(this.$el, error.response.data.errors)
                this.alert.text = error.response.data.message
                this.alert.type = 'danger'
                this.alert.dismissCountDown = this.alert.dismissSecs
              }).then(() => {
                this.saving = false
              })
            }
          },
          getEmployeeName: function (employee) {
            let name = ''
            if (employee.invited_name) {
              name = employee.invited_name
            }
            if (employee.name && employee.invited_name !== employee.name) {
              name += ' (' + employee.name + ')'
            }
            return name
          },
          getEmployees: function (id) {
            const comp = this
            axios.get('/api/merchant/' + id + '/settings/invited-users').then(response => {
              comp.employees = response.data
            })
          },
          EditAccountInformation: function () {
            this.alert.text = ''

            axios.put('/settings/contact', this.merchant)
              .then(() => {
                this.errors = ''
                this.alert.text = 'Settings saved successfully'
                this.alert.type = 'success'
                this.alert.dismissCountDown = this.alert.dismissSecs
              })
              .catch((error) => {
                clearErrors(this.$el)
                showErrors(this.$el, error.response.data.errors)
              })
          },

          countDownChanged (dismissCountDown) {
            this.alert.dismissCountDown = dismissCountDown
          },
          addEmployee () {
            this.employeeError = false
            this.employee = {id: '', name: '', email: '', role: ''}
            this.modalName = 'Add Employee'
            this.$root.$emit('bv::show::modal', 'add-employee')
          },
          saveEmployee: function () {
            this.alert.text = ''
            this.saving = true
            this.employeeError = false
            axios.post('/api/merchant/' + Spark.state.currentTeam.id + '/settings/invitations', this.employee).then((response) => {
              this.getEmployees(Spark.state.currentTeam.id)
              this.$root.$emit('bv::hide::modal', 'add-employee')
              this.alert.type = 'success'
              this.alert.text = 'Employee invite successfully'
              this.saving = false
            }).catch((error) => {
              this.saving = false
              if(error && error.response) {
                this.employeeError = true
                if (error.response.status && error.response.status == 404) {
                  this.employee_errors = error.response.data.error
                } else {
                  this.employee_errors = error.response.data.errors.email[0]
                  this.employee_errors = error.response.data.errors.name[0]
                }
              }
            })
          },
          editEmployee (employee) {
            this.employeeError = false
            this.modalName = 'Edit Employee'
            this.employee = {
              id: employee.id || null,
              invitation_id: employee.invitation_id || null,
              name: employee.invited_name,
              email: employee.invited_email,
              role: employee.role,
              status: employee.status || 'pending',
            }
            this.$root.$emit('bv::show::modal', 'add-employee')
          },
          saveEditEmployee (employee) {
            this.saving = true
            axios.put('/api/merchant/' + Spark.state.currentTeam.id + '/settings/invitations', employee).then(() => {
              this.getEmployees(Spark.state.currentTeam.id)
              this.$root.$emit('bv::hide::modal', 'add-employee')
              this.alert.type = 'success'
              this.alert.text = 'Employee edit successfully'
              this.saving = false
            }).catch((error) => {
              this.saving = false
              this.employee_errors = error.response.data.errors
            })
          },
          deleteEmployee (employee) {
            this.deleting = true
            axios.post('/api/merchant/' + Spark.state.currentTeam.id + '/settings/invited-users/delete', employee).then(() => {
              this.employees = this.employees.filter((item) => {
                return item !== employee
              })
              this.getEmployees(Spark.state.currentTeam.id)
              this.$root.$emit('bv::hide::modal', 'add-employee')
              this.employee = {id: '', name: '', email: '', type: '', status: ''}
              this.deleting = false
            }).catch((error) => {
              this.deleting = false
              this.employee_errors = error.response.data.errors
            })
          },
          resetKey: function (type) {
            axios.get('/api/merchants/' + Spark.state.currentTeam.id + '/reset-api-key').then((response) => {
              if (response.data) {
                if (type === 'key') {
                  this.form.merchant.api.key = response.data.string
                  this.form.merchant.api.key_hash = response.data.hash
                }
                if (type === 'secret') {
                  this.form.merchant.api.secret = response.data.string
                  this.form.merchant.api.secret_hash = response.data.hash
                }
              }
            }).catch((error) => {
              console.log(error)
            }).then(() => {})
          },
        },
        computed: {
          filteredEmployees: function () {
            if (this.search) {
              return this.employees.filter(
                item => item.name.toUpperCase().includes(this.search.toUpperCase()) || item.status.toUpperCase().includes(this.search.toUpperCase())
              )
            }
            return this.employees
          },
          currencyDisplayOptions: function () {
            let selectedCurrency = this.currencies.find(item => {
              return item.id === this.form.merchant.currency_id
            }) || null
            let currencyDisplayOptions = []
            if (selectedCurrency && selectedCurrency.currency_sign && selectedCurrency.currency_sign.length) {
              currencyDisplayOptions.push({
                text: selectedCurrency.currency_sign,
                value: 1
              })
            }
            if (selectedCurrency && selectedCurrency.name && selectedCurrency.name.length) {
              currencyDisplayOptions.push({
                text: selectedCurrency.name,
                value: 0
              })
            }
            return currencyDisplayOptions
          }
        },
        watch: {}
      })
    </script>

@endsection