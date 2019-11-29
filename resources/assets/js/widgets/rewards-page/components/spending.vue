<template>
	<section class="spending">
		<h1 class="section-title" 
		    :style="{'color': data.design.titleColor, 'fontSize': data.design.titleFontSize+'px'}" v-text="title"></h1>

		<div class="spending-actions" v-if="data.actions.length > 0">
			<div class="spending-action" 
				 :style="{'background': data.design.boxColor, 'color': data.design.boxTextColor}" 
				 v-if="action != null"
				 v-for="action in data.actions">
				<h5 :style="{'fontSize': data.design.boxFontSize+'px'}">{{action.points}}</h5>
				<p :style="{'fontSize': data.design.boxFontSize-3+'px'}">{{action.title}}</p>
				
			</div>
		</div>
	</section>
</template>

<script>
	export default {
		props: ['data', 'title']
	}
</script>
