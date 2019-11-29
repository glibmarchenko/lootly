<div class="row section-border-bottom p-t-25 p-b-25">
	<div class="col-md-6 col-12">
		<div class="well">
			<div :class="{ 'loading' : loading }" v-cloak>
				<div class="border-bottom p-b-10">
					<label class="bolder f-s-15 m-b-0">
						Spending Settings
					</label>
				</div>
				<div class="spending-settings m-t-15">
					<div class="row">
						<div class="col-12">
							<label>Title</label>
							<input name="spending.title" placeholder="Spending title" class="form-control" v-model="form.spending.title">
						</div>
					</div>

					<div class="row m-t-15">
						<div class="col-md-12">
							<label class="m-b-5 pull-left">Spending Rewards</label> 
							<a class="color-blue bolder f-s-14 pull-right" @click="addAction('spending')">Add</a>
						</div>
					</div>

					<draggable :list="form.spending.selectedActions">
	                    <div class="row draggable-rewards-action"
	                         v-for="(action, index) in form.spending.selectedActions">
	                        <div class="col-md-12 col-12">
	                        	<span class="handle"><i class="fa fa-bars" aria-hidden="true"></i></span>

	                            <b-form-select v-model="action.id" @input="computeActions('spending')">                            	
										 <option v-if="isActions(form.spending)" disabled="" value="">Select a reward</option>
										 <option v-else disabled="" value="">No rewards available</option>					
		                	    	<option v-for="option in form.spending.options" 
		                	    			:value="option.id"
		                	    			:class="{'d-none': notSelectedAction('spending', option.id, action.id)}"
												v-text="option.points +' ('+ option.title +')'"></option>

	                            </b-form-select>
	                            <button class="btn btn-default" type="button"
	                                    @click="deleteAction('spending', index)">
	                                <i class="fa fa-trash-o f-s-19"></i>
	                            </button>
	                        </div>
	                    </div>
					</draggable>
				</div>
			</div>
		</div>
		<div class="well m-t-20">
			<div :class="{ 'loading' : loading }" v-cloak>
				<div class="border-bottom p-b-10">
					<label class="bolder f-s-15 m-b-0">
						Spending Design
					</label>
				</div>
				<div class="spending-settings m-t-15">
					<div class="row">
						<div class="col-6">
							<label>Title Color</label>
							<colorpicker :color="form.spending.design.titleColor" v-model="form.spending.design.titleColor" name="spending.design.titleColor"/>
						</div>
						<div class="col-6">
							<label>Box Text Color</label>
							<colorpicker :color="form.spending.design.boxTextColor" v-model="form.spending.design.boxTextColor" name="spending.design.boxTextColor"/>
						</div>
					</div>

					<div class="row m-t-15">
						<div class="col-6">
							<label>Title Font Size</label>
							<input-number max="50" min="10" v-model="form.spending.design.titleFontSize"></input-number>
						</div>
						<div class="col-6">
							<label>Box Font Size</label>
							<input-number max="35" min="10" v-model="form.spending.design.boxFontSize"></input-number>
						</div>
					</div>
					<div class="row m-t-15">
						<div class="col-md-6 col-12">
							<label>Box Color</label>
							<colorpicker :color="form.spending.design.boxColor" v-model="form.spending.design.boxColor" name="spending.design.boxColor"/>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6 col-12">
		<div class="sticky-top" style="background: #fff;padding-bottom: 10px;">
			<div class="rewards-page-component">
				<div ref="spendingSection">
					<rewards-page-spending :data="form.spending" :title="replacePointsTag(form.spending.title)"></rewards-page-spending>
				</div>
			</div>
			<p v-if="form.spending.actions.length == 0" style="padding: 0 10px 10px; text-align: center;">There are no actions selected.</p>
		</div>
	</div>
</div>
