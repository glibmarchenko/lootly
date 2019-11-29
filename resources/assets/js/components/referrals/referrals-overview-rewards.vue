<template>
    <div class="row m-t-20">
        <div class="col-md-6 m-t-5">
            <div class="well well-table">
                <table class="table">
                    <thead>
                    <tr>
                        <th class="color-dark-grey f-s-17">Reward Given To Sender</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="senderReward">
                        <td><img v-if="senderReward.reward_icon" :src="senderReward.reward_icon" style="max-width: 30px;">
                            <span v-else :class="senderReward.reward.icon" class="m-r-10"></span>
                            <label class="m-b-0 m-t-0" v-text="senderReward.reward_text"></label></td>
                        <td class="text-right">
                            <a class="bold f-s-14" :href="senderRewardUrl">Edit Reward</a>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6 m-t-5">
            <div class="well well-table">
                <table class="table">
                    <thead>
                    <tr>
                        <th class="color-dark-grey f-s-17">Reward Given to Receiver</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="receiverReward">
                        <td><img v-if="receiverReward.reward_icon" :src="receiverReward.reward_icon" style="max-width: 30px;">
                            <span v-else :class="receiverReward.reward.icon" class="m-r-10"></span>
                            <label class="m-b-0 m-t-0" v-text="receiverReward.reward_text"></label></td>
                        <td class="text-right">
                            <a class="bold f-s-14" :href="receiverRewardUrl">Edit Reward</a>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>


<script type="text/javascript">
    export default {
        data: function () {
            return {
                senderReward: null,
                receiverReward: null,
                receiverRewardUrl:null,
                senderRewardUrl:null,
            }
        },
        created: function () {
            this.getData();
        },
        methods: {
            getData: function(){
                axios.get('/referrals/rewards/get').then((response) => {
                        console.log('response: ', response);
                        this.senderReward=response.data.senderReward;
                        this.receiverReward=response.data.receiverReward;
                        this.senderRewardUrl=response.data.senderRewardUrl;
                        this.receiverRewardUrl=response.data.receiverRewardUrl;
                        if (!this.form.iconPreview) {
                            showPreviewIcon(this.form.program.reward_icon, this.icon_default_class, this.icon_parent_el);
                        } else {
                            clearPreviewIcon(this.icon_default_class, this.icon_parent_el);
                        }
                    }
                ).catch((error) => {

                    this.errors = error
                });
            },

        }
    }
</script>

