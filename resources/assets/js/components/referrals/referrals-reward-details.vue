<template>

    <div class="well bg-white">
        <div class="row section-border-bottom p-b-10 m-b-15">
            <div class="col-md-8">
                <div class="form-group m-b-0">
                    <label class="bolder f-s-15 m-b-0 m-t-5">
                        Reward Details
                    </label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group m-b-0">
                    <label class="light-font m-b-10">
                        <span>Reward value ($)</span>
                    </label>
                    <input class="form-control"  v-model="form.reward.values" name='reward.values' placeholder="e.g. 5">
                </div>
            </div>
        </div>
        <div class="row m-t-10">
            <div class="col-md-12">
                <div class="form-group m-b-0">
                    <label class="light-font m-b-10">
                        <span>Coupon prefix</span>
                    </label>
                    <input class="form-control"  v-model="form.coupon.prefix" placeholder="e.g. $5off-">
                </div>
            </div>
        </div>
        <div class="row m-t-10">
            <div class="col-md-12">
                <div class="form-group m-b-0">
                    <label class="light-font m-b-10">
                        <span>Minimum order amount</span>
                    </label>
                    <input class="form-control"  v-model="form.reward.minOrder" placeholder="e.g. 10">
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
            }
        } ,

    }
</script>