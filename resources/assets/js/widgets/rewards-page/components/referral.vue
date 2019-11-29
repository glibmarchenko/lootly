<template>
	<section class="referrals" v-if="data.status != 0">
		<h1 class="section-title" 
		    :style="{'color': data.design.titleColor, 'fontSize': data.design.titleFontSize+'px'}">{{data.title}}</h1>
		<p class="section-desc" v-if="data.subtitle" 
		   :style="{'color': data.design.subtitleColor, 'fontSize': data.design.subtitleFontSize+'px'}">{{data.subtitle}}</p>

		<div class="referrals-actions">
			<div v-if="data.receiver.exists == '1' || data.receiver.exists" class="action-item">
				<span class="pull-left">
					<img v-if="data.receiver.reward_icon" :src="data.receiver.reward_icon">
					<i v-else class="pull-left" :class="data.receiver.reward_icon_name"></i>
				</span>
				<div class="action-item-content">
					<p class="title" v-html="data.receiver.name"></p>
					<p v-html="data.receiver.text"></p>
				</div>
			</div>
			<div v-if="data.sender.exists == '1' || data.sender.exists" class="action-item">
				<span class="pull-left">
					<img v-if="data.sender.reward_icon" :src="data.sender.reward_icon">
					<i v-else class="pull-left" :class="data.sender.reward_icon_name"></i>
				</span>
				<div class="action-item-content">
					<p class="title" v-html="data.sender.name"></p>
					<p v-html="data.sender.text"></p>
				</div>
			</div>
		</div>
	</section>
</template>

<script>
	export default {
		props: ['data']
	}
</script>
