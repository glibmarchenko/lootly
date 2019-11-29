<div class="row section-border-bottom p-t-25 p-b-25">
	<div class="col-md-6 col-12">
		<div class="well">
			<div :class="{ 'loading' : loading }" v-cloak>
				<div class="border-bottom p-b-10">
					<label class="bolder f-s-15 m-b-0">
						How it works Settings
					</label>
				</div>
				<div class="how-it-works-settings m-t-15">
					<div class="row">
						<div class="col-12">
							<label>Title</label>
							<input name="howItWorks.title" placeholder="How it works title" class="form-control" v-model="form.howItWorks.title">
						</div>
					</div>
					<div class="row m-t-15">
						<div class="col-6">
							<label>Step 1</label>
							<input name="howItWorks.step1" placeholder="Step 1 text" class="form-control" v-model="form.howItWorks.step1">
						</div>
						<div class="col-6">
							<label>Step 2</label>
							<input name="howItWorks.step2" placeholder="Step 2 text" class="form-control" v-model="form.howItWorks.step2">
						</div>
						<div class="col-6 m-t-15">
							<label>Step 3</label>
							<input name="howItWorks.step3" placeholder="Step 3 text" class="form-control" v-model="form.howItWorks.step3">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="well m-t-20">
			<div :class="{ 'loading' : loading }" v-cloak>
				<div class="border-bottom p-b-10">
					<label class="bolder f-s-15 m-b-0">
						How it works Design
					</label>
				</div>
				<div class="how-it-works-settings m-t-15">
					<div class="row">
						<div class="col-6">
							<label>Title Color</label>
							<colorpicker :color="form.howItWorks.design.titleColor" v-model="form.howItWorks.design.titleColor" name="howItWorks.design.titleColor"/>
						</div>
						<div class="col-6">
							<label>Steps Color</label>
							<colorpicker :color="form.howItWorks.design.stepsColor" v-model="form.howItWorks.design.stepsColor" name="howItWorks.design.stepsColor"/>
						</div>
					</div>

					<div class="row m-t-15">
						<div class="col-6">
							<label>Title Font Size</label>
							<input-number max="50" min="10" v-model="form.howItWorks.design.titleFontSize"></input-number>
						</div>
						<div class="col-6">
							<label>Steps Font Size</label>
							<input-number max="35" min="10" v-model="form.howItWorks.design.stepsFontSize"></input-number>
						</div>
					</div>

					<div class="row m-t-15">
						<div class="col-6">
							<label>Circle Full Color</label>
							<colorpicker :color="form.howItWorks.design.circleFullColor" v-model="form.howItWorks.design.circleFullColor" name="howItWorks.design.circleFullColor"/>
						</div>
						<div class="col-6">
							<label>Circle Empty Color</label>
							<colorpicker :color="form.howItWorks.design.circleEmptyColor" v-model="form.howItWorks.design.circleEmptyColor" name="howItWorks.design.circleEmptyColor"/>
						</div>
					</div>

					<div class="row m-t-15">
						<div class="col-6">
							<label>Arrows Color</label>
							<colorpicker :color="form.howItWorks.design.arrowsColor" v-model="form.howItWorks.design.arrowsColor" name="howItWorks.design.arrowsColor"/>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6 col-12">
		<div class="sticky-top">
			<div class="rewards-page-component">
				<div class="table-responsive" ref="howItWorksSection">
					<rewards-page-how-it-works :data="form.howItWorks"></rewards-page-how-it-works>
				</div>
			</div>
		</div>
	</div>
</div>
