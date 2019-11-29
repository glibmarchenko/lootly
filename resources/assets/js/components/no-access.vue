<template>
    <div class="well permissions-box" :class="plan">
        <span :class="{'loading': loading}">
            <div class="border-bottom">
                <h5 class="bold f-s-18 m-b-15">
                	<span v-text="title"></span>
                </h5>
            </div>
            <div class="m-t-20 m-b-10 p-l-25 p-r-25">
                <img :src="'/images/permissions/'+icon" height="80">

                <p class="text-center f-s-15 m-t-20 m-b-20">
                	<span v-text="desc"></span>
                </p>

                <a class="btn upgrade-plan-btn" :href="url">Upgrade <span v-if="plan">to</span> <span v-text="plan"></span></a>
            </div>
        </span>
    </div>
</template>

<script>
    export default {
        props: {
            title: {
                default: 'No Access!',
                type: String
            },
            icon: {
                default: 'integrations.png',
                type: String
            },
            desc: {
                default: '',
                type: String
            },
            plan: {
                default: '',
                type: String
            },
            url: {
                default: '/account/upgrade',
                type: String
            },
            loading: {
                default: false,
                type: [Boolean]
            }
        }
	}
</script>

<style lang="scss">
.permissions-box {
	text-align: center;
	padding: 20px 0 !important;

	&.ultimate {
		/*border-top: 6px solid #4d5ba0;*/
	}
}
</style>