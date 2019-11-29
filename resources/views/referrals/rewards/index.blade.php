@extends('layouts.app')

@section('title', 'Referrals Rewards')

@section('content')
    <div id="rewards-page" class="m-t-20 m-b-10">
    <span id="pageLoading" class="loading">
        <div class="row m-t-20 p-b-10 section-border-bottom">
            <div class="col-md-12 col-12">
                <h3 class="page-title pull-left m-t-0 color-dark">Referrals Rewards</h3>
            </div>
        </div>

        <div class="row p-t-25 p-b-25 section-border-bottom">
            <div class="col-md-5 col-12">
                <h5 class="bolder m-b-15">Sender Reward</h5>
                <p class="m-b-10">This is the reward that is given to the sender once their referral makes a purchase.</p>
                <p v-if="senderReward">
                    <a class="bold color-blue" @click="removeReward('sender')">Remove this reward</a> to create a new one
                </p>
            </div>
            <div class="col-md-7 col-12">
                <div class="well">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group m-b-0">
                                <span v-if="senderReward">
                                    <img v-if="senderReward.reward_icon" :src="senderReward.reward_icon"
                                         style="max-width: 30px;">
                                    <span v-else :class="senderReward.reward.icon" class="m-r-10"></span>
                                    {{--<i :class="senderRewardIcon" class="f-s-30 m-r-10"></i>--}}
                                    <label class="m-b-0 m-t-0" v-text="senderReward.reward_text"></label>
                                    <a :href="senderRewardUrl"
                                       class="bolder f-s-14 color-blue pull-right">Edit Reward</a>
                                </span>
                                <span v-else>
                                    <i class="icon-gift f-s-30 m-r-10"></i>
                                    <label class="m-b-0 m-t-0">
                                        Reward your referral senders with points and discounts.
                                    </label>
                                    <a href="{{ route('referrals.rewards.sender') }}"
                                       class="bolder f-s-14 color-blue pull-right">Add Reward</a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row p-t-25 p-b-25">
            <div class="col-md-5 col-12">
                <h5 class="bolder m-b-15">Receiver Reward</h5>
                <p class="m-b-10">This is the reward that the referred person will receive to incentivize them to make a purchase.</p>
                <p v-if="receiverReward">
                    <a class="bold color-blue" @click="removeReward('receiver')">Remove this reward</a> to create a new one
                </p>
            </div>
            <div class="col-md-7 col-12">
                <div class="well">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group m-b-0">
                                <span v-if="receiverReward">
                                    <img v-if="receiverReward.reward_icon" :src="receiverReward.reward_icon"
                                         style="max-width: 30px;">
                                    <span v-else :class="receiverReward.reward.icon" class="m-r-10"></span>
                                    <label class="m-b-0 m-t-0" v-text="receiverReward.reward_text"></label>
                                    <a :href="receiverRewardUrl"
                                       class="bolder f-s-14 color-blue pull-right">Edit Reward</a>
                                </span>
                                <span v-else>
                                    <i class="icon-gift f-s-30 m-r-10"></i>
                                    <label class="m-b-0 m-t-0">
                                        Reward your referral receivers with discounts.
                                    </label>
                                    <a href="{{ route('referrals.rewards.receiver') }}"
                                       class="bolder f-s-14 color-blue pull-right">Add Reward</a>
                                </span>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</div>
@endsection

@section('scripts')
    <script type="text/javascript">
      var rewards = new Vue({
        el: '#rewards-page',
        data: {
          senderReward: null,
          receiverReward: null,
          deleteType: ''
        },
        created: function () {
          this.getData()
        },
        methods: {
          getData: function () {
            axios.get('/referrals/rewards/get').then((response) => {
              this.senderReward = response.data.senderReward
              this.receiverReward = response.data.receiverReward
              this.senderRewardUrl = response.data.senderRewardUrl
              this.receiverRewardUrl = response.data.receiverRewardUrl
              // if (!this.form.iconPreview) {
              //     showPreviewIcon(this.form.program.reward_icon, this.icon_default_class, this.icon_parent_el);
              // } else {
              //     clearPreviewIcon(this.icon_default_class, this.icon_parent_el);
              // }
              this.stopLoadingAnimation()
            }).catch((error) => {
              this.stopLoadingAnimation()
              this.errors = error.response
            })
          },
          removeReward: function ($reward_type) {
            this.deleteType = $reward_type;
            swal({
              className: "warning-swal",
              icon: "/images/icons/fa-warning.png",
              title: "Remove Reward Confirmation",
              text: "Are you sure that you would like to delete this reward?",
              dangerMode: true,
              buttons: true,
            }).then((response) => {
                if (response) {
                    this.removeRewardConfirm();
                }
            });
          },
          removeRewardConfirm: function () {
            let rewardId
            if (this.deleteType == 'sender') {
              rewardId = this.senderReward.id
              this.senderReward = null

            } else if (this.deleteType == 'receiver') {
              rewardId = this.receiverReward.id
              this.receiverReward = null

            }

            axios.delete('/api/merchants/' + Spark.state.currentTeam.id + '/rewards/' + rewardId).then((response) => {
              //
            }).catch((error) => {
              //
            })

            /*axios.delete('/referrals/rewards/' + this.deleteType + '/delete/' + rewardId).then((response) => {
              // this.form.program.icon = null;
              // this.form.program.reward_icon = null;
              // this.form.iconPreview = '';
              // showPreviewIcon(this.form.program.reward_icon, this.icon_default_class, this.icon_parent_el);
            }).catch((error) => {

            })*/
            this.$root.$emit('bv::hide::modal', 'delete-reward')
          },
          stopLoadingAnimation: function () {
            $('#pageLoading').removeClass('loading')
          },
        }
      })
    </script>
@endsection