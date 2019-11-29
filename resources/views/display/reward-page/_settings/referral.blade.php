<div class="row section-border-bottom p-t-25 p-b-25">
	<div class="col-md-6 col-12">
		<div class="well">
			<div :class="{ 'loading' : loading }" v-cloak>
				<div class="border-bottom p-b-10">
					<label class="bolder f-s-15 m-b-0">
						Referral Settings
					</label>
				</div>
				<div class="referral-settings m-t-15">
					<div class="row">
						<div class="col-12">
							<label>Title</label>
							<input name="referral.title" placeholder="Referral title" class="form-control" v-model="form.referral.title">
						</div>
						<div class="col-12 m-t-10">
							<label>Subtitle</label>
							<input name="referral.subtitle" placeholder="Referral subtitle" class="form-control" v-model="form.referral.subtitle">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="well m-t-20">
			<div :class="{ 'loading' : loading }" v-cloak>
				<div class="border-bottom p-b-10">
					<label class="bolder f-s-15 m-b-0">
						Referral Design
					</label>
				</div>
				<div class="referral-settings m-t-15">
					<div class="row">
						<div class="col-6">
							<label>Title Color</label>
							<colorpicker :color="form.referral.design.titleColor" v-model="form.referral.design.titleColor" name="referral.design.titleColor"/>
						</div>
						<div class="col-6">
							<label>Subtitle Color</label>
							<colorpicker :color="form.referral.design.subtitleColor" v-model="form.referral.design.subtitleColor" name="referral.design.subtitleColor"/>
						</div>
					</div>

					<div class="row m-t-15">
						<div class="col-6">
							<label>Title Font Size</label>
							<input-number max="50" min="10" v-model="form.referral.design.titleFontSize"></input-number>
						</div>
						<div class="col-6">
							<label>Subtitle Font Size</label>
							<input-number max="35" min="10" v-model="form.referral.design.subtitleFontSize"></input-number>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6 col-12">
		<div class="sticky-top">
			<div class="rewards-page-component">
				<div ref="referralSection">
					<rewards-page-referral :data="form.referral"></rewards-page-referral>
				</div>
			</div>
		</div>
	</div>
</div>
