<div class="row">
	<div class="col-md-12">
		<div class="well well-chart p-l-50 p-r-50 p-t-30 p-b-30">
			<div class="overflow">
				<div class="pull-left bolder m-b-0 f-s-18">
					Total Investment
				</div>
				<div class="pull-right" v-bind:class="getState(investment) == 'up' ? 'color-green': 'color-red' ">

					<span class="bolder f-s-18" v-text="getPercent(investment)"></span>

					<i style="color: inherit;" class="m-l-10 f-s-26" v-bind:class="getState(investment) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>

				</div>
			</div>
			<div class="chart-container" style="position: relative; height:360px; width:100%">
				<canvas-line-chart 
					:labels="investment.chart.labels"
					:data="investment.chart.data"
					:tooltip="investment.chart.tooltip"
					:color="'#58db7d'"
					:background="'#c5f2d1'"
					:symbol="currencySign"
					:ymax="countMax(investment.chart.data)"
					:key="renewChart"
					:skip-labels="skipLabels"></canvas-line-chart>
			</div>
		</div>
	</div>
</div>

<div class="row m-t-30">
	<div class="col-md-3 d-p-r-10">
		<div class="chart-card card">
			<div class="card-desc text-center">
				Order Discounts
			</div>
			<div class="card-total bolder text-center">
				@{{investment.orderDiscounts | to-float | currency(currencySign)}}
			</div>
		</div>
	</div>
	<div class="col-md-3 d-p-l-10 d-p-r-10">
		<div class="chart-card card">
			<div class="card-desc text-center">
				Free Product Discounts
			</div>
			<div class="card-total bolder text-center">
				@{{investment.freeProductDiscounts | to-float | currency(currencySign)}}
			</div>
		</div>
	</div>
	<div class="col-md-3 d-p-l-10 d-p-r-10">
		<div class="chart-card card">
			<div class="card-desc text-center">
				Free Shipping Discounts
			</div>
			<div class="card-total bolder text-center">
				@{{investment.freeShippingDiscounts | to-float | currency(currencySign)}}
			</div>
		</div>
	</div>
	<div class="col-md-3 d-p-l-10">
		<div class="chart-card card">
			<div class="card-desc text-center">
				Lootly Plan Cost
			</div>
			<div class="card-total bolder text-center">
				@{{investment.lootlyPlanCost | to-float | currency(currencySign)}}
			</div>
		</div>
	</div>
</div>
