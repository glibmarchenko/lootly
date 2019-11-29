<div class="row section-border-bottom p-t-25 p-b-25">
	<div class="col-md-6 col-12">
		<div class="well">
			<div :class="{ 'loading' : loading }" v-cloak>
				<div class="border-bottom p-b-10">
					<label class="bolder f-s-15 m-b-0">
						Earning Settings
					</label>
				</div>
				<div class="earning-settings m-t-15">
					<div class="row">
						<div class="col-12">
							<label>Title</label>
							<input name="earning.title" placeholder="Earning title" class="form-control" v-model="form.earning.title">
						</div>
					</div>
					<div class="row m-t-15">
						<div class="col-md-12">
							<label class="m-b-5 pull-left">Earning Actions</label> 
							<a class="color-blue bolder f-s-14 pull-right" @click="addAction('earning')">Add</a>
						</div>
					</div>
					<draggable :list="form.earning.selectedActions">
	                    <div class="row draggable-rewards-action"
	                         v-for="(action, index) in form.earning.selectedActions">
	                        <div class="col-md-12 col-12">
	                        	<span class="handle"><i class="fa fa-bars" aria-hidden="true"></i></span>

	                            <b-form-select v-model="action.id" @input="computeActions('earning')">                            	
										  <option v-if="isActions(form.earning)" disabled="" value="">Select an action</option>
										  <option v-else disabled="" value="">No actions available</option>										  
		                	    	<option v-for="option in form.earning.options" 
		                	    			:value="option.id"
		                	    			:class="{'d-none': notSelectedAction('earning', option.id, action.id)}">@{{option.title}}</option>

	                            </b-form-select>
	                            <button class="btn btn-default" type="button"
	                                    @click="deleteAction('earning', index)">
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
						Earning Design
					</label>
				</div>
				<div class="earning-settings m-t-15">
					<div class="row">
						<div class="col-6">
							<label>Title Color</label>
							<colorpicker :color="form.earning.design.titleColor" v-model="form.earning.design.titleColor" name="earning.design.titleColor"/>
						</div>
						<div class="col-6">
							<label>Action Text Color</label>
							<colorpicker :color="form.earning.design.actionTextColor" v-model="form.earning.design.actionTextColor" name="earning.design.actionTextColor"/>
						</div>
					</div>

					<div class="row m-t-15">
						<div class="col-6">
							<label>Title Font Size</label>
							<input-number max="50" min="10" v-model="form.earning.design.titleFontSize"></input-number>
						</div>
						<div class="col-6">
							<label>Action Font Size</label>
							<input-number max="35" min="10" v-model="form.earning.design.actionFontSize"></input-number>
						</div>
					</div>
					<div class="row m-t-15">
						<div class="col-6">
							<label>Point Text Color</label>
							<colorpicker :color="form.earning.design.pointColor" v-model="form.earning.design.pointColor" name="earning.design.pointColor"/>
						</div>
						<div class="col-6">
							<label>Ribbon Color</label>
							<colorpicker :color="form.earning.design.ribbonColor" v-model="form.earning.design.ribbonColor" name="earning.design.ribbonColor"/>
						</div>
					</div>

					<div class="row m-t-15">
						<div class="col-6">
							<label>Point Font Size</label>
							<input-number max="50" min="10" v-model="form.earning.design.pointFontSize"></input-number>
						</div>
						<div class="col-6">
							<label>Box Color</label>
							<colorpicker :color="form.earning.design.boxColor" v-model="form.earning.design.boxColor" name="earning.design.boxColor"/>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6 col-12">
		<div class="sticky-top" style="background: #fff;padding-bottom: 10px;">
			<div class="rewards-page-component">
				<div ref="earningSection">
					<rewards-page-earning :data="form.earning" :title="replacePointsTag(form.earning.title)"></rewards-page-earning>
				</div>
			</div>
			<p v-if="form.earning.actions.length == 0" 
			   style=" text-align: center;position: relative;top: -25px;z-index: 999;">There are no actions selected.</p>
		</div>
	</div>
</div>
