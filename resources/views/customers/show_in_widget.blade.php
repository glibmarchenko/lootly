@extends('layouts.app')

@section('title', 'Customer Profile')

@section('content')
    <div id="customer-widget" class="loader p-b-40 m-t-20 m-b-10" v-cloak>
        <div v-if="customerExists">
            <b-alert v-cloak :show="alert.dismissCountDown" dismissible :variant="alert.type" @dismissed="alert.dismissCountdown=0" @dismiss-count-down="countDownChanged" {{--:show="alertText ? true : false" dismissible--}}>@{{alert.text}}
            </b-alert>
            <div class="row m-t-15 p-b-10 section-border-bottom">
                <div class="col-md-12 col-12 m-t-5">
                    <h3 class="page-title m-t-0 color-dark">
                        <span>Widget Preview</span>
                    </h3>
                </div>
            </div>
            <div class="row m-t-20">
                <div id="impersonated-widget"></div>
            </div>
        </div>
        <div v-else class="well flex-center">
            <div class="content">
                <div class="title">User Not Found</div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <style type="text/css">#intercom-container { display: none !important;}</style>
    <script src="{{ url('js/integrations/common/script.js') }}"></script>
    <script>
        new Vue({
            el: '#customer-widget',
            data: {
                customerId: '{{$id}}',
                alertText: '',
                merchants: {
                    current: {
                        details: {
                            data: [],
                            loading: false
                        }
                    }
                },
                merchant: {},
                overview: {
                    name: 'Customer Name',
                    email: '',
                    currentPoints: 'N/A',
                    totalEarnedPoints: 'N/A',
                    totalSpent: '',
                    couponsUsed: 'N/A',
                    vipTier: 'N/A',
                    lastSeen: 'N/A',
                    birthday: 'N/A',
                    referralLink: '',
                    ecommerce_id: '',
                },
                loading: false,
                customerExists: true,
                shopifyCustomerID: '',
                alert: {
                    type: '',
                    text: '',
                    dismissSecs: 5,
                    dismissCountDown: 0
                },
            },
            created: function() {
                this.initImpersonatedWidget()
            },
            methods: {
                initImpersonatedWidget: function() {
                    axios.get('/api/merchants/' + Spark.state.currentTeam.id + '/customers/' + this.customerId +
                        '/widget-impersonate'
                    ).then(response => {
                        let config = response.data.data
                        let app_url = "{{ config('app.url') }}"
                        document.getElementById('impersonated-widget').innerHTML = `
                          <div id="lootly-widget" class="lootly-init" style="display: none;"
                                data-provider="${config.provider}"
                                data-api-key="${config.api_key}"
                                data-shop-domain="${config.shop_domain}"
                                data-shop-id="${config.shop_id}"
                                data-customer-id="${config.customer_id}"
                                data-customer-signature="${config.customer_signature}">
                            </div>
                        `
                        lootlyWidgetInit()
                    }).catch(error => {
                        console.log(error)
                    })
                },
                countDownChanged(dismissCountDown) {
                    this.alert.dismissCountDown = dismissCountDown
                }
            },
            computed: {},
            watch: {}
        })
    </script>
@endsection