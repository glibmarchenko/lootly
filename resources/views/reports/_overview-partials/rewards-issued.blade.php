<div class="row">
	<div class="col-md-12">
		<div class="well well-chart p-l-50 p-r-50 p-t-30 p-b-30">
			<div class="overflow">
				<div class="pull-left bolder m-b-0 f-s-18">
					Rewards Issued
				</div>
				<div class="pull-right" v-bind:class="getState(rewardsIssued.rewards) == 'up' ? 'color-green': 'color-red' ">
					<span class="bolder f-s-18">
						<span v-text="getPercent(rewardsIssued.rewards)"></span>
					</span>
					<i style="color: inherit;" class="m-l-10 f-s-26" v-bind:class="getState(rewardsIssued.rewards) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:360px; width:100%">
				<canvas-line-chart 
					:labels="rewardsIssued.rewards.chart.labels"
					:data="rewardsIssued.rewards.chart.data"
					:tooltip="rewardsIssued.rewards.chart.tooltip"
					:color="'#ff7390'"
					:background="'#fee4e9'"
					:ymax="countMax(rewardsIssued.rewards.chart.data)"
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
					Redemptions
				</div>
				<div class="pull-right" v-bind:class="getState(rewardsIssued.redemptions) == 'up' ? 'color-green': 'color-red' ">
					
					<span class="bolder f-s-18">
						@{{rewardsIssued.redemptions.value | to-float}}
					</span>

					<i style="color: inherit;" class="m-l-10 f-s-26" v-bind:class="getState(rewardsIssued.redemptions) == 'up' ? 'icon-upward-arrow': 'icon-downward-arrow' "></i>
				</div>
			</div>
			<div class="chart-container" style="position: relative; height:360px; width:100%">
				<canvas-line-chart 
					:labels="rewardsIssued.redemptions.chart.labels"
					:data="rewardsIssued.redemptions.chart.data"
					:tooltip="rewardsIssued.redemptions.chart.tooltip"
					:color="'#ffab63'"
					:background="'#fbecde'"
					:ymax="countMax(rewardsIssued.redemptions.chart.data)"
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
                :title="'Popular Spending Rewards'" 
                :page-size="10"
                :contents="rewardsIssued.popularSpendingData"
                sort-by="rewards_issued"
                direction="center"
                :thead="[{text: 'Name', name: 'name'}, {text: 'Reward Type', name: 'reward_type'}, {text: 'Points Required', name: 'points_required'}, {text: 'Rewards Issued', name: 'rewards_issued'} , {text: 'Redemptions', name: 'redemption_count'}]">
                    
                <template slot-scope="{row}">
                    <td>
                        @{{row.name}}
                    </td>
                    <td>
                        @{{row.reward_type}}
                    </td>
                    <td>
                        @{{row.points_required || 'N/A'}}
                    </td>
                    <td>
                        @{{row.rewards_issued | to-float}}
                    </td>
                    <td>
                        @{{row.redemption_count | to-float}}
                    </td>
                </template> 

            </sortable-table>
        </div>
	</div>
</div>
