@extends('layouts.app')

@section('title', 'Program Overview')

@section('content')
<div id="reports-overview" class="loader m-b-40" v-cloak>
	<div class="row m-t-20 p-b-30">
		<div class="col-md-6 col-12">
			<h3 class="page-title m-t-0 color-dark">Program Overview</h3>
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
						@{{valueStatistic.value | to-float | currency(currencySign)}}
					</div>
				</div>
			</label>
		</div>
		<div class="chart-column">
			<input class="d-none" v-model="tabIndex" id="opt2" value="2" type="radio">
			<label for="opt2">
				<div class="chart-card outline card green" v-bind:class="[tabIndex == '2' ? 'active' : '']">
					<div class="card-desc bold">
						Investment
					</div>
					<div class="card-total bolder">
						@{{investment.value | to-float | currency(currencySign)}}
					</div>
				</div>
			</label>
		</div>
		<div class="chart-column">
			<input class="d-none" v-model="tabIndex" id="opt3" value="3" type="radio">
			<label for="opt3">
				<div class="chart-card outline card orange" v-bind:class="[tabIndex == '3' ? 'active' : '']">
					<div class="card-desc bold">
						Points Earned
					</div>
					<div class="card-total bolder">
						@{{pointsEarned.value | to-float }}
					</div>
				</div>
			</label>
		</div>
		<div class="chart-column">
			<input class="d-none" v-model="tabIndex" id="opt4" value="4" type="radio">
			<label for="opt4">
				<div class="chart-card outline card pink" v-bind:class="[tabIndex == '4' ? 'active' : '']">
					<div class="card-desc bold">
						Rewards Issued
					</div>
					<div class="card-total bolder">
						@{{rewardsIssued.rewards.value | to-float }}
					</div>
				</div>
			</label>
		</div>
	</div>

	<span v-show="tabIndex == 1">
		@include('reports._overview-partials.value-generated')
	</span>

	<span v-show="tabIndex == 2">
		@include('reports._overview-partials.investment')
	</span>

	<span v-show="tabIndex == 3">
		@include('reports._overview-partials.points-earned')
	</span>

	<span v-show="tabIndex == 4">
		@include('reports._overview-partials.rewards-issued ')
	</span>

</div>
@endsection

