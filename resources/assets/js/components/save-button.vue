<template>
	<div>
        <span style="vertical-align: middle;" v-if="saving" class="i-loading"></span>
        <button v-show="!saving" :type="type" @click.prevent="submit" class="btn btn-save" :disabled="saving">
            <span v-text="button"></span>
        </button>
	</div>
</template>

<script>
    export default {
        props: {
            saving: {
                default: false,
                type: [String, Boolean]
            },
            button: {
                default: 'Save',
                type: [String]
            },
            type: {
                default: 'button',
                type: [String]
            }
        },
		methods: {
		    submit() { 
		    	this.$emit('event')
			}
		}
    }
</script>