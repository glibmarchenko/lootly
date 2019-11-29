@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div id="dashboard" class="loader" v-cloak>
        <div class="customer-accounts-alert alert alert-warning"
             v-if="ecommerceIntegration.slug === 'shopify' && !merchantSettings.customer_accounts_enabled">
		<span class="img">
			<img src="{{ url('images/icons/fa-warning.png') }}" width="40">
		</span>
            <p>You will need to enable <b>Customer Accounts</b> on Shopify for Lootly to begin working for your
                customers. It only takes a few seconds to enable this setting, <a @click="customerAccountsSwal">click
                    here for instructions.</a></p>
        </div>
        <div class="row m-t-20 p-b-30">
            <div class="col-md-6 col-12">
                <h3 class="page-title m-t-0 color-dark">Dashboard</h3>
            </div>
            <div class="col-md-6 col-12 text-right">
                <div :class="{ 'sm-loading loading' : loading }" v-cloak>
                    <div class="btn-group date-range-buttons" role="group" aria-label="Basic example">
                        <button type="button" v-bind:class="[dateRange.selectedRange == 'custom' ? 'active' : '']"
                                id="chartRange" class="btn btn-default pull-right">Custom
                        </button>
                        <button type="button" @click="changeDateRange(30)"
                                v-bind:class="[dateRange.selectedRange == '30 days' ? 'active' : '']"
                                class="btn btn-default">30 Days
                        </button>
                        <button type="button" @click="changeDateRange(7)"
                                v-bind:class="[dateRange.selectedRange == '7 days' ? 'active' : '']"
                                class="btn btn-default">7 Days
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="chart-cards">
            <div class="chart-column">
                <input class="d-none" v-model="chartType" id="opt1" value="New Program Members" name="chartType"
                       type="radio">
                <label for="opt1">
                    <div class="chart-card card blue"
                         v-bind:class="[chartType == 'New Program Members' ? 'active' : '']">
                        <div class="card-desc">
                            New Program Members
                            <span class="bold f-s-15 pull-right"
                                  v-text="getPercent(newMembers.currentNum, newMembers.pastNum)"></span>
                        </div>
                        <div class="card-total">
						<span class="bolder">
							@{{ newMembers.currentNum | to-float }}
						</span>
                            <i :class="parseInt(newMembers.currentNum) > parseInt(newMembers.pastNum) ?
										'icon-upward-arrow m-t-15 pull-right' :
										'icon-downward-arrow m-t-15 pull-right'"
                            ></i>
                        </div>
                    </div>
                </label>
            </div>
            <div class="chart-column">
                <input class="d-none" v-model="chartType" id="opt2" value="Points Earned" name="chartType" type="radio">
                <label for="opt2">
                    <div class="chart-card card green" v-bind:class="[chartType == 'Points Earned' ? 'active' : '']">
                        <div class="card-desc">
                            Points Earned
                            <span class="bold f-s-15 pull-right"
                                  v-text="getPercent(points.earned.currentNum, points.earned.pastNum)"></span>
                        </div>
                        <div class="card-total">
						<span class="bolder">
							@{{ points.earned.currentNum | to-float }}
						</span>
                            <i :class="parseInt(points.earned.currentNum) > parseInt(points.earned.pastNum) ?
									'icon-upward-arrow m-t-15 pull-right' :
									'icon-downward-arrow m-t-15 pull-right'"
                            ></i>
                        </div>
                    </div>
                </label>
            </div>
            <div class="chart-column">
                <input class="d-none" v-model="chartType" id="opt3" value="Activities Completed" name="chartType"
                       type="radio">
                <label for="opt3">
                    <div class="chart-card card pink"
                         v-bind:class="[chartType == 'Activities Completed' ? 'active' : '']">
                        <div class="card-desc">
                            Activities Completed
                            <span class="bold f-s-15 pull-right"
                                  v-text="getPercent(activities.currentNum, activities.pastNum)"></span>
                        </div>
                        <div class="card-total">
						<span class="bolder">
							@{{ activities.currentNum | to-float }}
						</span>
                            <i :class="parseInt(activities.currentNum) > parseInt(activities.pastNum) ?
								'icon-upward-arrow m-t-15 pull-right' :
								'icon-downward-arrow m-t-15 pull-right'"
                            ></i>
                        </div>
                    </div>
                </label>
            </div>
            <div class="chart-column">
                <input class="d-none" v-model="chartType" id="opt4" value="Value Generated" name="chartType"
                       type="radio">
                <label for="opt4">
                    <div class="chart-card card orange" v-bind:class="[chartType == 'Value Generated' ? 'active' : '']">
                        <div class="card-desc">
                            Value Generated
                            <span class="bold f-s-15 pull-right"
                                  v-text="getPercent(valueGenerated.currentNum, valueGenerated.pastNum)"></span>
                        </div>
                        <div class="card-total">
						<span class="bolder">
							{!! $currency_sign !!}@{{valueGenerated.currentNum | to-float }}
						</span>
                            <i :class="parseInt(valueGenerated.currentNum) > parseInt(valueGenerated.pastNum) ?
								'icon-upward-arrow m-t-15 pull-right' :
								'icon-downward-arrow m-t-15 pull-right'"></i>
                        </div>
                    </div>
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="dashboard-chart well">
				<span :class="{'loading': chartLoading}">
					<p class="bolder m-b-40 f-s-18">
						@{{chartType}}
					</p>
					<div id="chartContainer"
                         class="chart-container"
                         style="position: relative; height:400px; width:100%">
						<!-- <canvas id="dashboardChart"></canvas> -->
					</div>
				</span>
                </div>
            </div>
        </div>
        <div class="row m-t-25">
            <div class="col-md-4 m-t-5">
                <div class="well well-table">
                    <table class="table sm-table">
                        <thead>
                        <tr>
                            <th class="color-dark-grey f-s-17">Latest Points</th>
                            <th class="text-right m-w-85">
                                <a href="{{ route('points.activity', ['typeSearch' => 'earned']) }}" class="bolder f-s-14 color-blue">View All</a>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($latestPoints as $point)
                            <tr>
                                <td class="p-r-0" colspan="2">

                                @if($point->point_value == 0)
                                  <span class="badge badge-pill pull-right m-r-10">
                                    {!! $point->point_value !!}
                                  </span>
                                  <p><a class="bold color-blue f-s-15"
                                        href="{!! route('customers.show', $point->customer->id) !!}">{!! $point->customer->name !!}</a>
                                  </p>
                                  <p class="m-t-5">
                                      <span class="f-s-13">{!! $point->getActionName() !!}</span>
                                      -
                                      <span class="color-light-grey f-s-12">@{{ "{!! $point->created_at !!}" | date-human}}</span>
                                  </p>

                                @else

                                  <span class="badge badge-pill pull-right m-r-10 {!! ($point->point_value > 0) ? 'badge-success' : 'badge-danger'; !!}">
                                        {!! $point->point_value > 0 ? '+' . $point->point_value : $point->point_value !!}</span>
                                    <p><a class="bold color-blue f-s-15"
                                          href="{!! route('customers.show', $point->customer->id) !!}">{!! $point->customer->name !!}</a>
                                    </p>
                                    <p class="m-t-5">
                                        <span class="f-s-13">{!! $point->getActionName() !!}</span>
                                        -
                                        <span class="color-light-grey f-s-12">@{{ "{!! $point->created_at !!}" | date-human}}</span>
                                    </p>
                                @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4 m-t-5">
                <div class="well well-table">
                    <table class="table sm-table">
                        <thead>
                        <tr>
                            <th class="color-dark-grey f-s-17">Latest Referrals</th>
                            <th class="text-right m-w-100">
                                <a href="{{ route('referrals.activity') }}" class="bolder f-s-14 color-blue">View
                                    All</a>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($latestRefferals as $order)
                            <tr>
                                <td class="p-r-0" colspan="2">
                                    <p class="bold color-dark pull-right m-r-10 f-s-15">{!! $currency_sign !!}{!! $order->total_price !!}</p>
                                    <p><a class="bold color-blue f-s-15"
                                          href="{!! route('customers.show', $order->customer_id) !!}">{!! $order->name !!}</a>
                                    </p>
                                    <p class="m-t-5">
                                        @if($order->referring_customer_id)
                                            <span class="f-s-13">Referred by </span><a
                                                    href="{!! route('customers.show', $order->referring_customer_id) !!}">{!! $order->referral->name !!}</a>
                                            -
                                        @endif
                                        <span class="color-light-grey f-s-12">@{{ "{!! $order->created_at !!}" | date-human }}</span>
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4 m-t-5">
                <div class="well well-table">
                    <table class="table sm-table">
                        <thead>
                        <tr>
                            <th class="color-dark-grey f-s-17">Latest Rewards</th>
                            <th class="text-right m-w-100">
                                <a href="{{ route('points.activity', ['typeSearch' => 'spent']) }}" class="bolder f-s-14 color-blue">View All</a>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($latestRewards as $reward)
                            <tr>
                                <td class="p-r-0" colspan="2">
                                    <span class="badge badge-pill badge-danger pull-right m-r-10">{!! $reward->point_value !!}</span>
                                    <p><a class="bold color-blue f-s-15"
                                          href="{!! route('customers.show', $reward->customer_id) !!}">{!! $reward->customer->name !!}</a>
                                    </p>
                                    <p class="m-t-5">
                                        <span class="f-s-13">{!! $reward->getActionName() !!}</span>
                                        -
                                        <span class="color-light-grey f-s-12">@{{ "{!! $reward->created_at !!}" | date-human }}</span>
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css"/>
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <script>
      var start = moment().subtract(30, 'days')
      var end = moment()

      var dashboard = new Vue({
        el: '#dashboard',
        data: {
          section: 'customersStatistic',
          chartType: 'New Program Members',
          chart: {
            labels: [],
            data: []
          },
          dateRange: {
            selectedRange: '30 days',
            start: moment().subtract(30, 'days'),
            end: moment()
          },
          newMembers: {
            currentNum: '{!! $customersStatistic['currentNum'] !!}',
            pastNum: '{!! $customersStatistic['pastNum'] !!}',
            chartData: {
              labels: [
                  @foreach($customersStatistic['chartData']['labels'] as $label)
                    '{!! $label !!}',
                  @endforeach
              ],
              data: [
                  @foreach($customersStatistic['chartData']['data'] as $data)
                  {!! $data !!},
                  @endforeach
              ],
              tooltip: [
                  @foreach($customersStatistic['chartData']['tooltip'] as $data)
                    '{!! $data !!}',
                  @endforeach
              ]
            }
          },
          points: {
            earned: {
              currentNum: '{!! $pointsStatistic['currentNum'] !!}',
              pastNum: '{!! $pointsStatistic['pastNum'] !!}',
              chartData: {
                labels: [
                    @foreach($pointsStatistic['chartData']['labels'] as $label)
                      '{!! $label !!}',
                    @endforeach
                ],
                data: [
                    @foreach($pointsStatistic['chartData']['data'] as $data)
                    {!! $data !!},
                    @endforeach
                ],
                tooltip: [
                    @foreach($customersStatistic['chartData']['tooltip'] as $data)
                      '{!! $data !!}',
                    @endforeach
                ]
              }
            },
            spent: {},
          },
          activities: {
            currentNum: '{!! $activities['currentNum'] !!}',
            pastNum: '{!! $activities['pastNum'] !!}',
            chartData: {
              labels: [
                  @foreach($activities['chartData']['labels'] as $label)
                    '{!! $label !!}',
                  @endforeach
              ],
              data: [
                  @foreach($activities['chartData']['data'] as $data)
                  {!! $data !!},
                  @endforeach
              ],
              tooltip: [
                  @foreach($customersStatistic['chartData']['tooltip'] as $data)
                    '{!! $data !!}',
                  @endforeach
              ]
            }
          },
          valueGenerated: {
            currentNum: '{!! $valueStatistic['currentNum'] !!}',
            pastNum: '{!! $valueStatistic['pastNum'] !!}',
            chartData: {
              labels: [
                  @foreach($valueStatistic['chartData']['labels'] as $label)
                    '{!! $label !!}',
                  @endforeach
              ],
              data: [
                  @foreach($valueStatistic['chartData']['data'] as $data)
                  {!! $data !!},
                  @endforeach
              ],
              tooltip: [
                  @foreach($customersStatistic['chartData']['tooltip'] as $data)
                    '{!! $data !!}',
                  @endforeach
              ]
            }
          },
          currency_sign: '{!! $currency_sign !!}',
          chartLoading: true,
          loading: true,
          merchantId: Spark.state.currentTeam.id,
          merchantSettings: {
            name: '',
            logo: '{{ config('email-notification.default_logo') }}',
            remove_branding: false,
            points: {
              singular_name: 'point',
              plural_name: 'points'
            },
            currency: {
              id: null,
              sign: '$',
              name: 'USD',
              displaySign: true,
              selectedFormat: '$'
            },
            customer_accounts_enabled: false
          },
          ecommerceIntegration: {
            slug: null
          }
        },
        created: function () {
          this.loading = false
          this.getDataForRange()
          if (this.merchantId) {
            this.getMerchantSettings()
            this.getEcommerceIntegration()
          }
        },
        methods: {
          getMerchantSettings: function () {
            axios.get('/api/merchants/' + this.merchantId + '/settings/common').then(response => {
              if (response.data && response.data.data) {
                let merchantSettings = response.data.data
                // Get merchant settings
                this.merchantSettings.name = merchantSettings.name
                if (merchantSettings.logo) { this.merchantSettings.logo = merchantSettings.logo }

                // Get email notification settings
                if (merchantSettings.email_notification_settings && merchantSettings.email_notification_settings.data) {
                  this.merchantSettings.remove_branding = !!merchantSettings.email_notification_settings.data.remove_branding
                }

                // Get currency settings
                this.merchantSettings.currency.displaySign = !!merchantSettings.currency_display_sign
                if (merchantSettings.merchant_currency && merchantSettings.merchant_currency.data) {
                  this.merchantSettings.currency.id = merchantSettings.merchant_currency.data.id
                  this.merchantSettings.currency.sign = merchantSettings.merchant_currency.data.currency_sign
                  this.merchantSettings.currency.name = merchantSettings.merchant_currency.data.name
                  this.merchantSettings.currency.selectedFormat = (this.merchantSettings.currency.displaySign) ? this.merchantSettings.currency.sign : this.merchantSettings.currency.name
                }
                // Get points settings
                if (merchantSettings.points_settings && merchantSettings.points_settings.data) {
                  this.merchantSettings.points.singular_name = merchantSettings.points_settings.data.name
                  this.merchantSettings.points.plural_name = merchantSettings.points_settings.data.plural_name
                }

                // Get customer accounts settings
                this.merchantSettings.customer_accounts_enabled = !!merchantSettings.customer_accounts_enabled
              }
            }).catch((errors) => {
              console.log(errors)
            })
          },
          getEcommerceIntegration: function () {
            axios.get('/api/merchants/' + this.merchantId + '/integrations/ecommerce/active').then((res) => {
              if (res.data && res.data.data) {
                this.ecommerceIntegration = res.data.data
              }
            }).catch((err) => {
              console.log(err)
            })
          },
          changeDateRange: function (range) {
            if (range == 7) {
              this.dateRange.selectedRange = '7 days'
              this.dateRange.start = moment().startOf('day').add(1, 'day').subtract(7, 'days').format('DD-MM-YYYYTHH:mm:00')
              this.dateRange.end = moment().startOf('day').add(1, 'day').format('DD-MM-YYYYTHH:mm:00')
              this.getDataForRange()
            } else if (range == 30) {
              this.dateRange.selectedRange = '30 days'
              this.dateRange.start = moment().startOf('day').add(1, 'day').subtract(30, 'days').format('DD-MM-YYYYTHH:mm:00')
              this.dateRange.end = moment().startOf('day').add(1, 'day').format('DD-MM-YYYYTHH:mm:00')
              this.getDataForRange()
            }
          },
          getDataForRange (start = null, end = null) {
            if (start) this.dateRange.start = start.format('DD-MM-YYYYTHH:mm:00')
            if (end) this.dateRange.end = end.format('DD-MM-YYYYTHH:mm:00')
            this.loading = true
            let data = this.dateRange
            data.section = this.section
            axios.post('{!! route("dashboard.get-data") !!}', data)
              .then((response) => {
                const data = response.data
                this.activities = data.activities
                this.newMembers = data.customersStatistic
                this.points.earned = data.pointsStatistic
                this.valueGenerated = data.valueStatistic
                this.setDataToChart(this.chartType) // renew chart data
                this.loading = false
              }).catch((error) => {
              console.warn(error)
              this.loading = false
            })
          },
          getPercent: function (first, second) {
            if (second == 0) {
              return 'None'
            }
            var sing = parseInt(first) > parseInt(second) ? '+' : '-'
            var num = Math.round(Math.abs(first - second) / second * 100)
            return sing + Vue.options.filters['format-number'](num) + '%'
          },
          setDataToChart: function (chartType) {
            switch (chartType) {
              case 'New Program Members':
                this.chart = this.newMembers.chartData
                this.section = 'customersStatistic'
                break
              case 'Points Earned':
                this.chart = this.points.earned.chartData
                this.section = 'pointsStatistic'
                break
              case 'Activities Completed':
                this.chart = this.activities.chartData
                this.section = 'activities'
                break
              case 'Value Generated':
                this.chart = this.valueGenerated.chartData
                this.section = 'valueStatistic'
                break
            }
            initChart()
          },
          customerAccountsSwal: function () {
            var content = document.createElement('div')
            content.innerHTML = '<div class="text-left">1. Click the <b>Settings</b> button on the bottom left menu of your Shopify account. <br> 2. Once on the Settings screen, click on <b>Checkout</b>. <br> 3. Scroll down to <b>Customer Accounts</b>, and select either Optional or Required, than click Save.<br><br> You\'re all set now!</div>'
            swal({
              className: 'customer-accounts-swal',
              title: 'Customer Accounts on Shopify',
              content: content,
              icon: '/images/permissions/integrations.png',
              buttons: {
                catch: {
                  text: 'Close',
                  value: 'close',
                }
              },
            }).then((value) => {
            })
          }
        },
        computed: {
          color: function () {
            if (this.chartType == 'New Program Members') {
              return '3e75fa'
            } else if (this.chartType == 'Points Earned') {
              return '58db7d'
            } else if (this.chartType == 'Activities Completed') {
              return 'ff7390'
            } else if (this.chartType == 'Value Generated') {
              return 'ffab63'
            }
          },
          fillColor: function () {
            if (this.chartType == 'New Program Members') {
              return 'dce4f9'
            } else if (this.chartType == 'Points Earned') {
              return 'c5f2d1'
            } else if (this.chartType == 'Activities Completed') {
              return 'fee4e9'
            } else if (this.chartType == 'Value Generated') {
              return 'fbecde'
            }
          }
        },
        watch: {
          color: function () {
            // initChart()
          },
          chartType: function () {
            this.setDataToChart(this.chartType)
            this.getDataForRange()
          },
        }
      })

      /* Date Range Scripts */
      function cb (start, end) {
        dashboard.dateRange.selectedRange = 'custom'
        start = moment(start).startOf('day')
        end = moment(end).startOf('day')
        end.add(1, 'day') // it affects on ranges declared below
        dashboard.dateRange.start = start.format('DD-MM-YYYYTHH:mm:00')
        dashboard.dateRange.end = end.format('DD-MM-YYYYTHH:mm:00')
        dashboard.getDataForRange()
      }

      $('#chartRange').daterangepicker({
        locale: {
          format: 'YYYY-MM-DD'
        },
        startDate: start,
        endDate: end,
        ranges: {
          'Today': [moment().startOf('day'), moment().startOf('day')],
          'Yesterday': [moment().startOf('day').subtract(1, 'days'), moment().startOf('day').subtract(1, 'days')],
          'Last 7 Days': [moment().startOf('day').subtract(7, 'days'), moment().subtract(1, 'days').startOf('day')],
          'Last 30 Days': [moment().startOf('day').subtract(30, 'days'), moment().subtract(1, 'days').startOf('day')],
          'This Month': [moment().startOf('month'), moment().endOf('day')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
      }, cb)

      /* Chart Scripts */
      function hexToRgb (hex, op) {
        var bigint = parseInt(hex, 16)
        var r = (bigint >> 16) & 255
        var g = (bigint >> 8) & 255
        var b = bigint & 255
        return 'rgba(' + r + ',' + g + ',' + b + ',' + op + ')'
      }

      window.onload = function () {
        dashboard.chart = dashboard.newMembers.chartData
        initChart()
      }

      function initChart () {
        dashboard.chartLoading = true

        if (document.getElementById('dashboardChart'))
          document.getElementById('dashboardChart').remove()

        var ctx = document.createElement('canvas')
        ctx.id = 'dashboardChart'
        document.getElementById('chartContainer').appendChild(ctx)

        ctx = ctx.getContext('2d')
        var gradient = ctx.createLinearGradient(0, 0, 0, 700)
        gradient.addColorStop(0, hexToRgb(dashboard.fillColor, '1'))
        gradient.addColorStop(0.5, hexToRgb('ffffff', '1'))
        gradient.addColorStop(1, hexToRgb('ffffff', '1'))

        var options = {
          type: 'line',
          data: {
            labels: dashboard.chart.labels,
            datasets: [{
              data: dashboard.chart.data,
              lineTension: 0,
              fill: true,
              backgroundColor: gradient,
              borderColor: '#' + dashboard.color,
              borderWidth: 3,
              pointBorderColor: '#' + dashboard.color,
              pointBackgroundColor: '#' + dashboard.color,
              pointRadius: 0,
              pointHoverRadius: 10,
              pointHitRadius: 10,
              pointHoverBackgroundColor: '#' + dashboard.color,
              pointHoverBorderColor: '#fff',
              pointHoverBorderWidth: 3,

            }]
          },
          options: {
            maintainAspectRatio: false,
            responsive: true,
            tooltips: {
              enabled: false,
              custom: getTooltip,
              // position: 'average',
              yAlign: 'bottom',
              backgroundColor: '#fff',
              titleFontColor: '#000',
              titleFontSize: 16,
              yPadding: 18,
              xPadding: 20,
              borderWidth: 1,
              borderColor: '#ddd',
              titleFontFamily: 'sans-serif',
              callbacks: {
                label: function (tooltipItem, data) {
                  var label = dashboard.chartType + ': '
                  if (dashboard.chartType == 'Value Generated') {
                    label += '{!! $currency_sign !!}'
                    label += tooltipItem.yLabel ? tooltipItem.yLabel : '0'
                  } else {
                    label += tooltipItem.yLabel ? tooltipItem.yLabel : '0'
                  }
                  return label
                },
                title: function (tooltipItem, data) {
                  return dashboard.chart.tooltip[tooltipItem[0].index]
                },
                labelTextColor: function (tooltipItem, chart) {
                  return '#000'
                }
              }
            },
            scales: {
              yAxes: [{
                stacked: false,
                ticks: {
                  callback: function (value, index, values) {
                    if (dashboard.chartType == 'Value Generated') {
                      return '{!! $currency_sign !!}' + value
                    }
                    return value
                  },
                  beginAtZero: true,
                  suggestedMax: Math.max(...dashboard.chart.data) ? Math.ceil(Math.max(...dashboard.chart.data) * 1.1) : 1,
                  maxTicksLimit: 6,
                  fontStyle: 700,
                  padding: 20,
                  autoSkip: false,
                },
                gridLines: {
                  drawBorder: false,
                  zeroLineColor: '#eaeaea',
                  color: '#eaeaea',
                  drawTicks: false
                },
              }],
              xAxes: [{
                stacked: false,
                gridLines: {
                  drawBorder: false,
                  display: false
                },
                ticks: {
                  maxRotation: 0,
                  minRotation: 0,
                  fontStyle: 600,
                  autoSkip: false,
                }
              }],
            },
            legend: {
              display: false
            }
          }
        }

        var chart = new Chart(ctx, options)

        dashboard.chartLoading = false
      }

      function getTooltip (tooltipModel) {
        // Tooltip Element
        var tooltipEl = document.getElementById('chartjs-tooltip')

        // Create element on first render
        if (!tooltipEl) {
          tooltipEl = document.createElement('div')
          tooltipEl.id = 'chartjs-tooltip'
          tooltipEl.innerHTML = '<table></table>'
          document.body.appendChild(tooltipEl)
        }

        // Hide if no tooltip
        if (tooltipModel.opacity === 0) {
          tooltipEl.style.opacity = 0
          return
        }

        // Set caret Position
        tooltipEl.classList.remove('above', 'below', 'no-transform')
        if (tooltipModel.yAlign) {
          tooltipEl.classList.add(tooltipModel.yAlign)
        } else {
          tooltipEl.classList.add('no-transform')
        }

        function getBody (bodyItem) {
          return bodyItem.lines
        }

        // Set Text
        if (tooltipModel.body) {
          var titleLines = tooltipModel.title || []
          var bodyLines = tooltipModel.body.map(getBody)

          var innerHtml = '<thead>'

          titleLines.forEach(function (title) {
            innerHtml += '<tr><th>' + title + '</th></tr>'
          })
          innerHtml += '</thead><tbody>'

          bodyLines.forEach(function (body, i) {
            var colors = tooltipModel.labelColors[i]
            var style = 'background:' + colors.backgroundColor
            style += '; border-color:' + colors.borderColor
            style += '; border-width: 2px'
            var span = '<span style="' + style + '"></span>'
            innerHtml += '<tr><td>' + span + body + '</td></tr>'
          })
          innerHtml += '</tbody>'

          var tableRoot = tooltipEl.querySelector('table')
          tableRoot.innerHTML = innerHtml
        }
        // `this` will be the overall tooltip
        var position = this._chart.canvas.getBoundingClientRect()
        // Display, position, and set styles for font
        tooltipEl.style.opacity = 1
        tooltipEl.style.position = 'absolute'
        tooltipEl.style.left = position.left + window.pageXOffset + tooltipModel.caretX + 'px'
        tooltipEl.style.top = position.top + window.pageYOffset + tooltipModel.caretY + 'px'
        tooltipEl.style.fontFamily = tooltipModel._bodyFontFamily
        tooltipEl.style.fontSize = tooltipModel.bodyFontSize + 'px'
        tooltipEl.style.fontStyle = tooltipModel._bodyFontStyle
        tooltipEl.style.padding = tooltipModel.yPadding + 'px ' + tooltipModel.xPadding + 'px'
        tooltipEl.style.pointerEvents = 'none'
        tooltipEl.style.backgroundColor = '#fff'
        tooltipEl.style.border = 'solid 1px #eee'
        tooltipEl.style.borderRadius = '11px'
        tooltipEl.style.left = position.left + window.pageXOffset + tooltipModel.caretX - tooltipEl.offsetWidth / 2 + 'px'
        tooltipEl.style.top = position.top + window.pageYOffset + tooltipModel.caretY - tooltipEl.offsetHeight - 5 + 'px'
      }
    </script>
@endsection