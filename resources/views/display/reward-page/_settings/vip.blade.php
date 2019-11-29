<div class="row section-border-bottom p-t-25 p-b-25">
	<div class="col-md-6 col-12">
		<div class="well">
			<div :class="{ 'loading' : loading }" v-cloak>
				<div class="border-bottom p-b-10">
					<label class="bolder f-s-15 m-b-0">
						VIP Settings
					</label>
				</div>
				<div class="vip-settings m-t-15">
					<div class="row">
						<div class="col-12">
							<label>Title</label>
							<input name="vip.title" placeholder="VIP Title" class="form-control" v-model="form.vip.title">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="well m-t-20">
			<div :class="{ 'loading' : loading }" v-cloak>
				<div class="border-bottom p-b-10">
					<label class="bolder f-s-15 m-b-0">
						VIP Design
					</label>
				</div>
				<div class="vip-settings m-t-15">
					<div class="row">
						<div class="col-6">
							<label>Title Color</label>
							<colorpicker :color="form.vip.design.titleColor" v-model="form.vip.design.titleColor" name="vip.design.titleColor"/>
						</div>
						<div class="col-6">
							<label>Tier Name Color</label>
							<colorpicker :color="form.vip.design.tierNameColor" v-model="form.vip.design.tierNameColor" name="vip.design.tierNameColor"/>
						</div>
					</div>

					<div class="row m-t-15">
						<div class="col-6">
							<label>Title Font Size</label>
							<input-number max="50" min="10" v-model="form.vip.design.titleFontSize"></input-number>
						</div>
						<div class="col-6">
							<label>Tier Name Font Size</label>
							<input-number max="35" min="10" v-model="form.vip.design.tierNameFontSize"></input-number>
						</div>
					</div>
					<div class="row m-t-15">
						<div class="col-6">
							<label>Multiplier Text Color</label>
							<colorpicker :color="form.vip.design.multiplierColor" v-model="form.vip.design.multiplierColor" name="vip.design.multiplierColor"/>
						</div>
						<div class="col-6">
							<label>Requirements Color</label>
							<colorpicker :color="form.vip.design.requirementsColor" v-model="form.vip.design.requirementsColor" name="vip.design.requirementsColor"/>
						</div>
					</div>

					<div class="row m-t-15">
						<div class="col-6">
							<label>Multiplier Font Size</label>
							<input-number max="50" min="10" v-model="form.vip.design.multiplierFontSize"></input-number>
						</div>
						<div class="col-6">
							<label>Requirements Font Size</label>
							<input-number max="50" min="10" v-model="form.vip.design.requirementsFontSize"></input-number>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6 col-12">
		<div class="sticky-top">
			<div class="rewards-page-component" style="background: #fff;">
				<div ref="vipSection">
					<rewards-page-vip :data="form.vip" :title="replacePointsTag(form.vip.title)"></rewards-page-vip>
				</div>
			</div>
		</div>
	</div>
</div>
