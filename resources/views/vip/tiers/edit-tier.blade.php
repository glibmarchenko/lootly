@extends('layouts.app')

@section('title', 'Edit VIP Tier')

@section('content')
    <div id="action-page" class="loader m-t-20 m-b-10" v-cloak>
        <b-alert v-cloak :show="alert.dismissCountDown" dismissible :variant="alert.type"
                 @dismissed="alert.dismissCountdown=0" @dismiss-count-down="countDownChanged">
            @{{alert.text}}
        </b-alert>
        <form>
        <span :class="{'loading': loadingPage}">

            <div class="row p-b-10 section-border-bottom">
                <div class="col-md-12 m-b-15">
                    <a href="{{ route('vip.tiers') }}" class="bold f-s-15 color-blue">
                        <i class="arrow left blue"></i>
                        <span class="m-l-5">VIP Tiers</span>
                    </a>
                </div>
                <div class="col-md-12">
                    <h3 class="page-title m-t-0 color-dark pull-left">
                        <span v-text="form.program.name"></span> VIP Tier
                    </h3>
                    <button type="button" @click="saveTier" class="btn btn-save pull-right">Save</button>
                    <input type="hidden" class="tier-id" value="{{$id}}">
                </div>
            </div>
            <div class="row p-t-25 p-b-25">
                <div class="col-md-7 col-12">
                    <div class="well bg-white">
                        <span :class="{'loading': loading}">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group m-b-0">
                                        <label class="light-font m-b-0 m-t-5">
                                            <span v-text="form.program.name"></span> VIP Tier is currently
                                            <span class="bold"
                                                  v-text="form.program.status == 0 ? 'Disabled' : 'Enabled'"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 text-right">
                                    <a @click="toogleProgramStatus" v-cloak>
                                        <span v-if="form.program.status == 0">
                                            <span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
                                        </span>
                                        <span v-else>
                                            <span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </span>
                    </div>
                    <div class="well bg-white m-t-20">
                        <span :class="{'loading': loading}">
                            <div class="row section-border-bottom p-b-10 m-b-15">
                                <div class="col-md-8">
                                    <div class="form-group m-b-0">
                                        <label class="bolder f-s-15 m-b-0 m-t-5">
                                            General Settings
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group m-b-15">
                                        <label class="light-font m-b-10">
                                            <span>Tier Name</span>
                                        </label>
                                        <input class="form-control" v-model="form.program.name"
                                               placeholder="e.g. Bronze">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group m-b-15">
                                        <label class="light-font m-b-10">
                                            <span v-text="amountText"></span>
                                        </label>
                                        <input class="form-control" name="spend.value" placeholder="e.g. 100"
                                               v-model="form.spend.value">
                                    </div>
                                </div>
                            </div>
                        </span>
                    </div>

                    <div class="well bg-white m-t-20 p-b-10">
                        <span :class="{'loading': loading}">
                            <div class="row section-border-bottom p-b-10 m-b-15">
                                <div class="col-md-12">
                                    <div class="form-group m-b-0">
                                        <label class="bolder f-s-15 m-b-0 m-t-5">
                                            Benefits
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group m-b-15">
                                        <label class="light-font m-b-10">
                                            <span>Point Multiplier</span>
                                        </label>
                                        <input class="form-control" name="points.value" placeholder="e.g. 2"
                                               v-model="form.points.value">
                                    </div>
                                </div>
                            </div>
                            <div class="p-b-10 m-t-5">
                                <div class="row m-b-5">
                                    <div class="col-md-12">
                                        <label class="m-b-5 pull-left">Entry Rewards</label>
                                        <a class="color-blue bolder f-s-14 pull-right" @click="addEntryReward">Add</a>
                                    </div>
                                </div>

                                <div class="row select-inline-boxes two-columns m-b-10"
                                     v-for="(benefit, index) in form.benefits.entry" v-cloak>
                                    <div class="col-md-12 col-12">
                                        <select class="form-control custom-select"
                                                @change="selectedDiscount(benefit.reward,index,'entry')"
                                                :name="index+'entryReward'" v-model="benefit.reward">
                                            <option v-if="isBenefits" :disabled="true" value="null">Select type of reward</option>
                                            <option v-else :disabled="true" value="null">No rewards available</option>
                                            <option v-for="reward in computedRewards(benefit,'entry')"
                                                    :value="reward.type" :disabled="isExist(reward.type,'entry')"
                                                    :class="(!isBenefits && !isPointsSelected()) ? 'points_select' : ''"
                                                    v-text="reward.display_text"></option>
                                        </select>
                                        <span v-if="benefit.reward">
                                            <span v-if="benefit.reward != 'points'">
                                                <select class="form-control custom-select" @change="setId(benefit)"
                                                        :name="index+'entryDiscount'" v-model="benefit.discount">
                                                    <option :disabled="true" value="null">Select discount</option>
                                                    <option v-for="option in computedDiscounts(benefit.reward, benefit.discount,'entry')"
                                                            :value="option.reward_name"
                                                            v-text="option.reward_name"></option>
                                                </select>
                                            </span>
                                            <span v-else>
                                                <input type="text" :name="index+'entryPoints'" class="form-control"
                                                       placeholder="Enter point amount" v-model="benefit.discount">
                                            </span>
                                        </span>
                                        <span v-else class="emptyEntryDiscount">
                                            <input disabled="true" class="form-control">
                                            <!-- <select class="form-control custom-select">
                                                <option value="null">Select discount</option>
                                            </select> -->
                                        </span>
                                        <button class="btn btn-default pull-right" type="button"
                                                @click="deleteEntryReward(index)">
                                            <i class="fa fa-trash-o f-s-19"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="p-b-10">
                                <div class="row m-b-5">
                                    <div class="col-md-12">
                                        <label class="m-b-5 pull-left">Lifetime Rewards</label>
                                        <a class="color-blue bolder f-s-14 pull-right"
                                           @click="addLifetimeReward">Add</a>
                                    </div>
                                </div>
                                <div class="row select-inline-boxes two-columns m-b-10"
                                     v-for="(benefit, index) in form.benefits.lifetime" v-cloak>
                                    <div class="col-md-12 col-12">

                                        <select class="form-control custom-select"
                                                @change="selectedDiscount(benefit.reward,index,'lifetime')"
                                                :name="index+'lifetimeReward'" v-model="benefit.reward">
                                            <option v-if="isBenefits" :disabled="true" value="null">Select type of reward</option>
                                            <option v-else :disabled="true" value="null">No rewards available</option>
                                            <option :disabled="isExist(reward.type,'lifetime')"
                                                    v-for="reward in computedRewards(benefit,'lifetime')"
                                                    :value="reward.type" v-text="reward.display_text"></option>
                                        </select>
                                        <span v-if="benefit.reward">
                                            <select class="form-control custom-select" @change="setId(benefit)"
                                                    :name="index+'lifetimeDiscount'" v-model="benefit.discount">
                                                <option :disabled="true" value="null">Select discount</option>
                                                <option v-for="option in computedDiscounts(benefit.reward, benefit.discount,'lifetime')"
                                                        :value="option.reward_name"
                                                        :selected="option.reward_type == benefit.reward"
                                                        v-text="option.reward_name"></option>
                                            </select>
                                        </span>
                                        <span v-else class="emptyLifeTimeDiscount">
                                            <input disabled="true" class="form-control">
                                            <!-- <select class="form-control custom-select">
                                                <option value="null">Select discount</option>
                                            </select> -->
                                        </span>

                                        <button class="btn btn-default pull-right" type="button"
                                                @click="deleteLifetimeReward(index)">
                                            <i class="fa fa-trash-o f-s-19"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="p-b-10">
                                <div class="row m-b-5">
                                    <div class="col-md-12">
                                        <label class="m-b-5 pull-left">Custom Rewards</label>
                                        <a class="color-blue bolder f-s-14 pull-right" @click="addCustomReward">Add</a>
                                    </div>
                                </div>
                                <div style="position: relative">
                                    <div class="row select-inline-boxes one-column m-b-10"
                                         v-for="(benefit, index) in form.benefits.custom" v-cloak>
                                        <div class="col-md-12 col-12">
                                            <input class="form-control" :name="index+'customDiscount'"
                                                   placeholder="Custom reward" v-model="benefit.discount">
                                            <button class="btn btn-default pull-right" type="button"
                                                    @click="deleteCustomReward(index)">
                                                <i class="fa fa-trash-o f-s-19"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </span>
                    </div>
                    <div class="well bg-white m-t-20">
                        <span :class="{'loading': loading}">
                            <div class="row m-b-15">
                                <div class="col-md-12">
                                    <label class="bolder f-s-15 m-b-0 pull-left">
                                        Email Notification
                                    </label>
                                    <a href="/display/email-notifications/points/vip-tier-earned"
                                       class="bolder f-s-14 color-blue pull-right">Edit Notification</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="m-b-10" v-html="emailNotification()"></p>
                                    <b-form-checkbox v-model="form.emailNotification">Yes, send an email notification
                                    </b-form-checkbox>
                                </div>
                            </div>
                        </span>
                    </div>
                    <div class="well bg-white m-t-20">
                        <span :class="{'loading': loading}">
                            <div class="row section-border-bottom p-b-10 m-b-15">
                                <div class="col-md-12">
                                    <div class="form-group m-b-0">
                                        <label class="bolder f-s-15 m-b-0">
                                            Design
                                        </label>
                                        <a class="bolder f-s-14 color-blue pull-right" href=""
                                           @click.prevent="openModal">Preview notification</a>
                                        <custom-modal title="Preview email notification" id="preview-email"
                                                      :toggle_modal="isModalOpen" :close_callback="hideModal">
                                            <span v-html="modalHtml()"></span>
                                        </custom-modal>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group m-b-0">
                                        <div>
                                            <label class="light-font m-b-5" v-text="requirpmentText">
                                            </label>
                                            <input class="form-control" v-model="form.spend.defaultText">
                                            <div class="row m-t-15 p-b-10 section-border-bottom">
                                                <div class="col-md-12">
                                                    <span class="custom-tag email-tag" v-for="tag in spend_amount_tags">
                                                        <span v-text="tag"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="light-font m-t-15 m-b-5">
                                                Point multiplier text
                                            </label>
                                            <input class="form-control" v-model="form.points.defaultText">
                                            <div class="row m-t-15 p-b-10 section-border-bottom">
                                                <div class="col-md-12">
                                                    <span class="custom-tag email-tag" v-for="tag in point_multi_tags">
                                                        <span v-text="tag"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="light-font m-t-15 m-b-5">
                                                Tier Entry Email Text
                                            </label>
                                            <!-- <textarea style="height: 140px;" class="form-control email-textarea"
                                                    v-model="tierEmailText"></textarea> -->
                                            <div class="entry-email-text">
                                                <trumbowyg
                                                        v-model="form.program.emailDefaultText"
                                                        id="emailDefaultText"
                                                        @tbw-blur="blurEmailField"
                                                        @tbw-init="EditorCreated"
                                                        :config="config"
                                                        class="editor vip-tier-email-text"></trumbowyg>
                                            </div>
                                            <div class="row m-t-15 p-b-10 section-border-bottom">
                                                <div class="col-md-12">
                                                    <span class="custom-tag email-tag" v-for="tag in entry_email_tags">
                                                        <span v-text="tag" v-on:click="emailTagClick"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- New Field v2-->
                                            <div class="col-md-6 col-12">
                                                <label class="light-font m-t-15 m-b-5">
                                                    Default Icon Color
                                                </label>
                                                <colorpicker :color="form.program.defaultIconColor"
                                                             v-model="form.program.defaultIconColor"
                                                             name="defaultIconColor"/>
                                            </div>
                                        </div>

                                        <div class="light-font m-t-15 m-b-5">
                                            <p>Custom Icon</p>
                                            <span class="f-s-13 bolder color-light-grey">Recommended 250px x 250px - will auto size to fit</span>
                                        </div>
                                        <div class="file-drag-drop m-t-15" v-cloak>
                                            <b-form-file class="upload-icon" @change="iconImageChange"
                                                         v-model="form.program.icon" accept="image/*"></b-form-file>
                                            <div class="custom-file-overlay">
                                                <span class="img" v-if="form.program.reward_icon || form.iconPreview">

                                                    <img v-if="form.iconPreview" class="m-b-5" :src="form.iconPreview"
                                                         style="max-height:70px;max-width: 100%">
                                                    <img v-else-if="form.program.reward_icon" class="m-b-5"
                                                         :src="form.program.reward_icon"
                                                         style="max-height:70px;max-width: 100%">
                                                </span>
                                                <span class="img" v-else>
                                                    <i class="icon-image-upload"></i>
                                                </span>
                                                <h5 class="float f-s-17 bold"
                                                    v-if="(form.program.icon_name && form.program.reward_icon) || form.iconPreview">
                                                    <span class="text">
                                                        <span v-text="form.program.icon_name"></span>
                                                    </span>
                                                </h5>
                                                <h5 class="float f-s-17 bold"
                                                    v-else-if="form.program.icon && form.program.icon.name">
                                                    <span class="text">
                                                        <span v-text="form.program.icon && form.program.icon.name"></span>
                                                    </span>
                                                </h5>
                                                <h5 class="f-s-17 bold" v-else>
                                                    Drag files to upload
                                                </h5>
                                                <i v-if="form.iconPreview ||  form.program.reward_icon"
                                                   @click="cleariconImage(form.tier_id)"
                                                   class="fa fa-times color-light-grey pointer"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </span>
                    </div>
                    <div class="well bg-white m-t-20 p-b-10">
                        <span :class="{'loading': loading}">
                            <div class="row section-border-bottom p-b-15 m-b-15">
                                <div class="col-md-8">
                                    <p class="bolder f-s-15 m-b-10 m-t-0">VIP Tier Restrictions</p>
                                    <label class="light-font m-b-0">
                                        <span v-text="form.program.name"></span> VIP Tier restrictions are
                                        <span class="bold"
                                              v-text="form.restrictions.status == 0 ? 'Disabled' : 'Enabled'"></span>
                                    </label>
                                </div>
                                <div class="col-md-4 text-right">
                                    <a @click="toogleEarningRestrictions" v-cloak>
                                        <span v-if="form.restrictions.status == 0">
                                            <span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
                                        </span>
                                        <span v-else>
                                            <span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                            <div class="p-b-15">
                                <div class="row m-b-5">
                                    <div class="col-md-12">
                                        <label class="m-b-5 pull-left">Customer Restrictions</label>
                                        <a class="color-blue bolder f-s-14 pull-right" @click="addCustomerRestrictions">Add</a>
                                    </div>
                                </div>
                                <div class="row select-inline-boxes m-b-10"
                                     v-for="(customerRestriction, index) in form.restrictions.customer" v-cloak>
                                    <div class="col-md-12 col-12">
                                        <label>
                                            <span class="m-t-5 inline-block">Customer Tags</span>
                                        </label>
                                        <select class="form-control custom-select"
                                                v-model="customerRestriction.conditional">
                                            <option value="has-any-of">has any of</option>
                                            <option value="is">is</option>
                                        </select>
                                        <multiselect
                                                v-model="customerRestriction.values"
                                                tag-placeholder="Add"
                                                placeholder="Add option"
                                                select-label="Select"
                                                deselect-label="Remove"
                                                :id="index"
                                                :options="customerRestriction.options"
                                                :multiple="true"
                                                :taggable="true"
                                                @tag="addCustomerRestrictionsTag">
                                        </multiselect>
                                        <button class="btn btn-default pull-right" type="button"
                                                @click="deleteCustomerRestriction(index)">
                                            <i class="fa fa-trash-o f-s-19"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </span>
                    </div>
                </div>
                <div class="col-md-5 col-12">
                    <div class="sticky-top preview-box">
                        <div class="well bg-white">
                            <span :class="{'loading': loading}">
                                <h5 class="bold m-b-15">VIP Preview</h5>
                                <div class="bordered p-t-15 p-b-10 p-l-15 p-r-15">
                                    <div class="overflow">
                                        <label class="tier vip-preview-icon m-t-5 pull-left"
                                               :style="{'color': form.program.defaultIconColor}"></label>
                                        <div class="light-font pull-left m-l-20 m-t-5 tier-preview">
                                            <h5 class="bold m-b-0">
                                                <span v-if="form.program.name != ''" v-text="form.program.name"></span>
                                                <span v-else>Tier Name</span>
                                            </h5>
                                            <p v-if="form.spend.value != ''" v-text="spendText"></p>
                                        </div>
                                    </div>
                                    <div class="row" v-cloak>
                                        <div class="col-12 preview-benefits">
                                            <label class="bold m-t-5 m-b-0">Benefits</label>
                                            <p v-if="form.points.value != ''" v-text="pointsText"></p>

                                            <label v-if="form.benefits.entry.length > 0"
                                                   class="bold tier-preview-label">Entry Rewards</label>
                                            <p v-for="benefit in form.benefits.entry" v-if="benefit.discount">
                                                <span v-if="benefit.reward != 'points'">
                                                    <span v-if="benefit.reward == 'Percentage off' || benefit.reward == 'Fixed amount'">
                                                        {{--<span v-text="form.currency + benefit.discount"></span> off discount--}}
                                                        <span v-text="benefit.discount"></span>
                                                        {{--off discount--}}
                                                    </span>
                                                    <span v-if="benefit.reward == 'Free shipping'">
                                                        <span v-text="benefit.discount"></span> Amount
                                                    </span>
                                                    <span v-if="benefit.reward == 'Free Product'">
                                                        Free <span v-text="benefit.discount"></span>
                                                    </span>
                                                </span>
                                                <span v-else>
                                                    <span v-text="benefit.discount"></span> Free <span
                                                            v-text="form.point_namePlural"></span>
                                                </span>
                                            </p>
                                            <label v-if="form.benefits.lifetime.length > 0" style="margin-top: 5px;"
                                                   class="bold tier-preview-label">Lifetime Rewards</label>
                                            <p v-for="benefit in form.benefits.lifetime" v-if="benefit.discount">
                                                <span v-if="benefit.reward == 'Percentage off' || benefit.reward == 'Fixed amount'">
                                                    <span v-text="benefit.discount"></span>
                                                </span>
                                                <span v-if="benefit.reward == 'Free shipping'">
                                                    <span v-text="benefit.discount"></span> Amount
                                                </span>
                                                <span v-if="benefit.reward == 'Free Product'">
                                                    Free <span v-text="benefit.discount"></span>
                                                </span>
                                            </p>
                                            <label v-if="form.benefits.custom.length > 0" style="margin-top: 5px;"
                                                   class="bold tier-preview-label">Custom Rewards</label>
                                            <p v-for="benefit in form.benefits.custom" v-if="benefit.discount">
                                                <span v-text="benefit.discount "></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </span>
        </form>
    </div>

