<template>
	<div class="form-input-toggle">
		<a class="input-toggle minus-toggle" @click="minusToggle">
			<i class="fa fa-minus" aria-hidden="true"></i>
		</a>
		<input type="text" class="form-control" v-model="content" @blur="checkValue">
		<a class="input-toggle plus-toggle" @click="plusToggle">
			<i class="fa fa-plus" aria-hidden="true"></i>
		</a>
	</div>
</template>

<script>
    export default {
        props: ['value', 'max', 'min'],
        data () {
		    return {
		    	content: this.value,
		    	old: this.value
		  	}
		},
        methods: {
        	plusToggle: function () {
        		if(parseInt(this.content)+1 > this.max)
        			return false;

        		this.content = parseInt(this.content)+1;
				this.$emit('input', this.content)
        	},
        	minusToggle: function () {
        		if(this.content+1 < this.min)
        			return false;

        		this.content--;
				this.$emit('input', this.content)
        	},
        	checkValue: function () {
        		if(this.max && parseInt(this.content) > this.max) {
        			this.content = this.max;
        		}
        		if (this.min && parseInt(this.content) < this.min) {
        			this.content = this.min;
        		}
				this.$emit('input', this.content)
        	}
        }
    }
</script>

<style scoped>
	.input-toggle {
		display: inline-block;
		background: #0279b7;
		color: #fff !important;
		font-size: 10px;
		border-radius: 3px;
		cursor: pointer;
		width: 15px;
		height: 15px;
		text-align: center;
		padding-left: 1px;
	}
	.form-input-toggle input {
		width: 55px;
		display: inline-block;
		margin: 0 5px;
		text-align: center;
	}	
</style>