@extends('layouts.app')

@section('title', 'Customer Profile')

@section('content')
    <div id="customer-profile" class="loader p-b-40 m-t-20 m-b-10" v-cloak>

        <div v-if="customerExists">
            <b-alert v-cloak
                     :show="alert.dismissCountDown"
                     dismissible
                     :variant="alert.type"
                     @dismissed="alert.dismissCountdown=0"
                     @dismiss-count-down="countDownChanged"
                    {{--:show="alertText ? true : false" dismissible--}}>
                @{{alert.text}}
            </b-alert>

            <div class="row m-t-15 p-b-10 section-border-bottom">
                <div class="col-md-3 col-12 m-t-5">
                    <h3 class="page-title m-t-0 color-dark">
                        <span v-html="overview.name"></span>
                    </h3>
                </div>
                <div class="col-md-9 col-12 m-t-5 text-right customer-options">
                    <a v-show="{{ !$woocommerce }}" :href="shopifyCustomerID" target="_blank" class="bold color-blue f-s-15 m-r-20">
                        <i class="icon-cart icon-20 m-r-5"></i> View in Shopify
                    </a>
                    <a href="/customers/widget/{{ $id }}" class="bold color-blue f-s-15 m-r-20">
                        <i class="icon-gear icon-20 m-r-5"></i> Widget Preview
                    </a>
                    <a class="bold color-blue f-s-15 m-r-20" v-b-modal.give-reward>
                        <i class="icon-gift f-s-19 m-r-5"></i> Give Reward
                    </a>
                    <a class="bold color-blue f-s-15 m-r-20" v-b-modal.adjust-points>
                        <i class="icon-points f-s-19 m-r-5"></i> Adjust Points
                    </a>
                    <a class="bold color-blue f-s-15" @click="showAdjustVipStatusModal">
                        <i class="icon-vip f-s-15 m-r-5"></i> Adjust VIP Status
                    </a>
                </div>
            </div>
            <div class="row m-t-20">
                <div class="col-md-7">
                    <div class="well well-table">
                        <p class="bold p-l-20 p-r-20 p-t-20 p-b-20 f-s-16">
                            Activity History
                        </p>
                        <div :class="['custom-b-tabs', {'loading': activity.earning.loading ||activity.spending.loading ||activity.vip.loading }]">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#earning" aria-selected="true">Earning</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#spending"
                                       aria-selected="false">Spending</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#vipTab" aria-selected="false">VIP</a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane active" id="earning">
                                    <sortable-table
                                            :hide-header="true"
                                            :contents="activity.earning.data"
                                            :page-size="5"
                                            :thead="[{text: 'Activity', name: 'activity'}, {text: 'Points', name: 'points'}, {text: 'Date', name: 'date'} ]">
                                        <template slot-scope="{row}">
                                            <td>
                                                <span v-if="row.activity" v-text="row.activity"></span>
                                                <span class="span-empty" v-else>&mdash;</span>
                                            </td>
                                            <td>
                                                <span v-if="row.points > 0"
                                                      class="badge badge-pill badge-success"
                                                      v-text="row.points > 0 ? '+'+row.points: row.points "></span>
                                                <span v-else-if="row.points < 0"
                                                      class="badge badge-pill badge-danger"
                                                      v-text="row.points"></span>
                                                <span v-else
                                                      class="badge badge-pill"
                                                      v-text="row.points"></span>
                                            </td>
                                            <td>
                                                <span>@{{row.date | date-human}}</span>
                                            </td>
                                        </template>
                                    </sortable-table>
                                </div>
                                <div class="tab-pane" id="spending" role="tabpanel" aria-labelledby="spending-tab">
                                    <sortable-table
                                            :hide-header="true"
                                            :contents="activity.spending.data"
                                            :page-size="5"
                                            :sort-by="'date'"
                                            :thead="[{text: 'Activity', name: 'activity'}, {text: 'Points', name: 'points'}, {text: 'Code', name: 'code'}, {text: 'Date', name: 'date'} ]">
                                        <template slot-scope="{row}">
                                            <td>
                                                <span v-if="row.activity" v-text="row.activity"></span>
                                                <span class="span-empty" v-else>&mdash;</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-pill"
                                                      v-bind:class="row.points > 0 ? 'badge-success' : 'badge-danger'"
                                                      v-text="row.points > 0 ? '+'+row.points: row.points "></span>
                                            </td>
                                            <td v-text="row.code"></td>
                                            <td>
                                                <span>@{{row.date | date-human}}</span>
                                            </td>
                                        </template>
                                    </sortable-table>
                                </div>
                                <div class="tab-pane" id="vipTab" role="tabpanel" aria-labelledby="vip-tab">
                                    <sortable-table
                                            :hide-header="true"
                                            :contents="activity.vip.data"
                                            :page-size="5"
                                            :thead="[{text: 'Current Tier', name: 'current'}, {text: 'Previous Tier', name: 'previous'}, {text: 'Date', name: 'date'} ]">
                                        <template slot-scope="{row}">
                                            <td>
                                                <span v-if="row.current" v-text="row.current"></span>
                                                <span class="span-empty" v-else>&mdash;</span>
                                            </td>
                                            <td>
                                                <span v-if="row.previous" v-text="row.previous"></span>
                                                <span class="span-empty" v-else>&mdash;</span>
                                            </td>
                                            <td>
                                                <span v-if="row.date">@{{row.date | date-human}}</span>
                                                <span class="span-empty" v-else>&mdash;</span>
                                            </td>
                                        </template>
                                    </sortable-table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="well well-table m-t-20">
                        <p class="bold p-l-20 p-r-20 p-t-20 p-b-20 f-s-16">
                            Order History
                        </p>
                        <sortable-table
                                :class="{'loading': orders.loading}"
                                :contents="orders.data"
                                :page-size="5"
                                :hide-header="true"
                                :thead="[{text: 'Order #', name: 'index'}, {text: 'Order Total', name: 'amount'}, {text: 'Lootly Coupon', name: 'coupon'}, {text: 'Date', name: 'date'} ]">

                            <template slot-scope="{row}">
                                <td>
                                    @if ($woocommerce)
                                        #@{{row.order_id}}
                                    @else
                                    <a class="bold color-blue" target="_blank"
                                       :href="'//' + merchants.current.details.data.shop_domain + '/admin/orders/' + row.order_id">#@{{row.order_id}}</a>
                                    @endif
                                </td>
                                <td>
                                    @{{row.amount | format-number | currency(currencySign)}}
                                </td>
                                <td>
                                    @{{row.coupon}}
                                </td>
                                <td>
                                    <span>@{{row.date | date-format}}</span>
                                </td>
                            </template>

                        </sortable-table>
                    </div>
                    <div class="well well-table m-t-20">
                        <p class="bold p-l-20 p-r-20 p-t-20 p-b-20 f-s-16">
                            Referral History
                        </p>
                        <sortable-table
                                :class="{'loading': referrals.loading}"
                                :contents="referrals.data"
                                :hide-header="true"
                                :thead="[{text: 'Order #', name: 'index'}, {text: 'Referred', name: 'referred'}, {text: 'Amount', name: 'amount'}, {text: 'Date', name: 'date'} ]">

                            <template slot-scope="{row}">
                                <td>
                                    <a class="bold color-blue" target="_blank"
                                       :href="'//' + merchants.current.details.data.shop_domain + '/admin/orders/' + (row.order_id || '')">#@{{row.order_id}}</a>
                                </td>
                                <td>
                                    <a class="bold color-blue"
                                       :href="'/customers/profile/'+row.referred_id">@{{row.referred}}</a>
                                </td>
                                <td>
                                    @{{row.amount | format-number | currency(currencySign)}}
                                </td>
                                <td>
                                    <span>@{{row.date | date-format}}</span>
                                </td>
                            </template>

                        </sortable-table>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="well">
                        <div class="" :class="{ 'loading' : loading }" v-cloak>
                            <p class="bold f-s-16 m-b-5">
                                Overview
                            </p>
                            <p v-text="overview.name"></p>
                            <p v-text="overview.email"></p>
                            <div class="bordered p-t-10 p-b-10 p-l-15 p-r-15 m-t-15 m-b-10">
                                <div class="row">
                                    <div class="col-md-1 col-1">
                                        <label class="m-t-10"><i class="icon-points"></i></label>
                                    </div>
                                    <div class="col-md-9 col-9 m-l-20">
                                        <div class="light-font m-t-5">
                                            <h5 class="bold m-b-0">Current Points Balance</h5>
                                            <p v-text="overview.currentPoints"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bordered p-t-10 p-b-10 p-l-15 p-r-15 m-b-10">
                                <div class="row">
                                    <div class="col-md-1 col-1">
                                        <label class="m-t-10"><i class="icon-points"></i></label>
                                    </div>
                                    <div class="col-md-9 col-9 m-l-20">
                                        <div class="light-font m-t-5">
                                            <h5 class="bold m-b-0">Total Points Earned</h5>
                                            <p v-text="overview.totalEarnedPoints"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bordered p-t-10 p-b-10 p-l-15 p-r-15 m-b-10">
                                <div class="row">
                                    <div class="col-md-1 col-1">
                                        <label class="m-t-10"><i class="icon-coin"></i></label>
                                    </div>
                                    <div class="col-md-9 col-9 m-l-20">
                                        <div class="light-font m-t-5">
                                            <h5 class="bold m-b-0">Total Spent</h5>
                                            <p>
                                                <span>
                                                @{{ overview.totalSpent | format-number | currency(currencySign) }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bordered p-t-10 p-b-10 p-l-15 p-r-15 m-b-10">
                                <div class="row">
                                    <div class="col-md-1 col-1">
                                        <label class="m-t-10"><i class="icon-percentage"></i></label>
                                    </div>
                                    <div class="col-md-9 col-9 m-l-20">
                                        <div class="light-font m-t-5">
                                            <h5 class="bold m-b-0">Lootly Coupons Used</h5>
                                            <p v-text="overview.couponsUsed"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bordered p-t-10 p-b-10 p-l-15 p-r-15 m-b-10">
                                <div class="row">
                                    <div class="col-md-1 col-1">
                                        <label class="m-t-10">
                                            <i class="icon-vip f-s-19"></i>
                                        </label>
                                    </div>
                                    <div class="col-md-9 col-9 m-l-20">
                                        <div class="light-font m-t-5">
                                            <h5 class="bold m-b-0">VIP Tier</h5>
                                            <p v-text="overview.vipTier"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bordered p-t-10 p-b-10 p-l-15 p-r-15 m-b-10">
                                <div class="row">
                                    <div class="col-md-1 col-1">
                                        <label class="m-t-10"><i class="icon-customers"></i></label>
                                    </div>
                                    <div class="col-md-9 col-9 m-l-20">
                                        <div class="light-font m-t-5">
                                            <h5 class="bold m-b-0">Last Seen</h5>
                                            <p v-text="overview.lastSeen"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bordered p-t-10 p-b-10 p-l-15 p-r-15 m-b-10">
                                <div class="row">
                                    <div class="col-md-1 col-1">
                                        <label class="m-t-10"><i class="icon-birthday"></i></label>
                                    </div>
                                    <div class="col-md-8 col-7 m-l-20 m-t-0">
                                        <div v-if="birthday.isEdit">
                                            <input type="tel" id="birthdayInput" class="form-control m-t-5"
                                                   placeholder="MM/DD/YYYY" v-model="birthday.newDate"
                                                   v-mask="'##/##/####'">
                                        </div>
                                        <div class="light-font m-t-5" v-else>
                                            <h5 class="bold m-b-0">Birthday </h5>
                                            <p>
                                                @{{ overview.birthday || 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-1">
                                        <a class="birthday-btn bolder color-blue d-block"
                                           :class="{'loading': birthday.saving}" @click="editBirthday">
                                            <span v-if="birthday.isEdit">Save</span>
                                            <span v-else>Edit</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="bordered p-t-10 p-b-10 p-l-15 p-r-15 m-b-10">
                                <div class="row">
                                    <div class="col-md-1 col-1">
                                        <label class="m-t-10"><i class="icon-customers"></i></label>
                                    </div>
                                    <div class="col-md-9 col-9 m-l-20">
                                        <div class="light-font m-t-5">
                                            <h5 class="bold m-b-0">Referral Link</h5>
                                            <p class="break-word" v-text="overview.referralLink"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(!$have_rest_permissions)
                        <div class="m-t-20">
                            <no-access :loading="loading" title="Customer Segmentation"
                                       desc="Add tags to customers to facilitate in building out unique earning & spending rules, in addition to special VIP tiers."
                                       icon="customer-segmentation.png" plan="Growth"></no-access>

                        </div>
                    @else
                        {{-- Restricted Area For Growth Plan or Above --}}
                        <div class="well m-t-20">
                            <div class="" :class="{ 'loading' : tags.loading }" v-cloak>
                                <p class="bold f-s-16 m-b-5">
                                    Customer Tags
                                </p>
                                <label class="m-b-10">Active customer tags</label>

                                <multiselect
                                        v-model="tags.value"
                                        tag-placeholder="Add"
                                        placeholder="Add Tag"
                                        select-label="Select"
                                        deselect-label="Remove"
                                        open-direction="bottom"
                                        :options="tags.options"
                                        :multiple="true"
                                        :taggable="true"
                                        @tag="addCustomerTag">
                                </multiselect>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <b-modal class="custom-modal" id="adjust-points" title="Adjust Points" hide-footer v-cloak>
                <form>
                    <div class="row m-b-10 m-t-5">
                        <div class="col-md-12">
                            <label class="light-font">This tool allows you to add or subtract points from this customer.
                                To
                                subtract points use a negative number.</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="light-font">Point Adjustment Amount</label>
                                <input class="form-control" placeholder="e.g. 500" name="amount"
                                       v-model="adjustPoints.amount">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="light-font">Reason for adjustment (customer will see this message)</label>
                                <input class="form-control" placeholder="Manual Adjustment" name="reason"
                                       v-model="adjustPoints.reason">
                            </div>
                        </div>
                    </div>

                    <div class="row m-t-10 p-b-10 p-t-20 border-top">
                        <div class="col-md-6 offset-md-3 text-center">
                            <button v-if="!adjustPoints.saving" class="btn modal-btn-lg btn-block btn-success btn-glow"
                                    @click.prevent="saveAdjustedPoints">
                                Adjust Points
                            </button>
                            <span v-else class="i-loading"></span>
                        </div>
                    </div>
                </form>
            </b-modal>

            <b-modal class="custom-modal" ref="bv-adjust-vip-status-modal" title="Adjust VIP Status" v-on:show="resetAdjustVipStatusModal" hide-footer v-cloak>
                <form>
                    <div v-if="Object.keys(vipTier.errors).length">
                        <div class="alert alert-danger">
                            <div v-if="vipTier.message.length">
                                @{{ vipTier.message }}
                            </div>
                            <ul v-for="errors in vipTier.errors" class="my-0">
                                <li v-for="error in errors">
                                    @{{ error }}
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div v-else-if="vipTier.message.length">
                        <div class="alert alert-success">
                            <div>@{{ vipTier.message }}</div>
                        </div>
                    </div>
                    <div class="row m-b-10 m-t-5">
                        <div class="col-md-12">
                            <label class="light-font">This tool allows you to change the VIP Status of a customer. Once confirmed, their status will update automatically.</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="light-font">VIP Tier</label>
                                <select id="vipTierInput"
                                        class="form-control"
                                        name="tier_id"
                                        v-model="vipTier.tierId"
                                >
                                    <option disabled :value="-1">Select a Tier</option>
                                    <option v-for="(value, index) in tiers" :value="index">
                                        @{{ tierId == index ? value + ' (current)' : value }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row m-t-10 p-b-10 p-t-20 border-top">
                        <div class="col-6 text-center">
                            <button class="btn modal-btn-lg btn-block btn-success btn-glow"
                                    @click.prevent="editVipTier"
                                    :disabled="isEditVipTier"
                            >
                                {{ __('Update') }}
                            </button>
                            <span v-if="vipTier.loading" class="mt-3 i-loading"></span>
                        </div>
                        <div class="col-6 text-center">
                            <button @click.prevent="hideAdjustVipStatusModal" class="btn modal-btn-lg btn-block btn-secondary btn-glow">
                                {{ __('Close') }}
                            </button>
                        </div>
                    </div>
                </form>
            </b-modal>

            <b-modal id="give-reward"
                 v-on:show="resetGiveRewardModal"
                 class="custom-modal"
                 hide-footer
                 title="Reward Customer"
                 v-cloak
            >
                <form>
                    <div v-if="Object.keys(giveRewardValidate.errors).length">
                        <div class="alert alert-danger">
                            <div v-if="giveRewardValidate.message.length" class="bold">
                                @{{ giveRewardValidate.message }}
                            </div>
                            <ul v-for="errors in giveRewardValidate.errors" class="my-0 p-l-20">
                                <li class="my-1" v-for="error in errors">
                                    @{{ error }}
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div v-else-if="giveRewardValidate.message.length">
                        <div class="alert alert-success">
                            <div>@{{ giveRewardValidate.message }}</div>
                        </div>
                    </div>
                    <div class="row m-b-10 m-t-5">
                        <div class="col-md-12">
                            <label class="light-font">This tool allows you to manually reward the customer.</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="light-font">Reward Type</label>
                                <b-select v-model="giveReward.reward" name="reward">
                                    <option value="">Select Reward</option>
                                    <option v-for="reward in rewards.data" :value="reward.id"
                                            v-text="reward.reward_name"></option>
                                    {{--<option value="fixed-discount">$10 Off discount</option>
                                    <option value="percentage-discount">15% off discount</option>
                                    <option value="free-shipping">Free Shipping up to $25</option>
                                    <option value="free-product">Free Product</option>--}}
                                </b-select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="light-font">Deduct the points from customers account?</label>
                                <b-select v-model="giveReward.deductPoints" name="deductPoints">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </b-select>
                            </div>
                        </div>
                    </div>

                    <div class="row m-t-10 p-b-10 p-t-20 border-top">
                        <div class="col-md-6 offset-md-3 text-center">
                            <span v-if="giveReward.saving" class="i-loading"></span>
                            <button v-show="!giveReward.saving" 
                                    @click.prevent="saveGivenReward" 
                                    class="btn modal-btn-lg btn-block btn-success btn-glow">
                                Give Reward
                            </button>                            
                        </div>
                    </div>
                </form>
            </b-modal>
        </div>

        <div v-else class="well flex-center">
            <div class="content">
                <div class="title">User Not Found</div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
    <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
    <script src="https://unpkg.com/vue-the-mask@0.11.1/dist/vue-the-mask.js"></script>

    <script>
      var customer = new Vue({
        el: '#customer-profile',
        components: {
          Multiselect: window.VueMultiselect.default
        },
        data: {
          alertText: '',
          merchants: {
            current: {
              details: {
                data: {!! json_encode($merchant_details) !!},
                loading: false
              }
            }
          },
          merchant: {!! json_encode($merchant_data) !!},
          customerId: {{ $customer->id }},
          tiers: {!! json_encode($tiers) !!},
          tierId: {{ $customer->tier_id ?? -1 }},
          overview: {!! json_encode($overview) !!},
        //   overview: {
        //     name: 'Customer Name',
        //     email: '',
        //     currentPoints: 'N/A',
        //     totalEarnedPoints: 'N/A',
        //     totalSpent: '',
        //     couponsUsed: 'N/A',
        //     vipTier: 'N/A',
        //     lastSeen: 'N/A',
        //     birthday: 'N/A',
        //     referralLink: '',
        //     ecommerce_id: '',
        //   },
          activity: {
            earning: {
              data: {!! json_encode($earning) !!},
              loading: false
            },
            spending: {
              data: {!! json_encode($spending) !!},
              loading: false
            },
            vip: {
              data: {!! json_encode($vip_activity) !!},
              loading: false
            },
            loading: false
          },
          rewards: {
            data: {!! json_encode($rewards) !!},
            loading: false
          },
          giveReward: {
            reward: '',
            deductPoints: 1,
            saving: false
          },
          giveRewardValidate: {
              message: '',
              errors: {},
          },
          adjustPoints: {
            amound: '',
            reason: '',
            saving: false
          },
          orders: {
            data: {!! json_encode($orders) !!},
            loading: false
          },
          referrals: {
            data: {!! json_encode($referral_orders) !!},
            loading: false
          },
          birthday: {
            isEdit: false,
            saving: false,
            newDate: ''
          },
          vipTier: {
            tierId: {{ $customer->tier_id ?? -1 }},
            loading: false,
            message: '',
            errors: {},
          },
          tags: {
            value: ({!! json_encode($customer_tags) !!}).map((item) => {
              return item.name
            }),
            options: ({!! json_encode($tags_options) !!}).map((item) => {
              return item.name
            }),
            loading: false,
            allowSaving: true
          },
          loading: false,
          customerExists: true,
          shopifyCustomerID: '',
          alert: {
            type: '',
            text: '',
            dismissSecs: 5,
            dismissCountDown: 0
          },
        },
        created: function () {
        //   Promise.all([this.getMerchant(), this.getOverview()])
        //   this.getCurrentMerchantDetails()
        //   this.getTags()
        },
        mounted: function () {
        //   this.getActivity()
          // this.getOrders()
          // this.getReferrals()
          // this.getRewards()
        },
        methods: {
          resetGiveRewardModal: function () {
              this.giveRewardValidate.errors = '';
              this.giveRewardValidate.message = {};
          },
          getMerchant: function () {
            axios.get('/current/merchant').then(response => {
              this.merchant = response.data.data
            }).catch(error => {
              console.log(error)
            })
          },
          getCurrentMerchantDetails: function () {
            let comp = this
            comp.merchants.current.details.loading = true
            axios.get('/settings/store/details').then((response) => {
              comp.merchants.current.details.data = response.data.data
            }).catch((error) => {
              console.log(error)
            }).then(() => {
              comp.merchants.current.details.loading = false
            })
          },
          getOverview: function () {
            this.loading = true
            let customer_id = '{{ $id }}'
            let comp = this
            axios.get('/settings/customer/' + customer_id).then((response) => {
              let customer = response.data.data
              comp.overview.name = customer.name
              comp.overview.email = customer.email
              comp.overview.currentPoints = customer.points
              comp.overview.totalEarnedPoints = customer.points_earned
              comp.overview.totalSpent = customer.total_spend
              if (customer.coupons_used) comp.overview.couponsUsed = customer.coupons_used
              if (customer.tier && customer.tier.data) comp.overview.vipTier = customer.tier.data.name
              if (customer.last_seen) comp.overview.lastSeen = customer.last_seen
              if (customer.birthday) {
                comp.overview.birthday = customer.birthday
                comp.birthday.newDate = customer.birthday
              }
              comp.overview.referralLink = customer.referral_link
              comp.overview.ecommerce_id = customer.ecommerce_id

              this.loading = false
              this.customerExists = true
            }).catch((error) => {
              //alert(error)
              this.customerExists = false
              this.loading = false
              console.log(error)
            })
          },
          getTags: function () {
            const comp = this
            comp.tags.loading = true
            let customer_id = '{{ $id }}'

            Promise.all([
              axios.get('/settings/tag'),
              axios.get('/settings/customer/' + customer_id + '/tags')
            ]).then((response) => {
              comp.tags.options = response[0].data.data.map((item) => {
                return item.name
              })
              comp.tags.value = response[1].data.data.map((item) => {
                return item.name
              })
            }).catch((error) => {
              console.log(error)
            }).then(() => {
              comp.tags.loading = false
              comp.$nextTick(() => {
                comp.tags.allowSaving = true
              })
            })
          },
          getRewards: function () {
            const comp = this
            comp.rewards.loading = true
            axios.get('/settings/rewards').then((response) => {
              comp.rewards.data = response.data.data
            }).catch((error) => {
              console.log(error)
            }).then(() => {
              comp.rewards.loading = false
            })
          },
          saveGivenReward: function () {
            this.giveRewardValidate.errors = '';
            this.giveRewardValidate.message = {};

            const comp = this
            if (!comp.giveReward.saving) {
              let customer_id = '{{ $id }}'
              comp.giveReward.saving = true
              axios.post('/settings/customer/' + customer_id + '/give-reward', comp.giveReward).then((response) => {
                comp.giveReward.saving = false

                const data = response.data;

                this.giveRewardValidate.errors = {};
                this.giveRewardValidate.message = 'Reward successfully given.';

                if (data.data && data.data.customer_name) {
                    this.giveRewardValidate.message = `Reward successfully given to ${data.data.customer_name}.`;
                }

                /*comp.alert.dismissCountDown = comp.alert.dismissSecs
                comp.alert.type = 'success'
                comp.alert.text = 'Action saved successfully!'*/
                /*if (response.data.data) {
                  let data = response.data.data
                  comp.form.subjectLine = data.subject
                  comp.form.body = data.body
                  comp.form.button.text = data.button_text
                  comp.form.button.color = data.button_color
                }*/
              }).catch((error) => {
                comp.giveReward.saving = false;

                if (error.response.status === 422) {
                    const { message, errors } = error.response.data;

                    this.giveRewardValidate.errors = errors;
                    this.giveRewardValidate.message = message;

                } else if(error.response.status === 405) {
                    const data = error.response.data;

                    this.giveRewardValidate.errors = { error: [ data.error ] };
                    this.giveRewardValidate.message = data.message;

                } else {
                    const data = error.response.data;

                    this.giveRewardValidate.errors = { error: [ data.message ] };
                    this.giveRewardValidate.message = 'The given data was invalid.';
                }

                clearErrors(comp.$el);
                showErrors(comp.$el, error.response.data.errors);
                /*comp.alert.dismissCountDown = comp.alert.dismissSecs
                comp.alert.type = 'danger'
                comp.alert.text = error.response.data.message*/
              })
            }
          },
          saveAdjustedPoints: function () {
            const comp = this
            if (!comp.adjustPoints.saving) {
              let customer_id = '{{ $id }}'
              comp.adjustPoints.saving = true
              axios.post('/settings/customer/' + customer_id + '/adjust-points', comp.adjustPoints).then((response) => {
                comp.adjustPoints.saving = false
                this.$root.$emit('bv::hide::modal', 'adjust-points')

                comp.alert.type = 'success'
                comp.alert.text = 'Points successfully adjusted'

                if (response.status == '200') {
                  if (response.data.data) {
                    let points = response.data.data
                    // comp.getOverview()
                    this.overview.currentPoints += points.point_value;
                    this.overview.totalEarnedPoints += points.point_value;
                    if (points.point_value >= 0) {
                      comp.activity.earning.data.unshift({
                        activity: points.action ? points.action.action_name : (points.reason ? points.reason : ''),
                        points: points.point_value,
                        date: points.created_at,
                        timestamp: new Date(points.created_at).getTime()
                      })
                    } else {
                      comp.activity.spending.data.unshift({
                        activity: points.reward ? points.reward.reward_name : (points.reason ? points.reason : ''),
                        points: points.point_value,
                        code: points.coupon ? points.coupon.coupon_code : '',
                        date: points.created_at,
                        timestamp: new Date(points.created_at).getTime()
                      })
                    }
                  }
                }
                /*comp.alert.dismissCountDown = comp.alert.dismissSecs
                comp.alert.type = 'success'
                comp.alert.text = 'Action saved successfully!'*/
                /*if (response.data.data) {
                  let data = response.data.data
                  comp.form.subjectLine = data.subject
                  comp.form.body = data.body
                  comp.form.button.text = data.button_text
                  comp.form.button.color = data.button_color
                }*/
              }).catch((error) => {
                comp.adjustPoints.saving = false
                clearErrors(comp.$el)
                //alert('ERROR');
                console.log(error.response.data.errors)
                showErrors(comp.$el, error.response.data.errors)
                /*comp.alert.dismissCountDown = comp.alert.dismissSecs*/
                comp.alert.type = 'danger'
                comp.alert.text = error.response.data.message
              }).then(() => {
                comp.alert.dismissCountDown = comp.alert.dismissSecs
              })
            }
          },
          updateTags: function (tags) {
            const comp = this
            let customer_id = '{{ $id }}'

            axios.put('/settings/customer/' + customer_id + '/tags', {
              tags: tags
            }).then((response) => {
              // ok
            }).catch((error) => {
              console.log(error)
            })
          },
          getActivity: function () {
            let customer_id = '{{ $id }}'
            let comp = this
            comp.activity.earning.loading = true
            axios.get('/settings/customer/' + customer_id + '/earning').then((response) => {

              if (response.data.data.length != 0) {
                comp.activity.earning.data = response.data.data.map((item) => {
                  return {
                    // activity: (item.action && item.action.data) ? item.action.data.action_name : (item.reason ? item.reason : (item.title ? item.title : '')),
                    activity: item.action_name,
                    points: item.point_value,
                    date: item.created_at,
                    timestamp: new Date(item.created_at).getTime()
                  }
                })
              } else {
                comp.activity.earning.data = 'Customer has not earned any points yet.'
              }

            }).catch((error) => {
              //
            }).then(() => {
              comp.activity.earning.loading = false
            })

            comp.activity.spending.loading = true
            axios.get('/settings/customer/' + customer_id + '/spending').then((response) => {
              if (response.data.data.length !== 0) {
                comp.activity.spending.data = response.data.data.map((item) => {
                  return {
                    // activity: (item.reward && item.reward.data) ? item.reward.data.reward_name : (item.reason ? item.reason : ''),
                    activity: item.action_name,
                    points: item.point_value,
                    code: item.coupon ? item.coupon.coupon_code : '',
                    date: item.created_at,
                    timestamp: new Date(item.created_at).getTime()
                  }
                })
              } else {
                comp.activity.spending.data = 'Customer has not spent any points yet.'
              }

            }).catch((error) => {
              //
            }).then(() => {
              comp.activity.spending.loading = false
            })

            comp.activity.vip.loading = true
            axios.get('/settings/customer/' + customer_id + '/vip').then((response) => {
              if (response.data.data.length !== 0) {
                comp.activity.vip.data = response.data.data.map((item) => {
                  return {
                    current: item.new_tier ? item.new_tier.name : 'N/A',
                    previous: item.old_tier ? item.old_tier.name : 'N/A',
                    date: item.created_at_with_tz,
                    timestamp: new Date(item.created_at).getTime()
                  }
                })
              } else {
                comp.activity.vip.data = 'Customer has never been apart of a VIP tier.'
              }

            }).catch((error) => {
              //
            }).then(() => {
              comp.activity.vip.loading = false
            })
          },
          getReferrals: function () {
            this.referrals.loading = true
            let customer_id = '{{ $id }}'
            let comp = this
            axios.get('/settings/customer/' + customer_id + '/referral-orders').then((response) => {
              if (response.data.data.length != 0) {
                comp.referrals.data = response.data.data.map((item) => {
                  let order = {
                    index: '#' + item.id,
                    order_id: item.order_id,
                    referred: item.customer ? item.customer.name : '',
                    referred_id: item.customer ? item.customer.id : '',
                    amount: item.total_price,
                    date: item.created_at
                  }
                  order.amountFormatted = order.amount
                  if (comp.merchant && comp.merchant.merchant_currency) {
                    let currency = comp.merchant.merchant_currency.data.name
                    if (comp.merchant.currency_display_sign) {
                      currency = comp.merchant.merchant_currency.data.currency_sign
                      order.amountFormatted = currency + order.amount
                    } else {
                      order.amountFormatted = order.amount + ' ' + currency
                    }
                  }
                  return order
                })
              } else {
                comp.referrals.data = 'Customer has not referred anybody yet.'
              }
            }).catch((error) => {
              console.log('Error in Referrals')
              console.log(error)
            }).then(() => {
              comp.referrals.loading = false
            })
          },
          getOrders: function () {
            this.orders.loading = true
            let customer_id = '{{ $id }}'
            let comp = this
            axios.get('/settings/customer/' + customer_id + '/orders').then((response) => {
              if (response.data.data.length != 0) {
                comp.orders.data = response.data.data.map((item) => {
                  let order = {
                    index: '#' + item.id,
                    order_id: item.order_id,
                    amount: item.total_price,
                    coupon: item.coupon ? item.coupon.coupon_code : '',
                    date: item.created_at
                  }
                  order.amountFormatted = order.amount
                  if (comp.merchant && comp.merchant.merchant_currency) {
                    let currency = comp.merchant.merchant_currency.data.name
                    if (comp.merchant.currency_display_sign) {
                      currency = comp.merchant.merchant_currency.data.currency_sign
                      order.amountFormatted = currency + order.amount
                    } else {
                      order.amountFormatted = order.amount + ' ' + currency
                    }
                  }
                  return order
                })
              } else {
                comp.orders.data = 'Customer has not placed an order yet.'
              }
            }).catch((error) => {
              console.log(error)
            }).then(() => {
              comp.orders.loading = false
            })
          },
          editBirthday: function () {
            if (this.birthday.isEdit == false) {
              this.birthday.isEdit = true
            } else {
              document.getElementById('birthdayInput').classList.remove('border-red')
              this.birthday.saving = true
              // Save new Birthday here
              let customer_id = '{{ $id }}'
              let comp = this
              axios.put('/settings/customer/' + customer_id, {
                'birthday': comp.birthday.newDate
              }).then((response) => {
                this.birthday.isEdit = false
                this.birthday.saving = false
                comp.overview.birthday = comp.birthday.newDate
              }).catch((error) => {
                this.birthday.saving = false
                document.getElementById('birthdayInput').classList.add('border-red')
              })
            }
          },
          showAdjustVipStatusModal: function () {
            this.$refs['bv-adjust-vip-status-modal'].show();
          },
          hideAdjustVipStatusModal: function () {
            this.$refs['bv-adjust-vip-status-modal'].hide();
          },
          resetAdjustVipStatusModal: function () {
            this.vipTier.errors = '';
            this.vipTier.message = {};
          },
          editVipTier: function () {
              this.vipTier.loading = true;

              this.vipTier.errors = '';
              this.vipTier.message = {};

              axios.put('/settings/customer/tier/' + this.customerId, {
                  tier_id: this.vipTier.tierId,

              }).then((response) => {
                  const data = response.data;

                  this.vipTier.loading = false;

                  this.vipTier.errors = {};
                  this.vipTier.message = data.message;

                  this.tierId = this.vipTier.tierId;
                  this.overview.vipTier = this.tiers[this.vipTier.tierId];

              }).catch((error) => {
                  this.vipTier.loading = false;

                  if (error.response.status === 422) {
                      const { message, errors } = error.response.data;

                      this.vipTier.errors = errors;
                      this.vipTier.message = message;

                  } else {
                      const data = error.response.data;

                      this.vipTier.errors = { error: [ data.message ] };
                      this.vipTier.message = 'The given data was invalid.';
                  }
              });
          },
          addCustomerTag: function (newTag) {
            this.tags.options.push(newTag)
            this.tags.value.push(newTag)
          },
          countDownChanged (dismissCountDown) {
            this.alert.dismissCountDown = dismissCountDown
          }
        },
        computed: {
            currencySign: function (){
                if(this.merchant.merchant_currency){
                    return this.merchant.merchant_currency.data.currency_sign
                }
                return '$';
            },
            isEditVipTier: function() {
                return this.vipTier.tierId == -1 || this.vipTier.tierId == this.tierId;
            },
        },
        watch: {
          'merchants.current.details': {
            handler: function () {
              this.shopifyCustomerID = '//' + this.merchants.current.details.data.shop_domain + '/admin/customers/' + (this.overview.ecommerce_id || '')
            },
            deep: true
          },
          'overview': {
            handler: function () {
              this.shopifyCustomerID = '//' + this.merchants.current.details.data.shop_domain + '/admin/customers/' + (this.overview.ecommerce_id || '')
            },
            deep: true
          },
          'tags.value': function () {
            if (this.tags.allowSaving) {
              //Save to database the new value of "tags.value"
              this.updateTags(this.tags.value)
            }
          }
        }
      })

    </script>
@endsection
