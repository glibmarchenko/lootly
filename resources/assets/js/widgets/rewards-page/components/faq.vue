<template>
	<section class="faq" v-if="data.status">
		<h1 class="section-title" 
		    :style="{'color': data.design.titleColor, 'fontSize': data.design.titleFontSize+'px'}">{{data.title}}</h1>

		<div class="questions-items" v-if="data.questions.length > 0">
			<div class="question-item" 
				 v-for="question in data.questions">
				<h5 :style="{'color': data.design.questionColor, 'fontSize': data.design.questionFontSize+'px'}">{{question.question}}</h5>
				<p :style="{'color': data.design.answerColor, 'fontSize': data.design.answerFontSize+'px'}">{{question.answer}}</p>
			</div>
		</div>
	</section>
</template>

<script>
	export default {
		props: ['data']
	}
</script>
