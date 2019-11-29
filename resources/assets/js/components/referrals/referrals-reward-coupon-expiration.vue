<template>
    <div class="well bg-white m-t-20">
        <div :class="{ 'loading' : loading }" v-cloak>
            <div class="row section-border-bottom p-b-10 m-b-15">
                <div class="col-md-8">
                    <div class="form-group m-b-0">
                        <p class="bolder f-s-15 m-b-5">Coupon Expiration</p>
                        <label class="light-font m-b-0">
                            <span v-text="form.defaultRewardName"></span> is
                            <span class="bold" v-text="form.coupon.status == 0 ? 'Disabled' : 'Enabled'"></span>
                        </label>
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    <a @click="toogleCouponLimit" v-cloak>
                                <span v-if="form.coupon.status == 0">
                                <span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
                                </span>
                        <span v-else>
                                <span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
                                </span>
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 col-8 p-r-0">
                    <input class="form-control" v-model="form.coupon.limit.value" placeholder="e.g. 30">
                </div>
                <div class="col-md-4 col-4">
                    <select class="mb-3 form-control custom-select" v-model="form.coupon.limit.duration">
                        <option value="days">Days</option>
                        <option value="weeks">Weeks</option>
                        <option value="months">Months</option>
                        <option value="years">Years</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

</template>


<script type="text/javascript">
    export default {
        props:{
            form:{
                required: true
            },
            loading: {
                required: true
            }
        } ,


        methods:{

            toogleCouponLimit: function () {
                if (this.form.coupon.status == 0) {
                    this.form.coupon.status = 1;
                } else {
                    this.form.coupon.status = 0;
                }
            },
        }

    }
</script>