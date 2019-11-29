<div class="row">
	<div class="col-md-12">
		<div class="well well-chart p-l-50 p-r-50 p-t-30 p-b-30">
			<div class="overflow">
				<div class="pull-left bolder m-b-0 f-s-18">
					Points Earned
				</div>
				<div class="pull-right" v-bind:class="getState(pointsEarned) == 'up' ? 'color-green': 'color-red' ">
					<span class="bolder f-s-18">
						<span v-text="getPercent(pointsEarned)"></span>
					</span>
					<i style="color: inherit;" class="m-l-10 f-s-26" v-bind:class="getState(pointsEarned) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:360px; width:100%">
				<canvas-line-chart 
					:labels="pointsEarned.chart.labels"
					:data="pointsEarned.chart.data"
					:tooltip="pointsEarned.chart.tooltip"
					:color="'#ffab63'"
					:background="'#fbecde'"
					:key="renewChart"
					:skip-labels="skipLabels"></canvas-line-chart>
			</div>
		</div>
	</div>
</div>

<div class="row m-t-30">
	<div class="col-md-12">
		<div class="well well-chart p-l-50 p-r-50 p-t-30 p-b-30">
			<div class="overflow">
				<div class="pull-left bolder m-b-0 f-s-18">
					Completed Earning Actions
				</div>
				<div class="pull-right" v-bind:class="getState(completedEarningActions) == 'up' ? 'color-green': 'color-red' ">
					<span class="bolder f-s-18">
						@{{completedEarningActions.value | to-float}}
					</span>
					<i style="color: inherit;" class="m-l-10 f-s-26" v-bind:class="getState(completedEarningActions) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:360px; width:100%">
				<canvas-line-chart 
					:labels="completedEarningActions.chart.labels"
					:data="completedEarningActions.chart.data"
					:tooltip="completedEarningActions.chart.tooltip"
					:color="'#3e75fa'"
					:background="'#e4ecff'"
					:ymax="countMax(completedEarningActions.chart.data)"
					:key="renewChart"
					:skip-labels="skipLabels"></canvas-line-chart>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-12">
        <div class="well well-table m-t-20" v-cloak>
            <sortable-table 
                :title="'Popular Earning Actions'" 
                :page-size="10"
                :contents="pointsEarned.popularEarningData"
                sort-by="points_earned"
                direction="center"
                ref="table"
                :thead="[{text: 'Name', name: 'name'}, {text: 'Action Type', name: 'action_type'}, {text: 'Reward', name: 'reward'}, {text: 'Points Earned', name: 'points_earned'} , {text: 'Completed Actions', name: 'completed_actions'}]">
                    
                <template slot-scope="{row}">
                    <td>
                        @{{row.name}}
                    </td>
                    <td>
                        @{{row.action_type}}
                    </td>
                    <td>
                        @{{row.reward}}
                    </td>
                    <td>
                        @{{row.points_earned | to-float}}
                    </td>
                    <td>
                        @{{row.completed_actions | format-number}}
                    </td>
                </template> 

            </sortable-table>
        </div>
	</div>
</div>
