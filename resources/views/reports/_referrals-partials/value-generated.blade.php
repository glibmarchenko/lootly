<div class="row">
	<div class="col-md-12">
		<div class="well well-chart p-l-50 p-r-50 p-t-30 p-b-30">
			<div class="overflow">
				<div class="pull-left bolder m-b-0 f-s-18">
					Total Value Generated
				</div>
				<div class="pull-right" v-bind:class="getState(valueGenerated) == 'up' ? 'color-green': 'color-red' ">
					<span class="bolder f-s-18">
						<span v-text="getPercent(valueGenerated.main)"></span>
					</span>
					<i style="color: inherit;" class="m-l-10 f-s-26" v-bind:class="getState(valueGenerated.main) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:360px; width:100%">
				<canvas-line-chart 
					:labels="valueGenerated.main.chart.labels"
					:data="valueGenerated.main.chart.data"
					:tooltip="valueGenerated.main.chart.tooltip"
					:color="'#3e75fa'"
					:background="'#dce4f9'"
					:symbol="currencySign"
					:ymax="countMax(valueGenerated.main.chart.data)"
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
					Investment
				</div>
				<div class="pull-right" v-bind:class="getState(valueGenerated.investment) == 'up' ? 'color-green': 'color-red' ">
					<span class="bolder f-s-17">
						@{{ valueGenerated.investment.value | format-number | currency(currencySign) }}
					</span>
					<i style="color: inherit;" class="m-l-10 f-s-20" v-bind:class="getState(valueGenerated.investment) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:270px; width:100%">
				<canvas-line-chart 
					:labels="valueGenerated.investment.chart.labels"
					:data="valueGenerated.investment.chart.data"
					:tooltip="valueGenerated.investment.chart.tooltip"
					:color="'#58db7d'"
					:background="'#c5f2d1'"
					:symbol="currencySign"
					:ymax="countMax(valueGenerated.investment.chart.data)"
					:key="renewChart"
					:skip-labels="skipLabels"></canvas-line-chart>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="well well-chart p-l-20 p-r-20 p-t-30 p-b-30">
			<div class="overflow">
				<div class="pull-left bolder m-b-0 f-s-18">
					Order Count
				</div>
				<div class="pull-right" v-bind:class="getState(valueGenerated.orderCount) == 'up' ? 'color-green': 'color-red' ">
					<span class="bolder f-s-17">
						@{{ valueGenerated.orderCount.value | format-number }}
					</span>
					<i style="color: inherit;" class="m-l-10 f-s-20" v-bind:class="getState(valueGenerated.orderCount) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:270px; width:100%">
				<canvas-line-chart 
					:labels="valueGenerated.orderCount.chart.labels"
					:data="valueGenerated.orderCount.chart.data"
					:tooltip="valueGenerated.orderCount.chart.tooltip"
					:color="'#ffab63'"
					:background="'#fbecde'"
					:ymax="countMax(valueGenerated.orderCount.chart.data)"
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
					Average Order Value
				</div>
				<div class="pull-right" v-bind:class="getState(valueGenerated.averageOrderValue) == 'up' ? 'color-green': 'color-red' ">
					<span class="bolder f-s-17">
						@{{ valueGenerated.averageOrderValue.value | to-float | currency(currencySign) }}
					</span>
					<i style="color: inherit;" class="m-l-10 f-s-20" v-bind:class="getState(valueGenerated.averageOrderValue) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:270px; width:100%">
				<canvas-line-chart 
					:labels="valueGenerated.averageOrderValue.chart.labels"
					:data="valueGenerated.averageOrderValue.chart.data"
					:tooltip="valueGenerated.averageOrderValue.chart.tooltip"
					:color="'#ff7390'"
					:background="'#fee4e9'"
					:symbol="currencySign"
					:ymax="countMax(valueGenerated.averageOrderValue.chart.data)"
					:key="renewChart"
					:skip-labels="skipLabels"></canvas-line-chart>
			</div>
		</div>
	</div>
</div>
