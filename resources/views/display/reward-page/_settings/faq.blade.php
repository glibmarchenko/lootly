<div class="row section-border-bottom p-t-25 p-b-25">
	<div class="col-md-6 col-12">
		<div class="well">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group m-b-0">
                        <label class="light-font m-b-0 m-t-5">
                            FAQ section is currently
                            <span class="bold" v-if="form.faq.status == 0">Disabled</span>
                            <span class="bold" v-if="form.faq.status == 1">Enabled</span>
                        </label>
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    <a @click="toogleFaqStatus">
						<span v-if="form.faq.status == 1">
							<span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
						</span>
						<span v-else>
							<span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
						</span>
                    </a>
                </div>
            </div>
		</div>

		<div class="well m-t-20" v-if="form.faq.status == 1">
			<div :class="{ 'loading' : loading }" v-cloak>
				<div class="border-bottom p-b-10">
					<label class="bolder f-s-15 m-b-0">
						FAQ Settings
					</label>
				</div>
				<div class="faq-settings m-t-15">
					<div class="row">
						<div class="col-12">
							<label>Title</label>
							<input name="faq.title" placeholder="FAQ title" class="form-control" v-model="form.faq.title">
						</div>
					</div>

					<div class="row m-t-15">
						<div class="col-md-12">
							<label class="m-b-5 pull-left">Questions</label> 
							<a class="color-blue bolder f-s-14 pull-right" @click="addQuestion">Add</a>
						</div>
					</div>

					<draggable :options="{handle:'.handle'}" class="faq-list" :list="form.faq.questions">
	                    <div class="row draggable-rewards-action faq-item"
	                         v-for="(question, index) in form.faq.questions">
	                        <div class="col-md-12 col-12">
	                        	<span class="handle"><i class="fa fa-bars" aria-hidden="true"></i></span>

	                            <input placeholder="Question title" v-model="question.question" class="form-control">
	                            <span class="btn btn-default" type="button"
	                                    @click="deleteQuestion(index)">
	                                <i class="fa fa-trash-o f-s-19"></i>
	                            </span>
	                            <span class="toogle-question" onclick="this.parentNode.classList.toggle('opened')">
	                            	<i class="toogle-arrow"></i>
	                            </span>
	                        	<textarea placeholder="Question Answer" class="form-control" v-model="question.answer"></textarea>
	                        </div>
	                    </div>
					</draggable>

				</div>
			</div>
		</div>
		<div class="well m-t-20" v-if="form.faq.status == 1">
			<div :class="{ 'loading' : loading }" v-cloak>
				<div class="border-bottom p-b-10">
					<label class="bolder f-s-15 m-b-0">
						FAQ Design
					</label>
				</div>
				<div class="faq-settings m-t-15">
					<div class="row">
						<div class="col-6">
							<label>Title Color</label>
							<colorpicker :color="form.faq.design.titleColor" v-model="form.faq.design.titleColor" name="faq.design.titleColor"/>
						</div>
						<div class="col-6">
							<label>Question Color</label>
							<colorpicker :color="form.faq.design.questionColor" v-model="form.faq.design.questionColor" name="faq.design.questionColor"/>
						</div>
					</div>

					<div class="row m-t-15">
						<div class="col-6">
							<label>Title Font Size</label>
							<input-number max="50" min="10" v-model="form.faq.design.titleFontSize"></input-number>
						</div>
						<div class="col-6">
							<label>Question Font Size</label>
							<input-number max="35" min="10" v-model="form.faq.design.questionFontSize"></input-number>
						</div>
					</div>

					<div class="row m-t-15">
						<div class="col-6">
							<label>Answer Color</label>
							<colorpicker :color="form.faq.design.answerColor" v-model="form.faq.design.answerColor" name="faq.design.answerColor"/>
						</div>
						<div class="col-6">
							<label>Answer Font Size</label>
							<input-number max="35" min="10" v-model="form.faq.design.answerFontSize"></input-number>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6 col-12">
		<div class="sticky-top" v-if="form.faq.status == 1">
			<div class="rewards-page-component">
				<div ref="faqSection">
					<rewards-page-faq :data="form.faq"></rewards-page-faq>
				</div>
			</div>
		</div>
	</div>
</div>
