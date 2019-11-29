<div class="row section-border-bottom p-t-25 p-b-25">
	<div class="col-md-6 col-12">
		<div class="well">
			<div :class="{ 'loading' : loading }" v-cloak>
				<div class="border-bottom p-b-10">
					<label class="bolder f-s-15 m-b-0">
						Header Settings
					</label>
				</div>
				<div class="header-settings m-t-15">
					<div class="row">
						<div class="col-12">
							<label>Title</label>
							<input name="header.title" placeholder="Header title" class="form-control" v-model="form.header.title">
						</div>
					</div>
					<div class="row m-t-15">
						<div class="col-12">
							<label>Subtitle</label>
							<input name="header.subtitle" placeholder="Header subtitle" class="form-control" v-model="form.header.subtitle">
						</div>
					</div>
					<div class="row m-t-15">
						<div class="col-6">
							<label>Button 1</label>
							<input name="header.button1" placeholder="Button 1 text" class="form-control" v-model="form.header.button1">
						</div>
						<div class="col-6">
							<label>Button 2</label>
							<input name="header.button2" placeholder="Button 2 text" class="form-control" v-model="form.header.button2">
						</div>
					</div>
					<div class="row m-t-15">
						<div class="col-12">
							<label>Button 1 Link</label>
							<input name="header.button1Link" placeholder="Button 1 Link" class="form-control" v-model="form.header.button1Link">
						</div>
					</div>
					<div class="row m-t-15">
						<div class="col-12">
							<label>Button 2 Link</label>
							<input name="header.button2Link" placeholder="Button 2 Link" class="form-control" v-model="form.header.button2Link">
						</div>
					</div>
					<div class="row m-t-15">
                        <div class="col-12">
                            <label class="m-b-0">
                                Background (recommended: 880px by 200px - will auto size to fit)
                            </label>
                            <div class="file-drag-drop w-100 m-t-10"
                                 v-bind:class="form.header.background || form.header.new_background ? 'background-file': ''"
                                 v-bind:style="{'background-image': 'url('+form.header.background+')'}"
                                 v-cloak>
                                <b-form-file class="upload-icon"
                                             @change="headerBackgroundChange"
                                             name="header.background"
															accept="image/*"
															ref="headerFileInput">
                                </b-form-file>

                                <div class="custom-file-overlay">
	                                <span class="img">
	                                    <i class="icon-image-upload" v-if="!form.header.background && !form.header.new_background"></i>
	                                </span>
                                    <h5 class="float f-s-17 bold">
                                    	<span class="text"
                                          v-if="form.header.background || form.header.new_background"
                                          v-text="form.header.background_name">
                                      	</span>
                                        <span v-else>Drag files to upload</span>
                                    </h5>
                                    <i v-if="form.header.background || form.header.new_background"
                                       @click="clearBackgroundImage('header')"
                                       class="fa fa-times color-light-grey pointer"></i>
                                </div>
                            </div>
                        </div>
					</div>
                    <div class="row m-t-15">
                        <div class="col-12">
                            <label class="m-b-5">Background Opacity</label>
                            <input type="text" class="form-control m-b-5" name="header.background_opacity" v-model="form.header.background_opacity" placeholder="Background Opacity" @blur="opacityFormat('header')">
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<div class="well m-t-20">
			<div :class="{ 'loading' : loading }" v-cloak>
				<div class="border-bottom p-b-10">
					<label class="bolder f-s-15 m-b-0">
						Header Design
					</label>
				</div>
				<div class="header-settings m-t-15">
					<div class="row">
						<div class="col-6">
							<label>Title Color</label>
							<colorpicker :color="form.header.design.titleColor" v-model="form.header.design.titleColor" name="header.design.titleColor"/>
						</div>
						<div class="col-6">
							<label>Subtitle Color</label>
							<colorpicker :color="form.header.design.subtitleColor" v-model="form.header.design.subtitleColor" name="header.design.subtitleColor"/>
						</div>
					</div>

					<div class="row m-t-15">
						<div class="col-6">
							<label>Title Font Size</label>
							<input-number max="50" min="10" v-model="form.header.design.titleFontSize"></input-number>
						</div>
						<div class="col-6">
							<label>Subtitle Font Size</label>
							<input-number max="35" min="10" v-model="form.header.design.subtitleFontSize"></input-number>
						</div>
					</div>

					<div class="row m-t-15">
						<div class="col-6">
							<label>Button Color</label>
							<colorpicker :color="form.header.design.buttonColor" v-model="form.header.design.buttonColor" name="header.design.buttonColor"/>
						</div>
						<div class="col-6">
							<label>Button Text Color</label>
							<colorpicker :color="form.header.design.buttonTextColor" v-model="form.header.design.buttonTextColor" name="header.design.buttonTextColor"/>
						</div>
					</div>

					<div class="row m-t-15">
						<div class="col-6">
							<label>Button Font Size</label>
							<input-number max="50" min="10" v-model="form.header.design.buttonFontSize"></input-number>
						</div>
						<div class="col-6">
							<label>Header Color</label>
							<colorpicker :color="form.header.design.color" v-model="form.header.design.color" name="header.design.color"/>
						</div>
					</div>
				
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6 col-12">
		<div class="sticky-top">
			<div class="rewards-page-component">
				<div ref="headerSection">
					<rewards-page-header :data="form.header"></rewards-page-header>
				</div>
			</div>
		</div>
	</div>
</div>
