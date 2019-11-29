<template>
	<section class="header">

		<div class="overlay-background" v-if="data.background" :style="{'background-image': 'url('+data.background+')', 'opacity': parseInt(data.background_opacity)/100}"></div>
		<div class="overlay-background" v-else :style="{'background-color': data.design.color, 'opacity': parseInt(data.background_opacity)/100}"></div>

		<h1 class="section-title" 
		    :style="{'color': data.design.titleColor, 'fontSize': data.design.titleFontSize+'px'}">{{data.title}}</h1>

		<p class="subtitle" v-if="data.subtitle" 
		   :style="{'color': data.design.subtitleColor, 'fontSize': data.design.subtitleFontSize+'px'}">{{data.subtitle}}</p>

		<div class="row">
			<div class="col-12">
				<a target="_block" :href="data.button1Link" class="btn btn-light" 
				   :style="{'color': data.design.buttonTextColor, 'background': data.design.buttonColor, 'fontSize': data.design.buttonFontSize+'px'}">{{data.button1}}</a>

				<a target="_block" :href="data.button2Link" class="btn btn-light" 
				   :style="{'color': data.design.buttonTextColor, 'background': data.design.buttonColor, 'fontSize': data.design.buttonFontSize+'px'}">{{data.button2}}</a>
			</div>
		</div>
		
	</section>
</template>

<script>
	export default {
		props: ['data']
	}
</script>
