<template>
    <div class="tab-rewards-reminders" v-if="data.show">
        <div class="dismiss-btn" v-if="data.spending.data.length > 0">
            <span @click="dismiss">Dismiss <i>&times;</i></span>
        </div>
        <div class="reminder-reward" :class="{'coupon-ready': data.coupon.show}" 
        	 v-for="(reward, index) in data.spending.data" 
        	 v-if="!reward.isLimitReached">
            <span class="reward-head" @click="redeemReward(reward)">
                <span class="reward-icon" 
                	  :class="{'loading-btn': data.coupon.processing}" 
                	  :style="{'border-color': $root.widgetSettings.tab.bg_color}">
                    <img v-if="reward.custom_icon" :src="reward.icon" style="max-width: 40px;" />
                    <i :class="reward.icon" :style="{ color: $root.widgetSettings.tab.bg_color }" v-else></i>
                </span>
                <div class="reward-content">
                    <p class="title">You have a reward available</p>
                    <p class="tagline">Add the reward to your cart</p>
                </div>
            </span>
            <span class="reward-body" v-if="data.coupon.show && data.coupon.code != ''">
                <p>Use this discount code on your next order for <span v-text="reward.displayType+'!'"></span></p>
                <input class="form-control" readonly="" v-model="data.coupon.code" id="coupon-code">
                <div class="text-center">
                    <button class="btn" 
                    		:style="{ background: $root.widgetSettings.tab.bg_color, color: $root.widgetSettings.tab.font_color }" 
                    		@click="copyClipboard">Copy Code</button>
                </div>
            </span>
        </div>
    </div>
</template>

<script>
	export default {
	    data: function() {
	        return {
	            data: {
	                show: true,
	                coupon: {
	                    show: false,
	                    code: '',
	                    processing: false
	                },
	                limit: 1,
	                customer: {},
	                points: {
	                    value: 0
	                },
	                spending: {
	                    data: []
	                }
	            }
	        }
	    },
	    created: function() {

	    	if(this.getCookie('customer-id') != this.$root.query.customer.id) this.deleteCookies()

	    	if(this.getCookie('dismissed') != 'true') {
	    		let cookieRedeemedReward = this.getCookie('redeemed-reward');
	    		let cookieCouponCode = this.getCookie('coupon-code');

		    	if( cookieRedeemedReward && cookieCouponCode && this.getCookie('customer-id') == this.$root.query.customer.id) {
		    		this.data.coupon.show = true;
		    		this.data.coupon.code = cookieCouponCode;
		    		this.data.spending.data = JSON.parse(cookieRedeemedReward);
                    this.$root.sendMessageFromWidget('toggle-rewards-tab')
                    this.$root.sendMessageFromWidget('reward-redeemed-tab')
		    	} else {
		    		this.getCustomer();
		    	}
	    	}

	    },
	    methods: {
	        dismiss: function() {
	        	this.setCookie('dismissed', true)
	            this.data.show = false;
	            this.$root.sendMessageFromWidget('toggle-rewards-tab')
	        },
	        getCustomer: function() {
	            let _this = this;
	            axios.post('/api/widget/customer', this.$root.query).then((result) => {
	                if (result.data && result.data.data) {
	                    let customer = result.data.data
	                    this.customer = customer
	                    if (customer.points) _this.data.points.value = parseInt(customer.points)
	                }
	            }).catch((error) => {
	                console.log(error)
	            }).then(() => {
	                _this.getRewards();
	            })
	        },
	        getRewards: function() {
	            const _this = this
	            axios.post('/api/widget/rewards', this.$root.query).then((result) => {
	                if (result.data && result.data.data) {
	                    let rewards = result.data.data
	                    if (rewards.length) {
	                        // Get Available Rewards
	                        let rewardsData = rewards.filter((item) => {
	                            return (parseInt(item.points_required) <= _this.data.points.value && item.type_id === 1 && item.reward_type != 'Variable amount')
	                        }).map((item, index) => {
	                            if (index < _this.data.limit) {
	                                return {
	                                    id: item.id,
	                                    title: item.reward_name,
	                                    icon: (item.reward_icon || (item.reward ? (item.reward.icon || '') : '')),
	                                    custom_icon: (item.reward_icon ? true : false),
	                                    displayType: item.reward.display_text,
										type: (item.reward ? item.reward.slug : item.reward_type),
	                                    isLimitReached: _this.customer.rewards_spending_limits.find(object => object.id === item.id).is_limit_reached,
	                                }
	                            }
	                        });
	                        _this.data.spending.data = rewardsData.slice(0, _this.data.limit)
	                        if(_this.data.spending.data.length > 0) _this.$root.sendMessageFromWidget('toggle-rewards-tab')
	                    }
	                }
	            }).catch((error) => {
	                console.log(error)
	            })
	        },
	        redeemReward: function(reward) {
	            let _this = this;
	            if (reward.type == 'variable-amount' || reward.type == "Variable amount") {
	                _this.$router.replace('/widget/variable-discount/' + reward.id)
	                return;
	            }
	            if (!_this.data.coupon.processing && _this.data.coupon.code == '') {
	                _this.data.coupon.processing = true
	                axios.post('/api/widget/rewards/' + reward.id + '/redeem', _this.$root.query).then((response) => {
	                    if (response.data && response.data.data) {
	                        let coupon = response.data.data;
	                        _this.data.coupon = {
	                        	show: true,
	                        	code: coupon.coupon_code,
	                        	processing: false
	                        }
	                        _this.setCookie('customer-id', _this.$root.query.customer.id);
	                        _this.setCookie('coupon-code', coupon.coupon_code);
	                        _this.setCookie('redeemed-reward', JSON.stringify(_this.data.spending.data));
	                        _this.$root.sendMessageFromWidget('reward-redeemed-tab')
	                        _this.$root.updateLoggedInWidget();
	                    }
	                }).catch((error) => {
	                    console.log(error)
	                    _this.dismiss();
	                })
	            } else {
	            	_this.copyClipboard()
	            }
	        },
			copyClipboard: function () {
				let couponField = document.querySelector('#coupon-code')
				couponField.select()
				document.execCommand('copy')
				window.getSelection().removeAllRanges()
			},
			setCookie: function(cname, cvalue, exdays = 1) {
				var d = new Date();
				d.setTime(d.getTime() + (exdays*24*60*60*1000));
				var expires = "expires="+ d.toUTCString();
				document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
			},
			getCookie: function(cname) {
				var name = cname + "=";
				var decodedCookie = document.cookie;
				var ca = decodedCookie.split(';');
				for(var i = 0; i <ca.length; i++) {
					var c = ca[i];
					while (c.charAt(0) == ' ') {
						c = c.substring(1);
					}
					if (c.indexOf(name) == 0) {
						return c.substring(name.length, c.length);
					}
				}
				return '';
			},
	        deleteCookies: function() {
	            document.cookie.split(";").forEach(function(c) { 
	                document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
	            });
	        },
	    }
	}
</script>