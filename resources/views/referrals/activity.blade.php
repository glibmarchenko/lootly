@extends('layouts.app')

@section('title', 'Referrals Activity')

@section('content')
    <div id="referrals-activity" class="loader p-b-40" v-cloak>
        <div class="row m-t-20 p-b-10 section-border-bottom">
            <div class="col-md-6 col-12 ">
                <h3 class="page-title m-t-0 color-dark">Referrals Activity</h3>
            </div>
            <div class="col-md-6 col-12 text-right table-actions">
                <div class="btn-group date-range-buttons" role="group">
                    <a class="bold color-blue f-s-15 m-r-15 m-t-5" :href="exportActivitiesUrl">
                        <i class="icon-export f-s-19 m-r-5"></i>
                        Export
                    </a>
                    <button type="button" v-bind:class="[dateRange.selectedRange == 'custom' ? 'active' : '']"
                            id="dataRange" class="btn btn-default pull-right">Custom
                    </button>
                    <button type="button" @click="changeDateRange(30)"
                            :class="[dateRange.selectedRange == '30 days' ? 'active' : '']"
                            class="btn btn-default">30 Days
                    </button>
                    <button type="button" @click="changeDateRange(7)"
                            :class="[dateRange.selectedRange == '7 days' ? 'active' : '']"
                            class="btn btn-default">7 Days
                    </button>
                    <!-- <button type="button" @click="changeDateRange(1)"
                            :class="[dateRange.selectedRange == '24 hours' ? 'active' : '']"
                            class="btn btn-default">24 Hours
                    </button> -->
                </div>
            </div>
        </div>
        <div class="row m-t-25">
            <div class="col-md-12">
                <div class="well well-table">

                    <div class="table-header table-header-filters">
                        <div class="row">
                            <div class="col-md-7 col-12">
                                <label class="bold m-t-5">Display: </label>
                                <select class="form-control" v-model="pageSize">
                                    <option value="5">5</option>
                                    <option selected value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div class="col-md-5 col-12">
                                <div class="input-group-icon">
                                    <div class="input-icon"><span><i aria-hidden="true" class="fa fa-search"></i></span>
                                    </div>
                                    <input v-model="search" placeholder="Search" class="form-control" type="text">
                                </div>
                            </div>
                        </div>
                    </div>
                    <span :class="{'loading': !referralsActivity.ready}">
						<div v-if="sortedActivity.length">
							<sortable-table
                                    :hide-header="true"
                                    :page-size="parseInt(pageSize)"
                                    :contents="sortedActivity"
                                    sort-by="date"
                                    direction="center"
                                    :thead="[{text: 'Sender Name', name: 'name'}, {text: 'Receiver Name', name: 'referred_name'}, {text: 'Order Number', name: 'order_number'}, {text: 'Order Total', name: 'order_total'} , {text: 'Date', name: 'date'}]">
									
								<template slot-scope="{row}">
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
                                        <span v-if="!merchantDetails.data.display_currency_name" v-text="merchantDetails.data.currency"></span><span v-text="row.order_total"></span>
                                        <span v-if="merchantDetails.data.display_currency_name" v-text="merchantDetails.data.currency"></span>
									</td>
									<td>
										@{{row.date | date-human}}
									</td>
								</template> 

							</sortable-table>
						</div>
						<div class="text-left" v-else style="padding: 20px;">
							No referral activity for the selected time period.
						</div>
					</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css"/>
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <script>
      var start = moment().subtract(7, 'days');
      var end = moment();

      var referrals = new Vue({
        el: '#referrals-activity',
        data: {
          search: '',
          pageSize: 10,
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
          dateRange: {
            selectedRange: '7 days',
            start: moment().startOf('day').subtract(7, 'days').format('DD-MM-YYYYTHH:mm:00'),
            end: moment().endOf('day').format('DD-MM-YYYYTHH:mm:00')
          },
          loading: true,
        },
        created: function () {
          this.getCurrentMerchantDetails()
          this.getReferralsActivity()
        },
        methods: {
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
          getReferralsActivity: function (range = 1) {
            console.log(this.dateRange.start, this.dateRange.end);
            let requestData = {
              start: this.dateRange.start,
              end: this.dateRange.end
            };
            if (range) {
              requestData.days = range;
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
              this.loading = false
              this.referralsActivity.ready = true
            })
          },
          changeDateRange: function (range) {
            let start, end;
            if (range == 7) {
                this.dateRange.selectedRange = '7 days';
                start = moment().startOf('day').subtract(7, 'days');
                end = moment().endOf('day');
            } else if (range == 30) {
                this.dateRange.selectedRange = '30 days';
                start = moment().startOf('day').subtract(30, 'days');
                end = moment().endOf('day');
            }
            this.dateRange.start = start.format('DD-MM-YYYYTHH:mm:00');
            this.dateRange.end = end.format('DD-MM-YYYYTHH:mm:00');
            this.getReferralsActivity(range)
          },
        },
        computed: {
          sortedActivity: function () {
            let activity = this.referralsActivity.data
            if (this.search) {
              return activity.filter(
                item => item.customer_name.toUpperCase().includes(this.search.toUpperCase()) || item.referrer_name.toUpperCase().includes(this.search.toUpperCase()) || item.order_number.toUpperCase().includes(this.search.toUpperCase())
              )
            }
            return activity
          },
          exportActivitiesUrl: function () {
              start = this.dateRange.start;
              end = this.dateRange.end;
            return `{!!route('referrals.activity.export')!!}?start=${start}&end=${end}&search=${this.search}`;
          },
        }
      })

      /* Date Range Scripts */
      function cb(start, end) {
          referrals.dateRange.selectedRange = 'custom';
          start = moment(start).startOf('day');
          end = moment(end).startOf('day');
          end.add(1, 'day'); // it affects on ranges declared below
          referrals.dateRange.start = start.format('DD-MM-YYYYTHH:mm:00');
          referrals.dateRange.end = end.format('DD-MM-YYYYTHH:mm:00');
          referrals.getReferralsActivity();
        }

        $('#dataRange').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment().startOf('day'), moment().endOf('day')],
                'Yesterday': [moment().startOf('day').subtract(1, 'days'), moment().endOf('day').subtract(1, 'days')],
                'Last 7 Days': [moment().startOf('day').subtract(7, 'days'), moment().subtract(1, 'days').endOf('day')],
                'Last 30 Days': [moment().startOf('day').subtract(30, 'days'), moment().subtract(1, 'days').endOf('day')],
                'This Month': [moment().startOf('month'), moment().endOf('day')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);
    </script>
@endsection