@endsection

@section('scripts')
    @include('layouts.trumbowyg-scripts')
    <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
    <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>

    <script type="module">
      import { restrictionsMixin } from '/js/mixins/restrictions-mixin.js'

      Vue.component('Trumbowyg', VueTrumbowyg.default)
      var page = new Vue({
        el: '#action-page',
        components: {
          Multiselect: window.VueMultiselect.default
        },
        mixins: [restrictionsMixin],
        data: {
          amountText: '',
          // emailDefaultText: 'Congratulations {customer}!  Your account has been upgraded to the {tier-name} tier thanks to your continued {company} loyalty. \n \n As thanks for shopping with us, we’d like to award you {tier-award-perk}. In addition the {tier-name} now gives you access to the following: {tier-perks}',
          defaultTextSpend: '${spent} spent in the last {period}',
          defaultTextPoint: 'Earn {#}  per $1 spent',
          makePurchasePoints: 1,
          form: {
            point_name: 'point',
            point_namePlural: 'points',
            benefitsLifetimeDiscountArr: [],
            benefitsEntryDiscountArr: [],
            tier_id: '{{$id}}',
            lableText: 'Amount of {points-name} earned to qualify for this tier in a {period} period. ',
            requirpmentDefaultText: 'Earned amount requirement text',
            rolling_days: '',
            currency: '',
            program: {
              status: '',
              name: '',
              emailDefaultText: `
                <table class="preview-table">
                    <tbody>
                        <tr>
                            <td class="text-center" style="padding-bottom: 13px;">
                                {customer},
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center pb-30">
                                Congratulations! You've just unlocked the <br>
                                <strong>{tier-name} VIP Tier</strong><br>
                                <span class="m-hide"></span>at {company-name}. Below is an overview of your benefits.
                            </td>
                        </tr>
                        <tr>
                            <td class="img-center mx-84 pb-30 vip-tier-preview-icon">
                                {icon}
                            </td>
                        </tr>
                        <tr>
                            <td class="img pb-20" style="padding-bottom: 27px;">
                                <table id="entry_benefits_table" width="100%">
                                    <tbody>
                                        <tr>
                                            <td class="text t-left" style="padding-bottom: 22px;">
                                                <p><strong>Available Rewards to use right now</strong></p><br>
                                                <p>Use the below coupon on your next order:</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                {entry-rewards}
                            </td>
                        </tr>
                        <tr>
                            <td class="img">
                                <table width="100%">
                                    <tbody>
                                        <tr>
                                            <td class="text t-left" style="padding-bottom: 22px;">
                                                <strong>{tier-name} VIP Tier Benefits</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                {lifetime-rewards}<br>
                                {custom-rewards}
                            </td>
                        </tr>
                    </tbody>
                </table>
              `,
              emailText: '',
              defaultIconColor: '#627482',
              icon: null,
              icon_name: null,
              reward_icon: null,
            },
            spend: {
              value: '',
              defaultText: '{spent-points} spent in the last {period}',
              text: '{spent-points} spent in the last {period}',
              defaultEarnedText: '{spent-points} {points-name} earned in the last {period}',
              defaultSpentText: '{spent-points} spent in the last {period}',
            },
            points: {
              value: '',
              text: '',
              defaultText: 'Earn {points} {points-name} per {currency}1 spent',
            },
            restrictions: {
              status: 0,
              customer: [],
              ready: false
            },
            benefits: {
              entry: [],
              lifetime: [],
              custom: []
            },
            rewards: [],
            iconPreview: null,
            emailNotification: true,
          },
          restrictions_options: {
            customer_tags: [],
          },
          setting: '',
          icon_default_class: 'icon-vip',
          icon_parent_el: 'tier',
          alert: {
            type: '',
            text: '',
            dismissSecs: 5,
            dismissCountDown: 0
          },
          loadingPage: true,
          loading: true,
          isModalOpen: false,
          isBenefits: true,
          config: {
            svgPath: '/fonts/icons/trumbowyg-icons.svg',
            btns: [
              ['fontsize', 'foreColor', 'bold', 'italic'],
              ['lineheight', 'horizontalRule', 'link'],
              ['justifyLeft', 'justifyCenter', 'justifyRight'],
              ['viewHTML']
            ]
          },
          entry_email_tags: ['{customer}', '{tier-name}', '{company-name}'],
          spent_tags: ['{currency}', '{spent-points}'],
          earned_tags: ['{earned-points}', '{points-name}'],
          spend_amount_tags: ['{period}'],
          point_multi_tags: ['{points}', '{points-name}', '{currency}'],
        },
        created: function () {
          this.getTierData()
          this.getRestrictionsData(['tags'])
          this.checkForm(this.form.benefits.lifetime, 'lifetime')
          this.checkForm(this.form.benefits.entry, 'entry')
          this.loadingPage = false
        },
        updated: function () {
          let pointsOptions = $('.points_select')
          if (pointsOptions.length !== 0) {
            if (pointsOptions[pointsOptions.length - 1].parentNode.value == 'null' && !this.isPointsSelected()) {
              this.form.benefits.entry = this.form.benefits.entry.filter((benefit) => {
                if (!benefit.reward) {
                  return false
                }
                return true
              })
              this.form.benefits.entry.push({
                'reward': 'points', // add Points option
                'discount': null
              })
            }
          }
        },
        methods: {
          getTierData: function () {
            const vm = this
            axios.get('/vip/tiers/edit/data/' + vm.form.tier_id).then((response) => {

              if(response.data.makePurchasePoints) {
                vm.makePurchasePoints = response.data.makePurchasePoints;  
              }  

              if (response.data.points_settings) {
                vm.form.point_name = response.data.points_settings['name']
                vm.form.point_namePlural = response.data.points_settings['plural_name']
              }

              let tierData = response.data.tier

              if (tierData.rolling_days) vm.form.rolling_days = tierData.rolling_days
              if (tierData.currency) vm.form.currency = tierData.currency
              if (tierData.program) {
                if (tierData.program.status) vm.form.program.status = tierData.program.status
                if (tierData.program.name) vm.form.program.name = tierData.program.name
                if (tierData.program.emailDefaultText) vm.form.program.emailDefaultText = tierData.program.emailDefaultText
                if (tierData.program.emailText) vm.form.program.emailText = tierData.program.emailText
                if (tierData.program.defaultIconColor) vm.form.program.defaultIconColor = tierData.program.defaultIconColor
                if (tierData.program.icon) vm.form.program.icon = tierData.program.icon
                if (tierData.program.icon_name) vm.form.program.icon_name = tierData.program.icon_name
                if (tierData.program.reward_icon) vm.form.program.reward_icon = tierData.program.reward_icon
                // if (tierData.program.reward_icon) vm.form.iconPreview = tierData.program.reward_icon
              }
              if (tierData.spend) {
                if (tierData.spend.value) vm.form.spend.value = tierData.spend.value
                if (tierData.spend.defaultText) vm.form.spend.defaultText = tierData.spend.defaultText
                if (tierData.spend.text) vm.form.spend.text = tierData.spend.text
                if (tierData.spend.defaultEarnedText) vm.form.spend.defaultEarnedText = tierData.spend.defaultEarnedText
                if (tierData.spend.defaultSpentText) vm.form.spend.defaultSpentText = tierData.spend.defaultSpentText
              }
              if (tierData.points) {
                if (tierData.points.value) vm.form.points.value = tierData.points.value
                if (tierData.points.defaultText) vm.form.points.defaultText = tierData.points.defaultText
                if (tierData.points.text) vm.form.points.text = tierData.points.text
              }
              if (tierData.restrictions) {
                if (tierData.restrictions.status) vm.form.restrictions.status = tierData.restrictions.status
                if (tierData.restrictions.customer) vm.form.restrictions.customer = tierData.restrictions.customer
              }
              if (tierData.benefits) {
                if (tierData.benefits.entry) vm.form.benefits.entry = tierData.benefits.entry
                if (tierData.benefits.lifetime) vm.form.benefits.lifetime = tierData.benefits.lifetime
                if (tierData.benefits.custom) vm.form.benefits.custom = tierData.benefits.custom
              }
              if (tierData.rewards) {
                if (tierData.rewards.iconPreview) vm.form.rewards.iconPreview = tierData.rewards.iconPreview
                if (tierData.rewards.emailNotification) vm.form.rewards.emailNotification = tierData.rewards.emailNotification
              }

              //vm.form = response.data.tier

              if (!vm.form.iconPreview) {
                showPreviewIcon(vm.form.program.reward_icon, vm.icon_default_class, vm.icon_parent_el)
                vm.form.iconPreview = tierData.program.reward_icon
              } else {
                clearPreviewIcon(vm.icon_default_class, vm.icon_parent_el)
              }
              if (response.data.currency) {
                vm.form.currency = response.data.currency.currency_sign
              } else {
                vm.form.currency = '$'
              }
              if (response.data.vipSetting) {
                if (response.data.vipSetting.requirement_type == 'amount-spent') {
                  vm.spend_amount_tags = vm.spent_tags.concat(vm.spend_amount_tags)
                } else {
                  vm.spend_amount_tags = vm.earned_tags.concat(vm.spend_amount_tags)
                }
                // if (vm.setting.program_status == 'Disabled') {
                //   vm.form.program.status = 0
                // } else {
                //   vm.form.program.status = 1
                // }
                vm.setting = response.data.vipSetting
                vm.form.rolling_days = response.data.vipSetting.rolling_period
                if (vm.form.rolling_days === '0') {
                  vm.form.spend.defaultText = [
                    vm.form.spend.defaultText.split(' in the last')[0],
                    vm.form.spend.defaultText.split(' in the last')[1]
                  ].join('')
                }
              } else {
                vm.setting = {
                  requirement_type: 'amount-spent',
                  rolling_period: '1-year'
                }
                vm.spend_amount_tags = vm.spent_tags.concat(vm.spend_amount_tags)
                // vm.form.spend.defaultText = vm.form.spend.defaultSpentText;
                // vm.form.spend.defaultText = [
                //             vm.form.spend.defaultText.split(' in the last')[0],
                //             vm.form.spend.defaultText.split(' in the last')[1]
                //         ].join('');
              }
              vm.form.rewards = response.data.merchantReward.filter(function (elem) {
                if (elem.merchant_reward.length == 0) {
                  return false
                }
                return true

              })
              vm.setPointName()
              vm.amountText = vm.setAmountText(vm.form.point_namePlural)
              this.loading = false

              this.getTierRestrictions(vm.form.tier_id)
            })
          },
          hideModal: function () {
            this.isModalOpen = false
          },
          openModal: function () {
            this.isModalOpen = true
          },
          modalHtml: function () {
            let templateHtml = '<?php echo str_replace("\n", '\n', str_replace('"', '\"', addcslashes(str_replace("\r", '', (string) view('vip.tiers.tierPreviewNotify')), "\0..\37'\\"))); ?>'
            templateHtml = templateHtml.replace(/{logo}/g, '{{$company_logo}}')
              .replace(/{body-text}/g, this.genPreviewEmailText())
              .replace(/{currency}/g, this.form.currency)
              .replace(/{icon-color}/g, this.form.program.defaultIconColor)
            return templateHtml
          },
          setPointName: function () {
            /*if (this.form.points.defaultText) {
              this.form.points.text = this.form.points.defaultText
                .replace('{points-name}', (this.form.points.value > 1) ? this.form.point_namePlural : this.form.point_name)
                .replace('{#}', this.form.points.value)
            }*/
            if (this.form.point_namePlural) {
              if (this.setting.rolling_period === '0') {
                if (this.setting.requirement_type === 'points-earned') {
                  return this.form.spend.text = '{amount} {points-name}  earned '
                    .replace('{points-name}', (this.form.spend.value > 1) ? this.form.point_namePlural : this.form.point_name)
                    .replace('{amount}', this.form.spend.value)
                } else {
                  return this.form.spend.text = '${spent} spent'.replace('{spent}', this.form.spend.value)
                }
              } else {

                let rolling_period = this.rollingPeriodText();

                if (this.setting.requirement_type === 'amount-spent') {

                  return this.form.spend.text = '${spent} spent in the last {period}'
                    .replace('{spent}', this.form.spend.value)
                    .replace('{period}', rolling_period )
                } else {
                  return this.form.spend.text = '{amount} {points-name}  earned in the last {period}'
                    .replace('{amount}', this.form.spend.value)
                    .replace('{points-name}', (this.form.spend.value > 1) ? this.form.point_namePlural : this.form.point_name)
                    .replace('{period}', rolling_period )
                }
              }
            }
          },
          rollingPeriodText: function() {

            let rolling_period = '';
            if( this.setting && this.setting.rolling_period ) {

              let rolling_period_array = this.setting.rolling_period.split( '-' );
              let rolling_period_number = rolling_period_array[0];
              let rolling_period_name = rolling_period_array[1];
              if( rolling_period_number !== 1 ) {
                rolling_period_name += 's';
              }
              else if( rolling_period_name === 'year' ) {
                rolling_period_number = 365;
                rolling_period_name = 'days';
              }
              rolling_period = rolling_period_number + ' ' + rolling_period_name;
            }
            else {
              rolling_period = '365 days';
            }
            return rolling_period;
          },
          saveTier: function () {
            this.loading = true
//            axios.post('/vip/tiers/update', this.form).then((response) => {
            axios.post('/api/merchants/' + Spark.state.currentTeam.id + '/tiers/' + this.form.tier_id, this.form).then((response) => {
              this.alert.dismissCountDown = this.alert.dismissSecs
              this.alert.type = 'success'
              this.alert.text = 'Tier saved successfully'
              this.loading = false
            }).catch((error) => {
              this.alert.dismissCountDown = this.alert.dismissSecs;
              this.alert.type = 'danger'
              this.alert.text = 'Tier not saved '
              this.loading = false
              clearErrors(this.$el)
              showErrors(this.$el, error.response.data.errors)
            })
          },
          addEntryReward: function () {
            if (this.checkForm(this.form.benefits.entry, 'entry')) {
              this.form.benefits.entry.push({
                reward: null,
                discount: null,
              })
            }
          },
          deleteEntryReward: function (index) {
            this.form.benefits.entry = this.form.benefits.entry.removeByIndex(index)
          },
          addLifetimeReward: function () {
            if (this.checkForm(this.form.benefits.lifetime, 'lifetime')) {
              this.form.benefits.lifetime.push({
                reward: null,
                discount: null,
              })
            }
          },
          deleteLifetimeReward: function (index) {
            this.form.benefits.lifetime = this.form.benefits.lifetime.removeByIndex(index)
          },
          addCustomReward: function () {
            if (this.checkForm(this.form.benefits.custom, 'custom')) {
              this.form.benefits.custom.push({
                discount: null,
              })
            }
          },
          deleteCustomReward: function (index) {
            this.form.benefits.custom = this.form.benefits.custom.removeByIndex(index)
          },
          isExist: function (name, type) {
            if (name, type) {
              let indexRewards = this.form.rewards.findIndex(function (obj) {
                return obj.type == name
              })
              let elem = this.form.rewards[indexRewards]
              let allCheckedsReward = []

              $.each(this.form.benefits, function (i, val) {
                // if(type==i){
                val.forEach(function (item) {
                  allCheckedsReward.push(item.reward)
                })
                // }

              })
              let currentRewardArr = allCheckedsReward.filter(function (key) {
                if (elem && elem.type == key) {
                  return true
                }
              })

              if (name === 'points') {
                if (allCheckedsReward.indexOf('points') == -1) {
                  return false
                }
                return true
              }

              if (elem.merchant_reward.length == currentRewardArr.length || (allCheckedsReward.indexOf(elem.type) !== -1 && elem.type == 'Free shipping')) {
                return true
              }
            }
            return false
          },
          checkForm: function (array, type) {
            let errors = {}
            if (array) {
              for (var i = 0; i < array.length; i++) {
                if (!array[i]['reward'] && type != 'custom') {
                  let name = i + type + 'Reward'
                  errors[name] = ['Reward required']
                  clearErrors(this.$el)
                  showErrors(this.$el, errors)
                  return false
                }
                if (array[i]['reward'] == 'points' && !array[i]['discount']) {
                  let name = i + type + 'Points'
                  errors[name] = ['points amount required']
                  clearErrors(this.$el)
                  showErrors(this.$el, errors)
                  return false
                }
                if (!array[i]['discount']) {
                  let name = i + type + 'Discount'
                  errors[name] = ['Discount required']
                  clearErrors(this.$el)
                  showErrors(this.$el, errors)
                  return false
                }
              }
            }
            return true
          },
          toogleEarningRestrictions: function () {
            if (this.form.restrictions.status == 0) {
              this.form.restrictions.status = 1
            } else {
              this.form.restrictions.status = 0
            }
          },
          toogleProgramStatus: function () {
            if (this.form.program.status == 0) {
              this.form.program.status = 1
            } else {
              this.form.program.status = 0
            }
          },
          cleariconImage: function (tierId) {
            clearPreviewIcon('test', this.icon_parent_el)
            let $el = $('.upload-icon')
            resetForm($el)
            if (this.form.program.reward_icon) {

              axios.delete('/vip/tiers/icon/' + tierId).then((response) => {
                this.form.program.icon = null
                this.form.program.reward_icon = null
                this.form.iconPreview = null
                showPreviewIcon(this.form.program.reward_icon, this.icon_default_class, this.icon_parent_el)
              }).catch((error) => {

              })
            } else {
              this.form.program.icon = null
              this.form.program.action_icon = null
              this.form.iconPreview = null
              showPreviewIcon(this.form.program.reward_icon, this.icon_default_class, this.icon_parent_el)
            }
          },
          iconImageChange: function (evt) {
            var $this = this
            var files = evt.target.files
            if (files.length != 0) {
              var f = files[0]
              this.form.program.icon_name = f.name
              var reader = new FileReader()
              this.form.program.reward_icon = ''
              reader.onload = (function (theFile) {
                return function (e) {
                  $this.form.iconPreview = e.target.result
                  // $this.form.program.reward_icon = e.target.result
                  clearPreviewIcon($this.icon_default_class, $this.icon_parent_el)
                  showPreviewIcon($this.form.iconPreview, $this.icon_default_class, $this.icon_parent_el)
                }

              })(f)
              reader.readAsDataURL(f)
            }
          },
          countDownChanged (dismissCountDown) {
            this.alert.dismissCountDown = dismissCountDown
          },

          computedDiscounts: function (value, currentDiscount = null, type) {
            let index = this.form.rewards.findIndex(function (obj) {
              return obj.type == value
            })
            if (index !== -1) {
              this.form.rewards[index].merchant_reward.forEach(function (element) {})
              let allCheckedsDiscount = []
              $.each(this.form.benefits, function (i, val) {
                // if(type==i){
                val.forEach(function (item) {
                  allCheckedsDiscount.push(item.discount)
                })
                // }

              })
              let result = this.form.rewards[index].merchant_reward.filter(function (elem) {
                if (allCheckedsDiscount.indexOf(elem.reward_name) !== -1 && elem.reward_name != currentDiscount) {
                  return false
                }
                return true
              })

              function compare (a, b) {
                if (value == 'Free shipping') {
                  if (parseInt(a.max_shipping) < parseInt(b.max_shipping))
                    return -1
                  if (parseInt(a.max_shipping) > parseInt(b.max_shipping))
                    return 1
                  return 0
                } else if (value == 'Free Product') {
                  return ('' + a.reward_name).localeCompare(b.reward_name)
                } else {
                  if (a.reward_value < b.reward_value)
                    return -1
                  if (a.reward_value > b.reward_value)
                    return 1
                  return 0
                }
              }

              result.sort(compare)
              return result
            }
          },
          computedRewards: function (benefit, type) {
            let allCheckedsReward = []
            let allCheckedsDiscount = []
            $.each(this.form.benefits, function (i, val) {
              val.forEach(function (item) {
                allCheckedsReward.push(item.reward)
                allCheckedsDiscount.push(item.discount)
              })

            })

            function checkedsReward (elem) {
              let currentRewardArr = allCheckedsReward.filter(function (key) {
                if (elem.type == key) {
                  return true
                }
              })

              if (elem.merchant_reward && elem.merchant_reward.length == currentRewardArr.length) {
                return true
              } else {
                if (currentRewardArr.find((e) => {
                    if (e === 'points') {
                      return true
                    }
                  }))
                  return true
                return false
              }
            }

            if (!benefit.discount && !benefit.reward) {
              let rewards = this.form.rewards.slice()
              if (type === 'entry' && allCheckedsReward.indexOf('points') == -1) {
                rewards.push({
                  'name': 'points', // add Points option
                  'type': 'points',
                  'display_text': 'Points'
                })
              }
              let result = rewards.filter(function (elem) {
                if (elem.type == 'Free shipping' && allCheckedsReward.indexOf('Free shipping') !== -1 || checkedsReward(elem)) {
                  return false
                }
                return true
              })
              if (result.length === 0 || (type == 'entry' && !this.isPointsSelected() && result.length === 1)) {
                this.isBenefits = false
              } else {
                this.isBenefits = true
              }
              return result
            }
            let entryRewards = []
            if (type === 'entry') {
              let entryRewards = this.form.rewards.slice()
              entryRewards.push({
                'name': 'points', // add Points option
                'type': 'points',
                'display_text': 'Points'
              })
              return entryRewards
            }
            return this.form.rewards

          },
          selectedDiscount: function (benefit_reward, index, type) {
            if (benefit_reward == 'points') {
              this.form.benefits[type][index].discount = null
            } else {
              let indexRewards = this.form.rewards.findIndex(function (obj) {
                return obj.type == benefit_reward
              })
              if (indexRewards != -1) {

                let elem = this.form.rewards[indexRewards]
                let discount = null
                let id = null
                let allCheckedsReward = []
                let allCheckedsDiscount = []
                $.each(this.form.benefits, function (i, val) {
                  val.forEach(function (item) {
                    allCheckedsReward.push(item.reward)
                    allCheckedsDiscount.push(item.discount)
                  })
                })
                let currentRewardArr = allCheckedsReward.filter(function (key) {
                  if (elem.type == key) {
                    return true
                  }
                })

                if (elem.merchant_reward.length - currentRewardArr.length == 0) {
                  elem.merchant_reward.forEach(function (element) {
                    if (allCheckedsDiscount.indexOf(element.reward_name) == -1) {
                      discount = element.reward_name
                      id = element.id
                    }

                  })
                } else {
                  discount = null
                  id = null
                }
                this.form.benefits[type][index].discount = discount
                this.form.benefits[type][index].id = id
              }
            }
          },
          setId: function (benefit) {
            if (!benefit.discount) {
              return
            }
            let reward = this.form.rewards.find((elem) => {
              return elem.type == benefit.reward
            })
            let merchant_reward = reward.merchant_reward.filter((elem) => {
              return elem.reward_name == benefit.discount
            })
            benefit.id = merchant_reward[0].id
          },
          setAmountText: function (point_name) {
            if (this.setting.rolling_period === '0') {
              if (this.setting.requirement_type === 'points-earned') {
                return 'Amount of {points-name} earned to qualify for this VIP Tier'
                  .replace(/{points-name}/g, point_name)
              } else {
                return 'Spend amount to qualify for this VIP Tier'
              }
            } else if (this.form.point_namePlural) {

              let rolling_period = this.rollingPeriodText( this.setting.rolling_period );

              if (this.setting.requirement_type === 'points-earned') {
                return 'Amount of {points-name} earned to qualify for this tier in a {period} period. '
                  .replace(/{points-name}/g, point_name)
                  .replace(/{period}/g, rolling_period)
              } else {
                return 'Spend amount to qualify for this tier in a {period}  period'
                  .replace(/{period}/g, rolling_period)
              }
            }
          },
          emailNotification: function () {
            return `Send your customers an email when they earn the ${this.form.program.name} VIP Tier`
          },
          hideModal: function () {
            this.isModalOpen = false
          },
          openModal: function () {
            this.genPreviewEmailText()
            this.isModalOpen = true
          },
          emailTagClick: function (event) {
            // let textarea = $('#emailDefaultText');

            // const range = $('#emailDefaultText').trumbowyg('getRange');
            // if(range === null){
            //     return;
            // }
            // let position = range.startOffset,
            //     html = range.startContainer.textContent.slice(0, position);
            // position += html.length - html.replace(/(<([^>]+)>)/ig, '').length;
            // const text = range.startContainer.textContent.replace(/\&nbsp;/g, ' ');
            // let txt = [
            //   text.slice(0, position),
            //   event.target.outerText,
            //   text.slice(position),
            // ].join('');
            // let parent = range.startContainer.parentNode;
            // if(parent.nodeName == 'P'){
            //     let rows = textarea.trumbowyg('html').replace(/\&nbsp;/g, ' ').split('</p>'),
            //         selectedIndex = 0;
            //     while(parent.previousSibling){
            //         parent = parent.previousSibling;
            //         if(parent.nodeName == 'BR'){
            //             continue;
            //         }
            //         selectedIndex++;
            //     }
            //     rows[selectedIndex] = '<p>'+ txt;
            //     textarea.trumbowyg('html', rows.join('</p>'));
            // } else {
            //     textarea.trumbowyg('html', txt);
            // }
            // return
          },
          blurEmailField: function (event) {
            $('#emailDefaultText').trumbowyg('saveRange')
          },
          genPreviewEmailText: function () {
            if (this.form.program.emailDefaultText) {
              return this.form.program.emailText = this.form.program.emailDefaultText
                .replace(/{company-name}/g, '{{$merchant->name}}')
                .replace(/{tier-name}/g, this.form.program.name)
                .replace(/{icon}/g, this.form.program.reward_icon ? `<img src=${this.form.program.reward_icon}>` : `<span style="color: ${this.form.program.defaultIconColor};"><i data-name="vip-tier-icon" style="font-size: 84px; color: inherit;" class="icon icon-vip"></i></span>`)
                .replace(/{entry-rewards}/g, this.genEntryRewards())
                .replace(/{lifetime-rewards}/g, this.genLifetimeRewards())
                .replace(/{custom-rewards}/g, this.genCustomRewards())
            }
          },
          getBenefitTextByType: function (benefit) {
            switch (benefit.reward) {
              case 'points':
                return benefit.discount + ' Free points'
                break
              case 'Free shipping':
                return benefit.discount + ' Amount'
                break
              default:
                return benefit.discount
            }
          },
          genEntryRewards: function () {
            if (!this.form.benefits.entry[0]) {
              $('#entry_benefits_table').css('display', 'none')
              return ''
            }
            $('#entry_benefits_table').css('display', 'block')

            let result = '<table class="vip-tier-benefit-table"><tbody>'
            this.form.benefits.entry.forEach((benefit) => {
              result += `<tr><td class="text" width="15" valign="top"><strong>•</strong></td>` +
                `<td class="text vip-tier-benefit-row" valign="top">${this.getBenefitTextByType(benefit)}</td></tr>`
            })
            result += '</tbody></table>'
            return result
          },
          genLifetimeRewards: function () {
            if (!this.form.benefits.lifetime[0]) {
              return `<table><tbody><tr><td class="text" width="15" valign="top"><strong>•</strong></td><td class="text vip-tier-benefit-row" valign="top">${this.pointsText}</td></tr></tbody></table>`
            }
            let result = `<table><tbody><tr><td class="text" width="15" valign="top"><strong>•</strong></td><td class="text vip-tier-benefit-row" valign="top">${this.pointsText}</td></tr>`
            this.form.benefits.lifetime.forEach((benefit) => {
              result += `<tr><td class="text" width="15" valign="top"><strong>•</strong></td>` +
                `<td class="text vip-tier-benefit-row" valign="top">${this.getBenefitTextByType(benefit)}</td></tr>`
            })
            result += '</tbody></table>'
            return result
          },
          genCustomRewards: function () {
            if (!this.form.benefits.custom[0]) {
              return ''
            }
            let result = '<table><tbody>'
            this.form.benefits.custom.forEach((benefit) => {
              result += `<tr><td class="text" width="15" valign="top"><strong>•</strong></td>` +
                `<td class="text vip-tier-benefit-row" valign="top">${benefit.discount}</td></tr>`
            })
            result += '</tbody></table>'
            return result
          },
          getPeriodText: function () {
            if (this.setting.rolling_period === '0') {
              return ''
            }
            if (this.setting.rolling_period === '2-year')
              return '2 Years'
            else {
              return '365 days'
            }
          },
          isPointsSelected: function () {
            if (!this.form.benefits.entry.length === 0) {
              return false
            }
            return (this.form.benefits.entry.filter((elem) => {
              if (elem.reward === 'points') {
                return true
              }
              return false
            }).length !== 0)
          },
          EditorCreated: function () {
            // $(".trumbowyg-viewHTML-button").attr('disabled', 'true');
            this.checkEditorPermissions($('.trumbowyg-viewHTML-button'))
          },
          checkEditorPermissions: function (elem = null) {
            if ('{{$has_editor_permissions}}' === '1') {
              return true
            } else {
              if (elem) {
                $(elem).addClass('trumbowyg-disable')
                $(elem).attr('disabled', 'true')
                $(elem).css('cursor', 'pointer')
                $(elem).wrap('<div id="blocked-editor" class="blocked-editor"></div>')
                $('#blocked-editor').click(e => {
                  swal({
                    className: 'upgrade-swal',
                    title: '{{$editor_upsell->upsell_title}}',
                    text: '{{$editor_upsell->upsell_text}}',
                    icon: '/images/permissions/{{$editor_upsell->upsell_image}}',
                    buttons: {
                      catch: {
                        text: 'Upgrade to {{$editor_upsell->getMinPlan()->name}}',
                        value: 'upgrade',
                      }
                    },
                  })
                    .then((value) => {
                      if (value == 'upgrade') {
                        window.location.href = '/account/upgrade'
                      }
                    })
                })
              }
              return false
            }
          }
        },
        computed: {
          requirpmentText: function () {
            if (this.setting.requirement_type === 'points-earned') {
              return 'Earned amount requirement text'
            } else {
              return 'Spend amount requirement text'
            }

          },
          pointsText: function () {
            var points = this.form.points.value * this.makePurchasePoints;
            return this.form.points.text = this.form.points.defaultText
                .replace('{points-name}', (points > 1) ? this.form.point_namePlural : this.form.point_name)
                .replace('{points_name}', (points > 1) ? this.form.point_namePlural : this.form.point_name) // for old versions compability
                .replace('{currency}', this.form.currency)
                .replace('{points}', points)
          },
          tierEmailText: function () {
            return this.genPreviewEmailText()
          },
          spendText: function () {
            if (this.form.spend.defaultText) {
              return this.form.spend.text = this.form.spend.defaultText
                .replace('{spent-points}', this.form.spend.value)
                .replace('{earned-points}', this.form.spend.value)
                .replace('{points-name}', (this.form.spend.value > 1) ? this.form.point_namePlural : this.form.point_name)
                .replace('{period}', this.rollingPeriodText())
                .replace('{currency}', this.form.currency)
            }
          },
        },
        watch: {}
      })
    </script>
@endsection
