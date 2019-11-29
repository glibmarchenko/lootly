import { Chrome } from 'vue-color';

Vue.component('select-color-picker', {
	components: {
		'chrome-picker': Chrome
	},
	template: `
		<div class="input-group color-picker" ref="colorpicker">
			<span class="input-group-addon color-picker-container">
				<span class="current-color" :style="'background-color: ' + colorValue" @click="togglePicker()"></span>
				<chrome-picker :value="colors" @input="updateFromPicker" v-if="displayPicker" />
			</span>
			<div class="dropdown select-color-picker">
			  <a class="btn btn-default dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    <span v-if="colorType == 'Custom Color'">Custom Color ({{ colorValue }})</span>
				    <span v-else>{{colorLabel}}</span>
			  </a>
			  <div class="dropdown-menu">
			    <a v-for="option in options" @click="changeType(option.value)" class="dropdown-item">{{option.label}}</a>
				<a @click="customColor" class="dropdown-item">Custom Color <span v-if="colorType == 'Custom Color'">({{ colorValue }})</span></a>
			  </div>
			</div>
		</div>`,
	props: {
        color: {
            default: '#153479',
            type: [String]
        },
        options: {
    	    default: function () { 
    	    	return [
	                {label: 'Primary Color', value: 'primary-color'},
	                {label: 'Secondary Color', value: 'secondary-color'},
    	    	];
		    },
            type: [Array, Object]
        }
	},
	data() {
		return {
			colorType: '',
			colors: {
				hex: '#000000',
			},
			colorValue: '',
			displayPicker: false,
		}
	},
	mounted() {
		var vm = this;
		this.setColor(this.color || '#000000');		
	},
	methods: {
		changeType(val) {
			this.colorType = val;
			this.optionSelect()
		},
		customColor() {
			this.colorType = 'Custom Color';
			this.showPicker()
		},
		checkOption() {
			if(this.color == this.$parent.form.primaryColor || this.color == this.options[0].value) {
				this.colorType = this.options[0].value;
			} else if(this.color == this.$parent.form.secondaryColor || this.color == this.options[1].value) {
				this.colorType = this.options[1].value;
			} else {
				this.colorType = 'Custom Color';
			}
		},
		optionSelect(val) {
			this.displayPicker = false;
			if (this.colorType == 'primary-color' ) {
				return this.colorValue = this.$parent.form.primaryColor;
			} else if (this.colorType == 'secondary-color') {
				return this.colorValue = this.$parent.form.secondaryColor;
			} else if(this.colorType == this.options[0].value) {
				return this.colorValue = this.options[0].value;
			} else if(this.colorType == this.options[1].value) {
				return this.colorValue = this.options[1].value;
			} else {
				this.showPicker();
			}
		},
		setColor(color) {
			this.updateColors(color);
			this.colorValue = color;
		},
		updateColors(color) {
			if(color.slice(0, 1) == '#') {
				this.colors = {
					hex: color
				};
			} else if(color.slice(0, 4) == 'rgba') {
				var rgba = color.replace(/^rgba?\(|\s+|\)$/g,'').split(','),
					hex = '#' + ((1 << 24) + (parseInt(rgba[0]) << 16) + (parseInt(rgba[1]) << 8) + parseInt(rgba[2])).toString(16).slice(1);
				this.colors = {
					hex: hex,
					a: rgba[3],
				}
			}
		},
		showPicker() {
			document.addEventListener('click', this.documentClick);
			this.displayPicker = true;
		},
		hidePicker() {
			document.removeEventListener('click', this.documentClick);
			this.displayPicker = false;
		},
		togglePicker() {
			this.displayPicker ? this.hidePicker() : this.showPicker();
		},
		updateFromInput() {
			this.updateColors(this.colorValue);
		},
		updateFromPicker(color) {
			this.colors = color;
			if(color.rgba.a == 1) {
				this.colorValue = color.hex;
			}
			else {
				this.colorValue = 'rgba(' + color.rgba.r + ', ' + color.rgba.g + ', ' + color.rgba.b + ', ' + color.rgba.a + ')';
			}
			this.checkOption()
		},
		documentClick(e) {
			var el = this.$refs.colorpicker,
				target = e.target;
			if(el !== target && !el.contains(target)) {
				this.hidePicker()
			}
		}
	},
	watch: {
		color: function(newColor, oldColor){
	      this.setColor(newColor || '#000000');
		},
		colorValue(val) {
			if(val) {
				this.updateColors(val);
				this.$emit('input', val);
			}
		},
		'$parent.form.primaryColor': function(val) {
			if(this.colorType == 'primary-color') {
				this.setColor(val)
			}
		},
		'$parent.form.secondaryColor': function(val) {
			if(this.colorType == 'secondary-color') {
				this.setColor(val)
			}
		},
		'$parent.loading': function(val) {
			if(!val) {
				this.checkOption()
			}
		}
	},
	computed: {
		'colorLabel': function() {
			if(this.colorType !== 'Custom Color') {
				var option = this.options.getObjectByKey('value', this.colorType);
				return option? option.label : '';
			}
		}
	}
});
