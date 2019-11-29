@extends('layouts.app')

@section('title', 'Referrals')

@section('content')
    <div id='overview' class="points-overview p-b-40">
        <div class="row m-t-20 p-b-10 section-border-bottom">
            <div class="col-md-6 col-12">
                <h3 class="page-title m-t-0 color-dark">Referrals Overview</h3>
            </div>
        </div>
        <referrals-overview-rewards></referrals-overview-rewards>

        <div class="row m-t-25">
            <div class="col-md-12">
                <div class="well well-table">
                    <div class="table-header">
                        <p class="bold color-dark-grey f-s-16 pull-left">Latest Referral Activity</p>
                        <p class="pull-right">
                            <a class="bolder f-s-14 color-blue" href="{{ route('referrals.activity') }}">View All</a>
                        </p>
                    </div>
                    <div :class="{'loading': !referralsActivity.ready}" v-cloak="">
                        <table class="table">
                            <thead>
                            <tr>
                                <th class="bold color-dark-grey">Sender Name</th>
                                <th class="bold color-dark-grey">Receiver Name</th>
                                <th class="bold color-dark-grey">Order Number</th>
                                <th class="bold color-dark-grey">Order Total</th>
                                <th class="bold color-dark-grey">Date</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="row in referralsActivity.data">
                                    <td>
                                        <a :href="'/customers/profile/' + row.referrer_id"
                                           class="bold color-blue f-s-15" v-text="row.referrer_name"></a>
                                    </td>
                                    <td>
                                        <a :href="'/customers/profile/' + row.customer_id"
                                           class="bold color-blue f-s-15" v-text="row.customer_name"></a>
                                    </td>
                                    <td>
                                        <a class="bold"
                                           :href="merchantDetails.data.shop_domain+'/admin/orders/'+row.order_number"
                                           target="_blank">
                                            #<span v-text="row.order_number"></span>
                                        </a>
                                    </td>
                                    <td>
                                        <span v-if="!merchantDetails.data.display_currency_name"
                                              v-text="merchantDetails.data.currency"></span><span
                                                v-text="row.order_total"></span>
                                        <span v-if="merchantDetails.data.display_currency_name"
                                              v-text="merchantDetails.data.currency"></span>
                                    </td>
                                    <td>
                                        @{{row.date | date-human}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script type="text/javascript">
      var rewards = new Vue({
        el: '#overview',
        data: {
          senderReward: null,
          receiverReward: null,
          receiverRewardUrl: null,
          senderRewardUrl: null,
          merchantDetails: {
            data: {
              shop_domain: '',
              currency: ''
            },
            ready: false
          },
          referralsActivity: {
            data: [],
            ready: false
          },
        },
        created: function () {
          this.getData()
          this.getCurrentMerchantDetails()
          this.getReferralsActivity()
        },
        methods: {
          getData: function () {
            axios.get('/referrals/rewards/get').then((response) => {
                //console.log(this.senderReward)
                this.senderReward = response.data.senderReward
                this.receiverReward = response.data.receiverReward
                this.senderRewardUrl = response.data.senderRewardUrl
                this.receiverRewardUrl = response.data.receiverRewardUrl
                if (!this.form.iconPreview) {
                  showPreviewIcon(this.form.program.reward_icon, this.icon_default_class, this.icon_parent_el)
                } else {
                  clearPreviewIcon(this.icon_default_class, this.icon_parent_el)
                }
              }
            ).catch((error) => {
              this.errors = error
            })
          },
          getCurrentMerchantDetails: function () {
            this.merchantDetails.ready = false
            axios.get('/api/merchants/' + Spark.state.currentTeam.id + '/details/all').then((response) => {
              if (response.data && response.data.data) {
                this.merchantDetails.data = response.data.data
                if (this.merchantDetails.data.shop_domain) {
                  this.merchantDetails.data.shop_domain = 'https://' + this.merchantDetails.data.shop_domain
                }
              }
            }).catch((errors) => {
              console.log(errors)
            }).then(() => {
              this.merchantDetails.ready = true
            })
          },
          getReferralsActivity: function () {
            let requestData = {
              latest: 5
            }
            this.referralsActivity.ready = false
            axios.get('/api/merchants/' + Spark.state.currentTeam.id + '/referrals/activity', {
              params: requestData
            }).then((response) => {
              if (response.data && response.data.data) {
                let referralsActivity = response.data.data
                this.referralsActivity.data = referralsActivity
                this.referralsActivity.data = referralsActivity.map((item) => {
                  return {
                    customer_id: item.customer_id,
                    customer_name: item.customer_name,
                    order_number: item.ecommerce_order_id,
                    order_total: item.total_price,
                    referrer_id: item.referrer_id,
                    referrer_name: item.referrer_name,
                    date: item.created,
                  }
                })
              }
            }).catch((errors) => {
              console.log(errors)
            }).then(() => {
              this.referralsActivity.ready = true
            })
          },
        }
      })
    </script>
@endsection
