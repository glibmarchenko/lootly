<template>
	<canvas id="canvas" ref="canvas"></canvas>
</template>

<script>
	export default {
	    props: {
	        labels: {
	            type: [Array, Object]
	        },
	        data: {
	            type: [Array, Object]
			},
			tooltip: {
	            type: [Array, Object]
			},
	        background: {
	            default: '#ffffff',
	            type: [String]
	        },
	        color: {
	            default: '#000',
	            type: [String]
	        },
	        symbol:{
	            default: '',
	            type: [String]
	        },
	        ymax: {
	            default: 0,
	            type: [Number]
	        },
	        maxTicksLimit: {
	            default: 6,
	            type: [Number]
			  },
			  'skip-labels': {
					default: true,
					type: [Boolean]
			  },
	    },
	    mounted () {
			var ctx = this.$refs.canvas.getContext('2d');

			var gradient = ctx.createLinearGradient(0, 0, 0, 700);
			gradient.addColorStop(0, hexToRgb(this.background.toString().replace('#', ''), "1"));   
			gradient.addColorStop(0.5, hexToRgb("ffffff", "1"));
			gradient.addColorStop(1, hexToRgb("ffffff", "1"));

			initChart(ctx, this.labels, this.data, this.tooltip, gradient, this.color, this.symbol, this.ymax, this.maxTicksLimit, this.skipLabels);
	    },
	    methods: {
	    },
	    computed: {
	    }
	}

	function hexToRgb(hex, op) {			
		var bigint = parseInt(hex, 16);
		var r = (bigint >> 16) & 255;
		var g = (bigint >> 8) & 255;
		var b = bigint & 255;
		return "rgba(" + r + "," + g + "," + b + "," + op + ")";
	}


	function initChart(ctx, labels, data, tooltip, gradient, color, symbol, yMax, maxTicksLimit, skipLabels){

		var options = {
			type: 'line',
			data: {
				labels: labels,
				datasets: [{
					data: data,
					lineTension: 0,
					fill: true,
					backgroundColor: gradient,
					borderColor: color,
					borderWidth: 3,
					pointBorderColor: color,
					pointBackgroundColor: color,
					pointRadius: 0,
					pointHoverRadius: 10,
					pointHitRadius: 10,
					pointHoverBackgroundColor: color,
					pointHoverBorderColor: "#fff",
					pointHoverBorderWidth: 3,
				}]
			},
			options: {
				layout: {
				  padding: {
				     top: 50
				  }
				},
				maintainAspectRatio: false,
				responsive: true,
				tooltips: {
					enabled: false,
					custom : getTooltip,
					yAlign: 'bottom',
					backgroundColor: "#fff",
					titleFontColor: "#000",
					titleFontSize: 16,
					yPadding: 16,
					xPadding: 20,
					borderWidth: 1,
					borderColor: "#ddd",
					titleFontFamily: "sans-serif",
					callbacks: {
						label: function(tooltipItem, data) {
							return symbol + tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
						},
						title: function(tooltipItem, data) {
							if (Array.isArray(tooltip))
								return tooltip[tooltipItem[0].index];
							return '';
						},
						labelTextColor:function(tooltipItem, chart){
							return '#000';
						}
					}
				},
				scales: {
					yAxes: [{
						stacked: false,
						ticks: {
							beginAtZero:true,
							suggestedMax: yMax,
							maxTicksLimit: maxTicksLimit,
							fontStyle: 700,
							padding: 20,
				            callback: function(value, index, values) {
				                return symbol + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
							},
							autoSkip: skipLabels,
						},
						gridLines: {
							drawBorder: false,
							zeroLineColor: "#eaeaea",
							color: "#eaeaea",
							drawTicks:false
						},
					}],
					xAxes: [{
						gridLines: {
							drawBorder: false,
							display: false
						},
						ticks: {
							maxRotation: 0,
							minRotation: 0,
							fontStyle: 600,
							autoSkip: skipLabels,
						}
					}],
				},
				legend: {
					display: false
				}
			}
		}
		
		var chart = new Chart(ctx, options);

		function getTooltip(tooltipModel) {
			// Tooltip Element
			var tooltipEl = document.getElementById('chartjs-tooltip');

			// Create element on first render
			if (!tooltipEl) {
				tooltipEl = document.createElement('div');
				tooltipEl.id = 'chartjs-tooltip';
				tooltipEl.innerHTML = "<table></table>";
				document.body.appendChild(tooltipEl);
			}

			// Hide if no tooltip
			if (tooltipModel.opacity === 0) {
				tooltipEl.style.opacity = 0;
				return;
			}

			// Set caret Position
			tooltipEl.classList.remove('above', 'below', 'no-transform');
			if (tooltipModel.yAlign) {
				tooltipEl.classList.add(tooltipModel.yAlign);
			} else {
				tooltipEl.classList.add('no-transform');
			}

			function getBody(bodyItem) {
				return bodyItem.lines;
			}

			// Set Text
			if (tooltipModel.body) {
				var titleLines = tooltipModel.title || [];
				var bodyLines = tooltipModel.body.map(getBody);

				var innerHtml = '<thead>';

				titleLines.forEach(function(title) {
					innerHtml += '<tr><th>' + title + '</th></tr>';
				});
				innerHtml += '</thead><tbody>';

				bodyLines.forEach(function(body, i) {
					var colors = tooltipModel.labelColors[i];
					var style = 'background:' + colors.backgroundColor;
					style += '; border-color:' + colors.borderColor;
					style += '; border-width: 2px';
					var span = '<span style="' + style + '"></span>';
					innerHtml += '<tr><td>' + span + body + '</td></tr>';
				});
				innerHtml += '</tbody>';

				var tableRoot = tooltipEl.querySelector('table');
				tableRoot.innerHTML = innerHtml;
			}
			// `this` will be the overall tooltip
			var position = this._chart.canvas.getBoundingClientRect();
			// Display, position, and set styles for font
			tooltipEl.style.opacity = 1;
			tooltipEl.style.position = 'absolute';
			tooltipEl.style.left = position.left + window.pageXOffset + tooltipModel.caretX + 'px';
			tooltipEl.style.top = position.top + window.pageYOffset + tooltipModel.caretY + 'px';
			tooltipEl.style.fontFamily = tooltipModel._bodyFontFamily;
			tooltipEl.style.fontSize = tooltipModel.bodyFontSize + 'px';
			tooltipEl.style.fontStyle = tooltipModel._bodyFontStyle;
			tooltipEl.style.padding = tooltipModel.yPadding + 'px ' + tooltipModel.xPadding + 'px';
			tooltipEl.style.pointerEvents = 'none';
			tooltipEl.style.backgroundColor = '#fff';
			tooltipEl.style.border = 'solid 1px #eee';
			tooltipEl.style.borderRadius = '11px';
			tooltipEl.style.left = position.left + window.pageXOffset + tooltipModel.caretX - tooltipEl.offsetWidth / 2 + 'px';
			tooltipEl.style.top = position.top + window.pageYOffset + tooltipModel.caretY - tooltipEl.offsetHeight - 5 + 'px';
		}
	}
</script>
