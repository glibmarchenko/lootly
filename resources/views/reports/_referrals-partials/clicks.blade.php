<div class="row">
	<div class="col-md-12">
		<div class="well well-chart p-l-50 p-r-50 p-t-30 p-b-30">
			<div class="overflow">
				<div class="pull-left bolder m-b-0 f-s-18">
					Total Clicks
				</div>
				<div class="pull-right" v-bind:class="getState(clicks) == 'up' ? 'color-green': 'color-red' ">
					<span class="bolder f-s-18">
						<span v-text="getPercent(clicks)"></span>
					</span>
					<i style="color: inherit;" class="m-l-10 f-s-26" v-bind:class="getState(clicks) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:360px; width:100%">
				<canvas-line-chart 
					:labels="clicks.chart.labels"
					:data="clicks.chart.data"
					:tooltip="clicks.chart.tooltip"
					:color="'#ffab63'"
					:background="'#fbecde'"
					:ymax="countMax(clicks.chart.data)"
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
					Facebook Link Clicks
				</div>
				<div class="pull-right" v-bind:class="getState(clicksFacebook) == 'up' ? 'color-green': 'color-red' ">
					<span class="bolder f-s-17">
						@{{ clicksFacebook.value | format-number }}
					</span>
					<i style="color: inherit;" class="m-l-10 f-s-20" v-bind:class="getState(clicksFacebook) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:270px; width:100%">
				<canvas-line-chart 
					:labels="clicksFacebook.chart.labels"
					:data="clicksFacebook.chart.data"
					:tooltip="clicksFacebook.chart.tooltip"
					:color="'#3b5998'"
					:background="'#d4d9e3'"
					:ymax="countMax(clicksFacebook.chart.data)"
					:key="renewChart"
					:skip-labels="skipLabels"></canvas-line-chart>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="well well-chart p-l-20 p-r-20 p-t-30 p-b-30">
			<div class="overflow">
				<div class="pull-left bolder m-b-0 f-s-18">
					Twitter Link Clicks
				</div>
				<div class="pull-right" v-bind:class="getState(clicksTwitter) == 'up' ? 'color-green': 'color-red' ">
					<span class="bolder f-s-17">
						@{{ clicksTwitter.value | format-number }}
					</span>
					<i style="color: inherit;" class="m-l-10 f-s-20" v-bind:class="getState(clicksTwitter) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:270px; width:100%">
				<canvas-line-chart 
					:labels="clicksTwitter.chart.labels"
					:data="clicksTwitter.chart.data"
					:tooltip="clicksTwitter.chart.tooltip"
					:color="'#1da1f3'"
					:background="'#daeffe'"
					:ymax="countMax(clicksTwitter.chart.data)"
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
					Email Link Clicks
				</div>
				<div class="pull-right" v-bind:class="getState(clicksEmail) == 'up' ? 'color-green': 'color-red' ">

					<span class="bolder f-s-17">
						@{{ clicksEmail.value | format-number }}
					</span>

					<i style="color: inherit;" class="m-l-10 f-s-20" v-bind:class="getState(clicksEmail) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:270px; width:100%">
				<canvas-line-chart 
					:labels="clicksEmail.chart.labels"
					:data="clicksEmail.chart.data"
					:tooltip="clicksEmail.chart.tooltip"
					:color="'#fd7591'"
					:background="'#fcdee4'"
					:ymax="countMax(clicksEmail.chart.data)"
					:key="renewChart"
					:skip-labels="skipLabels"></canvas-line-chart>
			</div>
		</div>
	</div>
</div>
