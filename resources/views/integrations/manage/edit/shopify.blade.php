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
                            <div class="col-md-12">
                                <div class="form-group m-b-0">
                                    <label class="light-font m-b-0 m-t-5">
                                        <span v-text="app.name"></span> is
                                        <span class="bold" v-if="app.status == 1">Connected</span>
                                        <span class="bold" v-if="app.status == 0">Disabled</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row p-t-25 p-b-25">
                <div class="col-md-5 col-12">
                    <h5 class="bolder m-b-15">Order Settings</h5>
                    <p class="m-b-10">Define how rewards are issued to your customers based on the order's status and
                        totals.</p>
                </div>
                <div class="col-md-7 col-12">
                    <div class="well bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group m-b-5">
                                    <label class="light-font m-b-0 m-t-5">
                                        Reward customers with points when the order status is set to:
                                    </label>
                                </div>
                                <b-form-select v-model="app.order_settings.reward_status"
                                               name="order_settings.reward_status">
                                    <option value="paid">Paid</option>
                                    <option value="fulfilled">Fulfilled</option>
                                </b-form-select>
                            </div>
                        </div>
                        <div class="row m-t-10 m-b-10">
                            <div class="col-md-12">
                                <div class="form-group m-b-5">
                                    <label class="light-font m-b-0 m-t-5">
                                        Subtract points from customers when the order status is set to:
                                    </label>
                                </div>
                                <b-form-select v-model="app.order_settings.subtract_status"
                                               name="order_settings.subtract_status">
                                    <option value="refunded">Refunded</option>
                                    <option value="voided">Voided</option>
                                </b-form-select>
                            </div>
                        </div>
                        <div class="row m-t-10 m-b-5">
                            <div class="col-md-12">
                                <div class="form-group m-b-5">
                                    <label class="light-font m-b-0 m-t-5">
                                        Reward customers with points based on the following order items:
                                    </label>
                                </div>
                                <b-form-checkbox
                                        class="w-100"
                                        name="order_settings.subtotal"
                                        v-if="app.name != 'Shopify' && app.name != 'shopify'"
                                        v-model="app.order_settings.subtotal"
                                        value="1"
                                        unchecked-value="0">
                                    Subtotal
                                </b-form-checkbox>
                                <b-form-checkbox class="w-100 m-t-10" name="order_settings.coupon"
                                                 v-model="app.order_settings.exclude_discounts"
                                                 value="1"
                                                 unchecked-value="0">
                                    Exclude coupon discounts
                                </b-form-checkbox>
                                <b-form-checkbox class="w-100 m-t-10" name="order_settings.shipping"
                                                 v-model="app.order_settings.include_shipping"
                                                 value="1"
                                                 unchecked-value="0">
                                    Include shipping
                                </b-form-checkbox>
                                <b-form-checkbox class="w-100 m-t-10" name="order_settings.taxes"
                                                 v-model="app.order_settings.include_taxes"
                                                 value="1"
                                                 unchecked-value="0">
                                    Include taxes
                                </b-form-checkbox>
                                <b-form-checkbox class="w-100 m-t-10" name="order_settings.previous_orders"
                                                 v-model="app.order_settings.include_previous_orders"
                                                 value="1"
                                                 unchecked-value="0">
                                    Include previous purchases as a guest
                                </b-form-checkbox>
                                <div>
                                    <button @click.prevent="reinstallWidgetCode" class="btn btn-blue"
                                            :class="{ 'disabled' : reinstallWidgetCodeProcessing }"
                                            style="padding-left: 15px; padding-right: 15px; margin-top: 15px;">Reinstall Widget Code
                                    </button>
                                    <div style="display: inline-block;vertical-align: top;margin-left: 10px;">
                                        <div style="width: 16px;height: 16px;margin: 0;margin-top: 23px;"
                                             :class="{'loading': reinstallWidgetCodeProcessing}"></div>
                                    </div>
                                </div>
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
          reinstallWidgetCodeProcessing: false,
          saving: false,
          merchantId: Spark.state.currentTeam.id,
          app: {
            name: '',
            status: 1,
            order_settings: {
              reward_status: 'fulfilled',
              subtract_status: 'refunded',
              subtotal: "1",
              exclude_discounts: "1",
              include_shipping: "0",
              include_taxes: "0",
              include_previous_orders: "1"
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
            axios.get('/api/merchants/' + Spark.state.currentTeam.id + '/integrations/' + '{{$id}}').then(result => {
              try {
                let integration = result.data.data
                this.app.name = integration.integration.title
                this.app.status = integration.status
                if (integration.settings) {
                  if (integration.settings.order_settings) {
                    let order_settings = integration.settings.order_settings
                    let boolKeys = ['subtotal', 'exclude_discounts', 'include_shipping', 'include_taxes', 'include_previous_orders']
                    for (let key in order_settings) {
                      if (boolKeys.indexOf(key) >= 0) {
                        order_settings[key] = +(!!order_settings[key])+''
                      }
                    }
                    this.app.order_settings = Object.assign(this.app.order_settings, order_settings)
                  }
                }
              } catch (e) {}
            }).catch(error => {

            }).then(() => {
              if (!this.app.name) {
                this.app.name = 'Shopify'
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
          reinstallWidgetCode: function () {
            if (!this.reinstallWidgetCodeProcessing) {
              this.reinstallWidgetCodeProcessing = true
              axios.post('/api/merchants/' + this.merchantId + '/integrations/{{$id}}/reinstall-widget-code').then(response => {
                this.alert.type = 'success'
                this.alert.text = 'Wigdet code was successfully reinstalled.'
                this.alert.dismissCountDown = this.alert.dismissSecs
              }).catch(errors => {
                this.alert.type = 'danger'
                this.alert.text = 'An error occurred while attempting to reinstall widget code. Please, try again.'
                this.alert.dismissCountDown = this.alert.dismissSecs
                console.log(errors)
              }).then(() => {
                this.reinstallWidgetCodeProcessing = false
              })
            }
          }
        }
      })
    </script>
@endsection
