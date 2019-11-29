<template>
	<section class="vip" v-if="data.status != 'Disabled' && data.status != 0">
		<h1 class="section-title" 
		    :style="{'color': data.design.titleColor, 'fontSize': data.design.titleFontSize+'px'}" v-text="title"></h1>

		<div class="vip-tiers">
			<span v-if="data.tiers.length > 0">
				<div v-for="tier in data.tiers">
					<div class="vip-tier">
						<img v-if="tier.icon_url" :src="tier.icon_url" style="max-width: 45px;">
						<i v-else class="icon-vip" :style="`color: ${tier.icon_color}; font-size: 33px;`"></i>

						<div class="vip-main">

							<p class="vip-title" v-html="tier.name"
								:style="{'color': data.design.tierNameColor, 'fontSize': data.design.tierNameFontSize+'px'}"></p>

							<p class="vip-desc" v-html="tier.multiplier +'x Point Multiplier'"
								:style="{'color': data.design.multiplierColor, 'fontSize': data.design.multiplierFontSize+'px'}"></p>

						</div>
						<div class="vip-info">
							<p v-html="`Earn ${tier.name} status after <br> ${tier.text}`"
								:style="{'color': data.design.requirementsColor, 'fontSize': data.design.requirementsFontSize+'px'}"></p>
						</div>
					</div>
				</div>
			</span>
			<span v-else>
				<p>There are no VIP tiers.</p>
			</span>
		</div>
	</section>
</template>

<script>
	export default {
		props: ['data', 'title']
	}
</script>
