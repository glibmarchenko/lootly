<div class="row">
	<div class="col-md-12">
		<div class="well well-chart p-l-50 p-r-50 p-t-30 p-b-30">
			<div class="overflow">
				<div class="pull-left bolder m-b-0 f-s-18">
					Total Shares
				</div>
				<div class="pull-right" v-bind:class="getState(shares) == 'up' ? 'color-green': 'color-red' ">
					<span class="bolder f-s-18">
						<span v-text="getPercent(shares)"></span>
					</span>
					<i style="color: inherit;" class="m-l-10 f-s-26" v-bind:class="getState(shares) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:360px; width:100%">
				<canvas-line-chart 
					:labels="shares.chart.labels"
					:data="shares.chart.data"
					:tooltip="shares.chart.tooltip"
					:color="'#58db7d'"
					:background="'#c5f2d1'"
					:ymax="countMax(shares.chart.data)"
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
					Facebook Shares
				</div>
				<div class="pull-right" v-bind:class="getState(sharesFacebook) == 'up' ? 'color-green': 'color-red' ">
					
					<span class="bolder f-s-17">
						@{{ sharesFacebook.value | format-number }}
					</span>

					<i style="color: inherit;" class="m-l-10 f-s-20" v-bind:class="getState(sharesFacebook) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:270px; width:100%">
				<canvas-line-chart 
					:labels="sharesFacebook.chart.labels"
					:data="sharesFacebook.chart.data"
					:tooltip="sharesFacebook.chart.tooltip"
					:color="'#3b5998'"
					:background="'#d4d9e3'"
					:ymax="countMax(sharesFacebook.chart.data)"
					:key="renewChart"
					:skip-labels="skipLabels"></canvas-line-chart>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="well well-chart p-l-20 p-r-20 p-t-30 p-b-30">
			<div class="overflow">
				<div class="pull-left bolder m-b-0 f-s-18">
					Twitter Shares
				</div>
				<div class="pull-right" v-bind:class="getState(sharesTwitter) == 'up' ? 'color-green': 'color-red' ">

					<span class="bolder f-s-17">
						@{{ sharesTwitter.value | format-number }}
					</span>

					<i style="color: inherit;" class="m-l-10 f-s-20" v-bind:class="getState(sharesTwitter) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:270px; width:100%">
				<canvas-line-chart 
					:labels="sharesTwitter.chart.labels"
					:data="sharesTwitter.chart.data"
					:tooltip="sharesTwitter.chart.tooltip"
					:color="'#1da1f3'"
					:background="'#daeffe'"
					:ymax="countMax(sharesTwitter.chart.data)"
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
					Email Shares
				</div>
				<div class="pull-right" v-bind:class="getState(sharesEmail) == 'up' ? 'color-green': 'color-red' ">

					<span class="bolder f-s-17">
						@{{ sharesEmail.value | format-number }}
					</span>

					<i style="color: inherit;" class="m-l-10 f-s-20" v-bind:class="getState(sharesEmail) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:270px; width:100%">
				<canvas-line-chart 
					:labels="sharesEmail.chart.labels"
					:data="sharesEmail.chart.data"
					:tooltip="sharesEmail.chart.tooltip"
					:color="'#fd7591'"
					:background="'#fcdee4'"
					:ymax="countMax(sharesEmail.chart.data)"
					:key="renewChart"
					:skip-labels="skipLabels"></canvas-line-chart>
			</div>
		</div>
	</div>
</div>
