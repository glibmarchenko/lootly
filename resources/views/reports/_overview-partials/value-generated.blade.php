<div class="row">
	<div class="col-md-12">
		<div class="well well-chart p-l-50 p-r-50 p-t-30 p-b-30">
			<div class="overflow">
				<div class="pull-left bolder m-b-0 f-s-18">
					Total Value Generated
				</div>
				<div class="pull-right" v-bind:class="getState(valueStatistic) == 'up' ? 'color-green': 'color-red' ">
					<span class="bolder f-s-18">
						<span v-text="getPercent(valueStatistic)"></span>
					</span>
					<i style="color: inherit;" class="m-l-10 f-s-26" v-bind:class="getState(valueStatistic) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:360px; width:100%">
				<canvas-line-chart 
					:labels="valueStatistic.chart.labels"
					:data="valueStatistic.chart.data"
					:tooltip="valueStatistic.chart.tooltip"
					:color="'#3e75fa'"
					:background="'#dce4f9'"
					:symbol="currencySign"
					:ymax="countMax(valueStatistic.chart.data)"
					:key="renewChart"
					:skip-labels="skipLabels"></canvas-line-chart>
			</div>
		</div>
	</div>
</div>

<div class="row m-t-30">
	<div class="col-md-6">
		<div class="well well-chart p-l-20 p-r-20 p-t-30 p-b-30">
			<div class="overflow">
				<div class="pull-left bolder m-b-0 f-s-18">
					Reward Revenue
				</div>
				<div class="pull-right" v-bind:class="getState(rewardRevenue) == 'up' ? 'color-green': 'color-red' ">
					<span class="bolder f-s-17">
						@{{rewardRevenue.value | to-float | currency(currencySign)}}
					</span>
					<i style="color: inherit;" class="m-l-10 f-s-20" v-bind:class="getState(rewardRevenue) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:270px; width:100%">
				<canvas-line-chart 
					:labels="rewardRevenue.chart.labels"
					:data="rewardRevenue.chart.data"
					:tooltip="valueStatistic.chart.tooltip"
					:color="'#58db7d'"
					:background="'#c5f2d1'"
					:symbol="currencySign"
					:ymax="countMax(rewardRevenue.chart.data)"
					:key="renewChart"
					:skip-labels="skipLabels"></canvas-line-chart>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="well well-chart p-l-20 p-r-20 p-t-30 p-b-30">
			<div class="overflow">
				<div class="pull-left bolder m-b-0 f-s-18">
					Referral Revenue
				</div>
				<div class="pull-right" v-bind:class="getState(referralRevenue) == 'up' ? 'color-green': 'color-red' ">
					<span class="bolder f-s-17">
						@{{referralRevenue.value | to-float | currency(currencySign)}}
					</span>
					<i style="color: inherit;" class="m-l-10 f-s-20" v-bind:class="getState(referralRevenue) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:270px; width:100%">
				<canvas-line-chart 
					:labels="referralRevenue.chart.labels"
					:data="referralRevenue.chart.data"
					:tooltip="referralRevenue.chart.tooltip"
					:color="'#58db7d'"
					:background="'#c5f2d1'"
					:symbol="currencySign"
					:ymax="countMax(referralRevenue.chart.data)"
					:key="renewChart"
					:skip-labels="skipLabels"></canvas-line-chart>
			</div>
		</div>
	</div>
</div>

<div class="row m-t-30">
	<div class="col-md-6">
		<div class="well well-chart p-l-20 p-r-20 p-t-30 p-b-30">
			<div class="overflow">
				<div class="pull-left bolder m-b-0 f-s-18">
					Reward Order Count
				</div>
				<div class="pull-right" v-bind:class="getState(rewardOrderCount) == 'up' ? 'color-green': 'color-red' ">
					<span class="bolder f-s-17">
						@{{rewardOrderCount.value | to-float}}
					</span>
					<i style="color: inherit;" class="m-l-10 f-s-20" v-bind:class="getState(rewardOrderCount) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:270px; width:100%">
				<canvas-line-chart 
					:labels="rewardOrderCount.chart.labels"
					:data="rewardOrderCount.chart.data"
					:tooltip="rewardOrderCount.chart.tooltip"
					:color="'#ffab63'"
					:background="'#fbecde'"
					:ymax="countMax(rewardOrderCount.chart.data)"
					:key="renewChart"
					:skip-labels="skipLabels"></canvas-line-chart>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="well well-chart p-l-20 p-r-20 p-t-30 p-b-30">
			<div class="overflow">
				<div class="pull-left bolder m-b-0 f-s-18">
					Referral Order Count
				</div>
				<div class="pull-right" v-bind:class="getState(referralOrderCount) == 'up' ? 'color-green': 'color-red' ">

					<span class="bolder f-s-17">
						@{{referralOrderCount.value | to-float}}
					</span>

					<i style="color: inherit;" class="m-l-10 f-s-20" v-bind:class="getState(referralOrderCount) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:270px; width:100%">
				<canvas-line-chart 
					:labels="referralOrderCount.chart.labels"
					:data="referralOrderCount.chart.data"
					:tooltip="referralOrderCount.chart.tooltip"
					:color="'#ffab63'"
					:background="'#fbecde'"
					:ymax="countMax(referralOrderCount.chart.data)"
					:key="renewChart"
					:skip-labels="skipLabels"></canvas-line-chart>
			</div>
		</div>
	</div>
</div>
