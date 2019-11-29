<template>
	<section class="how-it-works">
		<h3 class="section-title" :style="{'color': data.design.titleColor, 'font-size': data.design.titleFontSize + 'px'}">{{data.title}}</h3>
		<div class="how-it-works-steps">
			<div class="canvas-steps">

				<div class="canvas-step step-1" :style="{'border-color': data.design.circleEmptyColor}"> 
					<span class="step-index">1</span>
					<span class="arc-start" :style="{'border-top-color': data.design.circleFullColor}"></span>
					<span class="arc-end" :style="{'border-top-color': data.design.circleFullColor}"></span>
				</div>
				
				<div class="step-arrow">
					<span class="icon-long-arrow" :style="{'color': data.design.arrowsColor}"></span>
				</div>

				<div class="canvas-step step-2" :style="{'border-color': data.design.circleEmptyColor}">
					<span class="step-index">2</span>
					<span class="arc-start" :style="{'border-top-color': data.design.circleFullColor}"></span>
					<span class="arc-end" :style="{'border-top-color': data.design.circleFullColor, 'border-right-color': data.design.circleFullColor}"></span>
				</div>

				<div class="step-arrow second-arrow">
					<span class="icon-long-arrow" :style="{'color': data.design.arrowsColor}"></span>
				
				</div>
				<div class="canvas-step step-3" :style="{'border-color': data.design.circleFullColor}">
					<span class="step-index">3</span>
				</div>
				
			</div>
			<div>
				<p class="step-1-subtitle" :style="{'color': data.design.stepsColor, 'fontSize': data.design.stepsFontSize+'px'}">
					<span v-if="data.step1">{{data.step1}}</span>
					<span v-else>-</span>
				</p>
				<p class="step-2-subtitle" :style="{'color': data.design.stepsColor, 'fontSize': data.design.stepsFontSize+'px'}">
					<span v-if="data.step2">{{data.step2}}</span>
					<span v-else>-</span>
				</p>
				<p class="step-3-subtitle" :style="{'color': data.design.stepsColor, 'fontSize': data.design.stepsFontSize+'px'}">
					<span v-if="data.step3">{{data.step3}}</span>
					<span v-else>-</span>
				</p>
			</div>
		</div>
	</section>
</template>

<script>
	export default {
		props: ['data']
	}
</script>
