@extends('layouts.app')

@section('title', 'Referrals')

@section('content')
<div id="reports-referrals" class="loader m-b-40" v-cloak>
	<div class="row m-t-20 p-b-30">
		<div class="col-md-6 col-12">
			<h3 class="page-title m-t-0 color-dark">Referrals</h3>
		</div>
		<div class="col-md-6 col-12 text-right">
			<div :class="{ 'sm-loading loading' : loading }" v-cloak>
				<div class="btn-group date-range-buttons" role="group" aria-label="Basic example">
					<button type="button" v-bind:class="[dateRange.selectedRange == 'custom' ? 'active' : '']" id="chartRange" class="btn btn-default pull-right">Custom</button>
					<button type="button" @click="changeDateRange(30)" v-bind:class="[dateRange.selectedRange == '30 days' ? 'active' : '']" class="btn btn-default">30 Days</button>
					<button type="button" @click="changeDateRange(7)" v-bind:class="[dateRange.selectedRange == '7 days' ? 'active' : '']" class="btn btn-default">7 Days</button>
				</div>
			</div>
		</div>
	</div>

	<div class="chart-cards">
		<div class="chart-column">
			<input class="d-none" v-model="tabIndex" id="opt1" value="1" type="radio">
			<label for="opt1">
				<div class="chart-card outline card blue" v-bind:class="[tabIndex == '1' ? 'active' : '']">
					<div class="card-desc bold">
						Value Generated
					</div>
					<div class="card-total bolder">
						<span>
							@{{valueGenerated.main.value | to-float | currency(currencySign)}}
						</span>
					</div>
				</div>
			</label>
		</div>
		<div class="chart-column">
			<input class="d-none" v-model="tabIndex" id="opt2" value="2" type="radio">
			<label for="opt2">
				<div class="chart-card outline card green" v-bind:class="[tabIndex == '2' ? 'active' : '']">
					<div class="card-desc bold">
						Shares
					</div>
					<div class="card-total bolder">
						<span>
							@{{shares.value | to-float }}
						</span>
					</div>
				</div>
			</label>
		</div>
		<div class="chart-column">
			<input class="d-none" v-model="tabIndex" id="opt3" value="3" type="radio">
			<label for="opt3">
				<div class="chart-card outline card orange" v-bind:class="[tabIndex == '3' ? 'active' : '']">
					<div class="card-desc bold">
						Clicks
					</div>
					<div class="card-total bolder">
						<span>
							@{{clicks.value | to-float }}
						</span>
					</div>
				</div>
			</label>
		</div>
	</div>

	<span v-show="tabIndex == 1">
		@include('reports._referrals-partials.value-generated')
	</span>

	<span v-show="tabIndex == 2">
		@include('reports._referrals-partials.shares')
	</span>

	<span v-show="tabIndex == 3">
		@include('reports._referrals-partials.clicks')
	</span>
	<div class="row">
		<div class="col-12">
            <div class="well well-table m-t-20" v-cloak>
                <sortable-table 
                    :title="'Top Referrers'" 
                    :contents="referrers"
                    :page-size="10"
                    :sort-by="tableOrder"
                    :thead="[{text: 'Email', name: 'email'}, {text: 'Shares', name: 'shares'}, {text: 'Clicks', name: 'clicks'}, {text: 'Orders', name: 'orders'} , {text: 'Avg Order', name: 'avg_order'} , {text: 'Revenue', name: 'revenue'} ]">
                        
                    <template slot-scope="{row}">
                        <td>
                           <a class="bold color-blue" :href="customerProfileUrl(row.id)">@{{row.email}}</a>
                        </td>
                        <td>
                            @{{row.shares}}
                        </td>
                        <td>
                            @{{row.clicks}}
                        </td>
                        <td>
                            @{{row.orders}}
                        </td>
                        <td>
                            @{{row.avg_order | currency(currencySign)}}
                        </td>
                        <td>
                            @{{row.revenue | currency(currencySign)}}
                        </td>
                    </template> 

                </sortable-table>
            </div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
   <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
	<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
	
	<script>
		var start = moment().startOf('day').subtract(30, 'days');
      var end = moment().startOf('day').add(1, 'day');
		
		var referrals = new Vue({
			el: "#reports-referrals",
			data: {
				tabIndex: 1,
				valueGenerated: {
					main: {!! json_encode($valueGenerated) !!},
					investment: {!! json_encode($investment) !!},
					orderCount: {!! json_encode($orderCount) !!},
					averageOrderValue: {!! json_encode($averageOrderValue) !!},
				},
				shares: {!! json_encode($shares) !!},
				sharesFacebook: {!! json_encode($sharesFacebook) !!},
				sharesTwitter: {!! json_encode($sharesTwitter) !!},
				sharesEmail: {!! json_encode($sharesEmail) !!},
				clicks: {!! json_encode($clicks) !!},
				clicksFacebook: {!! json_encode($clicksFacebook) !!},
				clicksTwitter: {!! json_encode($clicksTwitter) !!},
				clicksEmail: {!! json_encode($clicksEmail) !!},
				referrers: {!! $topReferrers ? json_encode($topReferrers) : '"There are no referrers for the selected time period"' !!},
				dateRange: {
					selectedRange: '30 days',
					start: moment().startOf('day').add(1, 'day').subtract(30, 'days'),
					end: moment().startOf('day').add(1, 'day')
				},
				currencySign: '{!! "$currencySign" !!}',
				pointsSettings: {!! json_encode($pointsData) !!},
				renewChart: 0,
				skipLabels: false, // setup auto skip for chart`s labels
				loading: false,
			},
            created: function () {
            	//Get Data
               //  this.getReferrers();
            },
			methods: {
				getReferrers: function(){
                    axios.get('/demo-data/reports-referrers').then((response) => {
                        this.referrers = response.data;
                    });

				},
				changeTab: function($index) {
					this.tabIndex = $index;

				},
				changeDateRange: function(range){
					if(range == 7){
						this.dateRange.selectedRange = '7 days';
						this.dateRange.start = moment().startOf('day').add(1, 'day').subtract(7, 'days').format('DD-MM-YYYYTHH:mm:00');
						this.dateRange.end = moment().startOf('day').add(1, 'day').format('DD-MM-YYYYTHH:mm:00');
						this.getDataForRange();
					}else if (range == 30){
						this.dateRange.selectedRange = '30 days';
						this.dateRange.start = moment().startOf('day').add(1, 'day').subtract(30, 'days').format('DD-MM-YYYYTHH:mm:00');
						this.dateRange.end = moment().startOf('day').add(1, 'day').format('DD-MM-YYYYTHH:mm:00');
						this.getDataForRange();
					}
				},
				countMax: function(array){
					return Math.max(...array) ? Math.ceil(Math.max(...array)*1.1) : 1
				},
				getDataForRange: function(start = null, end = null){
					if(start) this.dateRange.start = start.format('DD-MM-YYYYTHH:mm:00');
					if(end) this.dateRange.end = end.format('DD-MM-YYYYTHH:mm:00');
					this.loading = true;
					axios.post('{!! route("reports.referrals.get-data") !!}', this.dateRange)
						.then((response) => {
							const data = response.data
							if (data.referrers.length == 0) {
								data.referrers = 'There are no referrers for the selected time period'
							}
							Object.assign(this, data);
							this.renewChart += 1;
							this.loading = false;
						}).catch((error) => {
							console.warn(error);
							this.loading = false;
						});
				},
				getPercent: function(object){
					if(object.pastValue == 0) {
						return 'None';
					}
					var num = Math.round(Math.abs(object.value - object.pastValue) / object.pastValue * 100);
					return num + '%';
				},
				getState: function(object){
					if(object.value > object.pastValue){
						return 'up';
					}
					return 'else';
				},
				customerProfileUrl: function(customerId){
					$templateUrl = "{{ route('customers.show', 'template') }}";
					$url = $templateUrl.replace('template', customerId);
					return $url;
				}
			},
			computed: {
				tableOrder: function() {
                	return this.tabIndex == 1 ? 'revenue' : this.tabIndex == 2 ? 'shares' : this.tabIndex == 3 ? 'clicks' : '';
				}
			}
		});
		

		/* Date Range Scripts */
        function cb(start, end) {
			referrals.dateRange.selectedRange = 'custom';
			start = moment(start).startOf('day');
			end = moment(end).startOf('day');
			end.add(1, 'day'); // it affects on ranges declared below
			referrals.dateRange.start = start.format('DD-MM-YYYYTHH:mm:00');
			referrals.dateRange.end = end.format('DD-MM-YYYYTHH:mm:00');
			referrals.getDataForRange();
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
        }, cb);
        		
	</script>
@endsection