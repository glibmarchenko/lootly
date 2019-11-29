<template>
	<section class="earning">
		<h3 class="section-title" 
			:style="{'color': data.design.titleColor, 'fontSize': data.design.titleFontSize+'px'}" v-text="title"></h3>

		<div class="earning-actions">
			<div class="" 
				 v-if="action != null"
				 v-for="action in data.actions">
				<div class="earning-action" :style="{'background': data.design.boxColor}">

					<i v-if="(action.icon.indexOf('http') == -1)" class="icon" :class="action.icon"></i>
					<img v-else class="icon" :src="action.icon" style="max-height: 47px; margin-left: auto; margin-right: auto;" />
					<div class="card-title" :style="{'background': data.design.ribbonColor}">

						<span class="card-title-before" :style="{'border-left-color': data.design.boxColor}"></span>

						<p class="points-widget-title" v-html="action.points" :style="{'color': data.design.pointColor, 'fontSize': data.design.pointFontSize+'px'}"></p>

						<span class="card-title-after" :style="{'border-right-color': data.design.boxColor}"></span>

					</div>
					<p class="card-desc" :style="{'color': data.design.actionTextColor, 'fontSize': data.design.actionFontSize+'px'}">{{action.title}}</p>
				</div>
			</div>
		</div>
	</section>
</template>

<script>
	export default {
		props: ['data', 'title']
	}
</script>