@section('scripts')
	<style type="text/css">
		body {
			overflow-y: scroll;
		}
	</style>
   <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
	<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>

	<script>
		var start = moment().startOf('day').subtract(30, 'days');
      var end = moment().startOf('day').add(1, 'day');
		
		var overview = new Vue({
			el: "#reports-overview",
			data: {
				tabIndex: 1,
				valueStatistic: {
					value: {!! $valueStatistic['currentNum'] !!},
					pastValue: {!! $valueStatistic['pastNum'] !!},
					chart: {
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
							@foreach($valueStatistic['chartData']['tooltip'] as $tooltip)
								"{!! $tooltip !!}",
							@endforeach
						]
					}
				},
				rewardRevenue: {
					value: {!! $rewardRevenue['currentNum'] !!},
					pastValue: {!! $rewardRevenue['pastNum'] !!},
					chart: {
						labels: [
							@foreach($rewardRevenue['chartData']['labels'] as $label)
								'{!! $label !!}',
							@endforeach
						],
						data: [
							@foreach($rewardRevenue['chartData']['data'] as $data)
								{!! $data !!},
							@endforeach
						],
						tooltip: [
							@foreach($rewardRevenue['chartData']['tooltip'] as $tooltip)
								"{!! $tooltip !!}",
							@endforeach
						]
					}
				},
				referralRevenue: {
					value: {!! $referralRevenue['currentNum'] !!},
					pastValue: {!! $referralRevenue['pastNum'] !!},
					chart: {
						labels: [
							@foreach($referralRevenue['chartData']['labels'] as $label)
								'{!! $label !!}',
							@endforeach
						],
						data: [
							@foreach($referralRevenue['chartData']['data'] as $data)
								{!! $data !!},
							@endforeach
						],
						tooltip: [
							@foreach($referralRevenue['chartData']['tooltip'] as $tooltip)
								"{!! $tooltip !!}",
							@endforeach
						]
					}
				},
				rewardOrderCount: {
					value: {!! $rewardOrderCount['currentNum'] !!},
					pastValue: {!! $rewardOrderCount['pastNum'] !!},
					chart: {
						labels: [
							@foreach($rewardOrderCount['chartData']['labels'] as $label)
								'{!! $label !!}',
							@endforeach
						],
						data: [
							@foreach($rewardOrderCount['chartData']['data'] as $data)
								{!! $data !!},
							@endforeach
						],
						tooltip: [
							@foreach($rewardOrderCount['chartData']['tooltip'] as $tooltip)
								"{!! $tooltip !!}",
							@endforeach
						]
					}
				},
				referralOrderCount: {
					value: {!! $referralOrderCount['currentNum'] !!},
					pastValue: {!! $referralOrderCount['pastNum'] !!},
					chart: {
						labels: [
							@foreach($referralOrderCount['chartData']['labels'] as $label)
								'{!! $label !!}',
							@endforeach
						],
						data: [
							@foreach($referralOrderCount['chartData']['data'] as $data)
								{!! $data !!},
							@endforeach
						],
						tooltip: [
							@foreach($referralOrderCount['chartData']['tooltip'] as $tooltip)
								"{!! $tooltip !!}",
							@endforeach
						]
					}
				},
				investment: {
					value: parseFloat('{!! $investment["currentNum"] !!}'),
					pastValue: parseFloat('{!! $investment["pastNum"] !!}'),
					orderDiscounts: parseFloat('{!! $investment["orderDiscounts"] !!}'),
					freeProductDiscounts: parseFloat('{!! $investment["freeProductDiscounts"] !!}'),
					freeShippingDiscounts: parseFloat('{!! $investment["freeShippingDiscounts"] !!}'),
					lootlyPlanCost: parseFloat('{!! $investment["lootlyPlanCost"] !!}'),
					chart: {
						labels: [
							@foreach($investment['chartData']['labels'] as $label)
								'{!! $label !!}',
							@endforeach
						],
						data: [
							@foreach($investment['chartData']['data'] as $data)
								parseFloat('{!! $data !!}'),
							@endforeach
						],
						tooltip: [
							@foreach($investment['chartData']['tooltip'] as $tooltip)
								"{!! $tooltip !!}",
							@endforeach
						]
					},
				},
				pointsEarned: {
					value: {!! $pointsEarned['currentNum'] !!},
					pastValue: {!! $pointsEarned['pastNum'] !!},
					chart: {
						labels: [
							@foreach($pointsEarned['chartData']['labels'] as $label)
								'{!! $label !!}',
							@endforeach
						],
						data: [
							@foreach($pointsEarned['chartData']['data'] as $data)
								{!! $data !!},
							@endforeach
						],
						tooltip: [
							@foreach($pointsEarned['chartData']['tooltip'] as $tooltip)
								"{!! $tooltip !!}",
							@endforeach
						]
					},
					pointName: "{!! $pointsEarned['pointName'] !!}",
					pointNamePlural: "{!! $pointsEarned['pointNamePlural'] !!}",
					popularEarningData: {!! json_encode($pointsEarned['popularEarningData']) !!}
				},
				completedEarningActions: {
					value: {!! $completedEarningActions['currentNum'] !!},
					pastValue: {!! $completedEarningActions['pastNum'] !!},
					chart: {
						labels: [
							@foreach($completedEarningActions['chartData']['labels'] as $label)
								'{!! $label !!}',
							@endforeach
						],
						data: [
							@foreach($completedEarningActions['chartData']['data'] as $data)
								{!! $data !!},
							@endforeach
						],
						tooltip: [
							@foreach($completedEarningActions['chartData']['tooltip'] as $tooltip)
								"{!! $tooltip !!}",
							@endforeach
						]
					},
				},
				rewardsIssued: {
					rewards: {
						value: {!! $rewardsIssued['rewards']['currentNum'] !!},
						pastValue: {!! $rewardsIssued['rewards']['pastNum'] !!},
						chart: {
							labels: [
								@foreach($rewardsIssued['rewards']['chartData']['labels'] as $label)
									'{!! $label !!}',
								@endforeach
							],
							data: [
								@foreach($rewardsIssued['rewards']['chartData']['data'] as $data)
									{!! $data !!},
								@endforeach
							],
							tooltip: [
								@foreach($rewardsIssued['rewards']['chartData']['tooltip'] as $tooltip)
									"{!! $tooltip !!}",
								@endforeach
							]
						},
					},
					redemptions: {
						value: {!! $rewardsIssued['redemptions']['currentNum'] !!},
						pastValue: {!! $rewardsIssued['redemptions']['pastNum'] !!},
						chart: {
							labels: [
								@foreach($rewardsIssued['redemptions']['chartData']['labels'] as $label)
									'{!! $label !!}',
								@endforeach
							],
							data: [
								@foreach($rewardsIssued['redemptions']['chartData']['data'] as $data)
									{!! $data !!},
								@endforeach
							],
							tooltip: [
								@foreach($rewardsIssued['redemptions']['chartData']['tooltip'] as $tooltip)
									"{!! $tooltip !!}",
								@endforeach
							]
						},
					},
					popularSpendingData: {!! $rewardsIssued['popularSpendingData'] ? json_encode($rewardsIssued['popularSpendingData']) : '"There are no rewards for the selected time period"' !!}
				},
				dateRange: {
					selectedRange: '30 days',
					start: moment().startOf('day').add(1, 'day').subtract(30, 'days'),
					end: moment().startOf('day').add(1, 'day')
				},
				renewChart: 0,
				loading: false,
				currencySign: '{!! $currencySign !!}',
				skipLabels: false, // setup auto skip for chart`s labels
			},
            created: function () {
					if(window.location.search){
						tabIndex = window.location.search.substring(1).split('tabId=')[1].split('&')[0];
						if(tabIndex >= 0 && tabIndex <= 4) this.tabIndex = tabIndex;
					}
               // this.popularEarningData();
					// this.popularSpendingData();
            },
			methods: {
				popularEarningData: function() {
					axios.get('/demo-data/popular-earning').then((response) => {
						this.pointsEarned.popularEarningData = response.data;
					});
				},
				popularSpendingData: function() {
					axios.get('/demo-data/popular-spending').then((response) => {
						this.rewardsIssued.popularSpendingData = response.data;
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
					axios.post('{!! route("reports.overview.get-data") !!}', this.dateRange)
						.then((response) => {
							const data = response.data
							if (data.rewardsIssued.popularSpendingData.length == 0) {
								data.rewardsIssued.popularSpendingData = 'There are no rewards for the selected time period'
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
			},
		});
		

		/* Date Range Scripts */
		function cb(start, end) {
			overview.dateRange.selectedRange = 'custom';
			start = moment(start).startOf('day');
			end = moment(end).startOf('day');
			end.add(1, 'day'); // it affects on ranges declared below
			overview.dateRange.start = start.format('DD-MM-YYYYTHH:mm:00');
			overview.dateRange.end = end.format('DD-MM-YYYYTHH:mm:00');
			overview.getDataForRange();
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