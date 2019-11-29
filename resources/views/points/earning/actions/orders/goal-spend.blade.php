@extends('layouts.app')

@section('title', 'Goal Spend')

@section('content')
    <div id="action-page" class="loader m-t-20 m-b-10" v-cloak>
        <b-alert v-cloak
                 :show="alert.dismissCountDown"
                 dismissible
                 :variant="alert.type"
                 @dismissed="alert.dismissCountdown=0"
                 @dismiss-count-down="countDownChanged">
            @{{alert.text}}
        </b-alert>
        <!-- <span :class="{'loading' : loadingSave}"> -->
        <form>
            <div class="row p-b-10 section-border-bottom">
                <div class="col-md-12 m-b-15">
                    <points-link-back></points-link-back>
                </div>
                <div class="col-md-12">
                    <h3 class="page-title m-t-0 color-dark pull-left">Goal Spend</h3>
                    <button type="button" @click="saveAction" class="btn btn-save pull-right">Save</button>
                </div>
            </div>
            <div class="row p-t-25 p-b-25">
                <div class="col-md-7 col-12">
                    <div class="well">
                        <div :class="{ 'loading' : loading }" v-cloak>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group m-b-0">
                                        <label class="light-font m-b-0 m-t-5">
                                            Goal Spend action is currently
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
                        </div>
                    </div>
                    <div class="well m-t-20">
                        <div :class="{ 'loading' : loading }" v-cloak>
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
                                    <div class="form-group m-b-0">
                                        <label class="light-font m-b-10">
                                            <span>Spend amount to reach goal</span>
                                        </label>
                                        <input class="form-control"
                                               name="earning.goal"
                                               v-on:keyup="changeGoal"
                                               v-model="form.earning.goal"
                                               placeholder="e.g. 1000">
                                    </div>
                                </div>
                            </div>
                            <div class="row m-t-10">
                                <div class="col-md-12">
                                    <div class="form-group m-b-0">
                                        <label class="light-font m-b-10">
                                            <span>Amount of points to award for this action</span>
                                        </label>
                                        <input class="form-control"
                                               v-on:keyup="changePoint"
                                               v-model="form.earning.value"
                                               name="earning.value"
                                               placeholder="e.g. 250">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="well m-t-20">
                        <div :class="{ 'loading' : loading }" v-cloak>
                            <div class="row m-b-15">
                                <div class="col-md-12">
                                    <label class="bolder f-s-15 m-b-0 pull-left">
                                        Email Notification
                                    </label>
                                    <a href="/display/email-notifications/points/earned" target="blank"
                                       class="bolder f-s-14 color-blue pull-right">Edit Notification</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="m-b-10">Send your customers an email when they complete this action.</p>
                                    <b-form-checkbox v-model="form.emailNotification">Yes, send an email notification
                                    </b-form-checkbox>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="well m-t-20">
                        <div :class="{ 'loading' : loading }" v-cloak>
                            @if(!$have_email_permissions)
                                <no-access :loading="loading"
                                    title="{{$email_upsell->upsell_title}}"
                                    desc="{{$email_upsell->upsell_text}}"
                                    icon="{{$email_upsell->upsell_image}}"
                                    plan="{{$email_upsell->getMinPlan()->name}}"></no-access>
                            @else
                                <div class="row section-border-bottom p-b-10 m-b-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="bolder f-s-15 m-b-0 m-t-5">
                                                Design
                                            </label>
                                            <a class="bolder f-s-14 color-blue pull-right" href=""
                                               @click.prevent="openModal">Preview Notification</a>
                                            <custom-modal
                                                    title="Preview email notification"
                                                    id="preview-email"
                                                    :toggle_modal="isModalOpen"
                                                    :close_callback="hideModal"
                                            >
                                                <span v-html="bodyPreview()"></span>
                                            </custom-modal>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <div>
                                                <label class="light-font m-b-5">
                                                    Name
                                                </label>
                                                <input class="form-control" placeholder="Goal Spend"
                                                       v-model="form.program.name">
                                            </div>
                                            <div>
                                                <label class="light-font m-t-15 m-b-5">
                                                    Reward Text
                                                </label>
                                                <input class="form-control" placeholder=""
                                                       v-model="form.program.rewardTextDefault">
                                                <div class="row m-t-15 p-b-10 section-border-bottom">
                                                    <div class="col-md-12">
                                                        <span class="custom-tag" v-for="tag in action_tags">
                                                            <span v-text="tag"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="light-font m-t-15 m-b-5">
                                                    Reward Email Text
                                                </label>
                                                <div class="reward-email-text">
                                                    <trumbowyg
                                                            v-model="form.program.emailDefaultText"
                                                            @tbw-blur="blurEmailField"
                                                            @tbw-init="EditorCreated"
                                                            id="emailDefaultText"
                                                            :config="config"
                                                            class="editor"></trumbowyg>
                                                </div>
                                                <div class="row m-t-15 p-b-10 section-border-bottom">
                                                    <div class="col-md-12">
                                                        <span class="custom-tag email-tag" v-for="tag in tags">
                                                            <span v-text="tag" v-on:click="emailTagClick"></span>
                                                    </span>
                                                    </div>
                                                </div>
                                            </div>
                                          @if(!$have_customization_permissions)
                                            <no-access :loading="loading"
                                                title="{{$customizations_upsell->upsell_title}}"
                                                desc="{{$customizations_upsell->upsell_text}}"
                                                icon="{{$customizations_upsell->upsell_image}}"
                                                plan="{{$customizations_upsell->getMinPlan()->name}}"></no-access>
                                          @else
                                            <div class="light-font m-t-15 m-b-5">
                                                <p>Custom Icon</p>
                                                <span class="f-s-13 bolder color-light-grey">Recommended 250px x 250px - will auto size to fit</span>
                                            </div>
                                            <div class="file-drag-drop m-t-15" v-cloak>
                                                <b-form-file class="upload-icon" @change="iconImageChange"
                                                             v-model="form.program.icon"
                                                             accept="image/*"></b-form-file>

                                                <div class="custom-file-overlay">
                                                    <span class="img"
                                                          v-if="form.program.action_icon || form.iconPreview">
                                                        <img v-if="form.iconPreview" class="m-b-5"
                                                             :src="form.iconPreview"
                                                             style="max-height:70px;max-width: 100%">
                                                        <img v-else-if="form.program.action_icon" class="m-b-5"
                                                             :src="form.program.action_icon"
                                                             style="max-height:70px;max-width: 100%">
                                                    </span>
                                                    <span class="img" v-else>
                                                        <i class="icon-image-upload"></i>
                                                    </span>
                                                    <h5 class="float f-s-17 bold"
                                                        v-if="(form.program.icon_name && form.program.action_icon) || form.iconPreview">
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
                                                    <h5 class="float f-s-17 bold"
                                                        v-if="(form.program.icon_name && form.program.action_icon) || form.iconPreview">
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
                                                    <i v-if="form.iconPreview ||  form.program.action_icon"
                                                       @click="cleariconImage(action_id)"
                                                       class="fa fa-times color-light-grey pointer"></i>
                                                </div>
                                            </div>
                                          @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if(!$have_rest_permissions)
                        <div class="well m-t-20 p-b-10">
                            <no-access :loading="loading"
                                       title="{{$restrictions_upsell->upsell_title}}"
                                       desc="{{$restrictions_upsell->upsell_text}}"
                                       icon="{{$restrictions_upsell->upsell_image}}"
                                       plan="{{$restrictions_upsell->getMinPlan()->name}}"></no-access>
                        </div>
                    @else
                        <div class="well restrictions-section m-t-20 p-b-10">
                            <div :class="{ 'loading' : (loading || !form.restrictions.ready) }" v-cloak>
                                <div class="row section-border-bottom p-b-15 m-b-15">
                                    <div class="col-md-8">
                                        <p class="bolder f-s-15 m-b-10 m-t-0">Earning Restrictions</p>
                                        <label class="light-font m-b-0">
                                            Earning restrictions are
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
                                <div class="border-bottom p-b-15">
                                    <div class="row m-b-5">
                                        <div class="col-md-12">
                                            <label class="m-b-5 pull-left">Customer Restrictions</label>
                                            <a class="color-blue bolder f-s-14 pull-right"
                                               @click="addCustomerRestrictions">Add</a>
                                        </div>
                                    </div>
                                    <div class="row select-inline-boxes m-b-10"
                                         v-for="(customerRestriction, index) in form.restrictions.customer" v-cloak>
                                        <div class="col-md-12 col-12">
                                            <select class="form-control custom-select"
                                                    v-model="customerRestriction.type"
                                                    @change="checkCustomerRestrictionOptions(customerRestriction)">
                                                <option value="customer-tags">Customer tags</option>
                                                <option value="vip-tier">VIP Tier</option>
                                            </select>
                                            <select class="form-control custom-select"
                                                    v-model="customerRestriction.conditional">
                                                <option value="" disabled="">Select one</option>     
                                                <option value="has-any-of">has any of</option>
                                                <option value="is">is</option>
                                            </select>
                                            <multiselect
                                                    v-model="customerRestriction.values"
                                                    tag-placeholder="Add"
                                                    placeholder="Add option"
                                                    select-label="Select"
                                                    deselect-label="Remove"
                                                    open-direction="bottom"
                                                    :id="index"
                                                    :options="customerRestriction.options"
                                                    :multiple="true"
                                                    :taggable="true"
                                                    class="responsive"
                                                    :class="[customerRestriction.conditional === 'is' ? 'single' : 'multiple', customerRestriction.values.length > 0 ? 'has-tags' : '']"
                                                    @tag="addCustomerRestrictionsTag">
                                            </multiselect>
                                            <button class="btn btn-default pull-right" type="button"
                                                    @click="deleteCustomerRestriction(index)">
                                                <i class="fa fa-trash-o f-s-19"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-bottom m-t-15 p-b-15">
                                    <div class="row m-b-5">
                                        <div class="col-md-12">
                                            <label class="m-b-5 pull-left">Product Restrictions</label>
                                            <a class="color-blue bolder f-s-14 pull-right"
                                               @click="addProductRestrictions">Add</a>
                                        </div>
                                    </div>
                                    <div class="row select-inline-boxes m-b-10"
                                         v-for="(productRestriction, index) in form.restrictions.product" v-cloak>
                                        <div class="col-md-12 col-12">
                                            <select class="form-control custom-select"
                                                    v-model="productRestriction.type"
                                                    @change="checkProductRestrictionOptions(productRestriction)">
                                                <option value="product-id">Product ID</option>
                                                <option value="collection">Collection</option>
                                            </select>
                                            <select class="form-control custom-select"
                                                    v-model="productRestriction.conditional">
                                                <option value="" disabled="">Select one</option>
                                                <option value="has-any-of">has any of</option>
                                                <option value="is">is</option>
                                            </select>
                                            <multiselect
                                                    v-model="productRestriction.values"
                                                    tag-placeholder="Add"
                                                    placeholder="Add option"
                                                    select-label="Select"
                                                    deselect-label="Remove"
                                                    open-direction="bottom"
                                                    :id="index"
                                                    :options="productRestriction.options"
                                                    :multiple="true"
                                                    :taggable="true"
                                                    class="responsive"
                                                    :class="[productRestriction.conditional === 'is' ? 'single' : 'multiple', productRestriction.values.length > 0 ? 'has-tags' : '']"
                                                    @tag="addProductRestrictionsTag">
                                            </multiselect>
                                            <button class="btn btn-default pull-right" type="button"
                                                    @click="deleteProductRestriction(index)">
                                                <i class="fa fa-trash-o f-s-19"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-t-15 p-b-5">
                                    <div class="row m-b-5">
                                        <div class="col-md-12">
                                            <label class="m-b-5 pull-left">Activity Restrictions</label>
                                            <a class="color-blue bolder f-s-14 pull-right"
                                               @click="addActivityRestrictions">Add</a>
                                        </div>
                                    </div>
                                    <div class="row select-inline-boxes m-b-10"
                                         v-for="(activityRestriction, index) in form.restrictions.activity" v-cloak>
                                        <div class="col-md-12 col-12">

                                            <select class="form-control custom-select"
                                                    v-model="activityRestriction.type"
                                                    @change="checkActivityRestrictionOptions(activityRestriction)">
                                                <option value="currency">Currency</option>
                                                <option value="total-discounts">Total discounts</option>
                                            </select>

                                            <select class="form-control custom-select"
                                                    v-model="activityRestriction.conditional"
                                                    v-if="activityRestriction.type == 'currency'">
                                                <option value="" disabled="">Select one</option>
                                                <option value="has-any-of">has any of</option>
                                                <option value="is">is</option>
                                            </select>
                                            <select class="form-control custom-select"
                                                    v-model="activityRestriction.conditional"
                                                    v-else>
                                                <option value="greater-than">greater than</option>
                                                <option value="equals">equals</option>
                                            </select>
                                            <span v-if="activityRestriction.type == 'currency'">
                                        <multiselect
                                                v-model="activityRestriction.values"
                                                tag-placeholder="Add"
                                                select-label="Select"
                                                deselect-label="Remove"
                                                open-direction="bottom"
                                                :id="index"
                                                :options="activityRestriction.options"
                                                :multiple="true"
                                                :taggable="true"
                                                class="responsive"
                                                :class="[activityRestriction.conditional === 'is' ? 'single' : 'multiple', activityRestriction.values.length > 0 ? 'has-tags' : '']"
                                                @tag="addActivityRestrictionsTag">
                                        </multiselect>
                                    </span>
                                            <input class="form-control" v-model="activityRestriction.values" v-else>

                                            <button class="btn btn-default pull-right" type="button"
                                                    @click="deleteActivityRestriction(index)">
                                                <i class="fa fa-trash-o f-s-19"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="well m-t-20 earning-expiration">
                        @if(!$have_limits_permissions)
                            <no-access :loading="loading"
                                       title="{{$limits_upsell->upsell_title}}"
                                       desc="{{$limits_upsell->upsell_text}}"
                                       icon="{{$limits_upsell->upsell_image}}"
                                       plan="{{$limits_upsell->getMinPlan()->name}}"></no-access>
                        @else
                            <div :class="{ 'loading' : loading }" v-cloak>
                                <div class="row section-border-bottom p-b-10 m-b-15">
                                    <div class="col-md-8">
                                        <div class="form-group m-b-0">
                                            <label class="light-font m-b-0">
                                                Earning limits are
                                                <span class="bold"
                                                      v-text="form.earning.limit.status == 0 ? 'Disabled' : 'Enabled'"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <a @click="toogleEarningLimit" v-cloak>
                                        <span v-if="form.earning.limit.status == 0">
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
                                        <div class="input-group">
                                            <input class="form-control" v-model="form.earning.limit.value"
                                                   placeholder="Times">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2">Times</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-4">
                                        <select class="mb-3 form-control custom-select"
                                                v-model="form.earning.limit.period">
                                            <option value="lifetime">Per lifetime</option>
                                            <option value="month">Per month</option>
                                            <option value="week">Per week</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="well m-t-20" v-show="{{ $zapier }}">
                        <div :class="{ 'loading' : loading }" v-cloak>
                            <div class="row section-border-bottom p-b-10 m-b-15">
                                <div class="col-md-8">
                                    <div class="form-group m-b-0">
                                        <label class="bolder f-s-15 m-b-0 m-t-5">
                                            Zapier
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group m-b-15">
                                        <label class="light-font m-b-10">
                                            <span>Zap ID</span>
                                        </label>
                                        <input class="form-control" name="zap"
                                               placeholder="zap key 12345"
                                               v-model="form.zap">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 col-12">
                    <div class="sticky-top">
                        <div class="well ">
                            <span :class="{'loading' : loading}">
                                <h5 class="bold m-b-15">Action Preview</h5>
                                <div class="preview-action">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="left-preview-icon goal-spend"></label>
                                            <div class="right-preview-text">
                                                <span>
                                                    <h5 class="bold m-b-0" v-text="form.program.name"></h5>
                                                    <p>
                                                        <span v-text="rewardText"></span>
                                                    </p>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- </span> -->
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
          action_id: '',
          reward_list: '',
          form: {
            currency: '$',
            point_name: '<?php echo isset($points_settings) ? $points_settings->name : 'Point'; ?>',
            point_namePlural: '<?php echo isset($points_settings) ? $points_settings->plural_name : 'Points'; ?>',
            defaultActionName: 'Goal Spend',
            action_slug: 'goal-spend',
            program: {
              status: 1,
              name: 'Goal Spend',
              rewardText: '',
              rewardTextDefault: 'Spend {amount} get {points} {points-name}',
              emailDefaultText: 'You just earned <strong>{points} {points-name}</strong> at {company-name} <br/>for spending {amount} with us.',
              icon: null,
              icon_name: 'icon-goal-spend',
              action_icon: null
            },
            earning: {
              type: 'variable-amount',
              value: '',
              goal: '',
              goal_type: 'money',
              limit: {
                status: 0,
                value: '',
                period: 'lifetime',
                type: 'times'
              }
            },
            restrictions: {
              status: 0,
              customer: [],
              product: [],
              activity: [],
              ready: false
            },
            iconPreview: '',
            emailNotification: true,
            zap: ''
          },
          restrictions_options: {
            customer_tags: [],
            vip_tiers: [],
            product_ids: [],
            collections: [],
            currencies: [],
            total_discounts: [],
          },
          icon_default_class: 'icon-goal-spend',
          icon_parent_el: 'goal-spend',
          icon_spend: false,
          alert: {
            type: '',
            text: '',
            dismissSecs: 5,
            dismissCountDown: 0
          },
          loading: true,
          rewardEmailTextPreview: '',
          loadingSave: false,
          isModalOpen: false,
          config: {
            svgPath: '/fonts/icons/trumbowyg-icons.svg',
            btns: [['fontsize', 'foreColor', 'bold', 'italic'], ['lineheight', 'horizontalRule', 'link'], ['justifyLeft', 'justifyCenter', 'justifyRight'], ['viewHTML']]
          },
          tags: ['{points}', '{points-name}', '{company-name}', '{amount}'],
          action_tags: ['{points}', '{points-name}', '{amount}'],
        },
        created: function () {
          this.loadingSave = true
          this.getCommonData()
          this.getRestrictionsData()
        },
        methods: {
          getCommonData: function () {
            const vm = this
            axios.get('/getCommonData').then((response) => {
              vm.form.point_name = response.data.point['name']
              vm.form.point_namePlural = response.data.point['plural_name']
              if (response.data.currency) {
                vm.form.currency = response.data.currency.currency_sign
              }
              else {
                vm.form.currency = '$'
              }

            }).catch((error) => {

              this.errors = error.response.data.errors
            })

            this.getDefaultParches()

          },
          hideModal: function () {
            this.isModalOpen = false
          },
          openModal: function () {
            this.generateRewardEmailText()
            this.isModalOpen = true
          },
          bodyPreview () {
            const points_name = this.form.earning.value > 1 ? this.form.point_namePlural : this.form.point_name
            let preview = `
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="img" style="padding: 0 10px; text-align:left;">
                            <div class="text-center m-b-20">
                                <img src="<?php echo isset($company_logo) ? $company_logo : 'https://s3.amazonaws.com/lootly-logos/logo-email.png'; ?>" style="max-height: 50px;">
                            </div>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="text"
                                        style="padding-bottom: 13px; color:#2a2a2a; font-family:Arial,sans-serif; font-size:16px; line-height:26px; text-align:center;">
                                        {customer},
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text pb-25"
                                        style="padding-bottom: 46px; color:#2a2a2a; font-family:Arial,sans-serif; font-size:16px; line-height:26px; text-align:center;">
                                            ${this.rewardEmailTextPreview}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <tr>
                            <td class="img p25-10-0" style="padding: 36px 10px 0; border-top: 1px solid #e8e8e8; text-align:left;">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td class="text-2"
                                            style="padding-bottom: 7px; color:#2a2a2a; font-family:Arial,sans-serif; font-size:20px; line-height:24px; text-align:center; font-weight:bold;">
                                            Next Reward
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-green"
                                            style="padding-bottom: 26px; color:#7ab74d; font-family:Arial,sans-serif; font-size:18px; line-height:22px; text-align:center;">
                                            {currency}{next-reward}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="img-center" style="padding-bottom: 27px; text-align:center;">
                                            {reward-icon}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text pb-30"
                                            style="padding-bottom: 39px; color:#2a2a2a; font-family:Arial,sans-serif; font-size:16px; line-height:26px; text-align:center;">
                                            You need <strong>{need-points} more points</strong><br/>
                                            You have <strong>{point-balance} points</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            {button}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tr>
                </table>
                    
                <table  width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>
                            <div  class="border-top p-l-15 p-r-15">
                                <p class="text-center m-t-10">Powered by Lootly</p>
                            </div>
                            <hr/>
                        </td>
                    </tr>
                </table>
            `
            const button = '<table border="0" cellspacing="0" cellpadding="0" style="margin-bottom:40px"><tr><td class="text-button" style="padding: 14px 37px; border-radius: 5px; color:#ffffff; font-family:Arial,sans-serif; font-size:17px; line-height:21px; text-align:center; font-weight:bold;" bgcolor="#022c82"><a href="#" target="_blank" class="link-white" style="color:#ffffff; text-decoration:none;"><span class="link-white" style="color:#ffffff; text-decoration:none;">'
              + 'Earn More Points' + // btn text
              '</span></a></td></tr></table>'
            const previewText = preview
              .replace(/{company}/g, '{{$company}}' || '{company-name}')
              .replace(/{customer}/g, '{customer}')
              .replace(/{points}/g, this.form.earning.value || '{points}')
              .replace(/{points-name}/g, points_name)
              .replace(/{currency}/g, this.form.currency || '$')
              .replace(/{next-reward}/g, '5 off Discount')
              .replace(/{reward-icon}/g, '<img src="<?php echo url("/images/icons/email-notification/ico_dollar.jpg"); ?>" width="84" height="84" border="0" alt="" />')
              .replace(/{need-points}/g, '500')
              .replace(/{point-balance}/g, '750')
              .replace(/{button}/g, button)
            return previewText
          },
          getDefaultParches: function () {

            axios.get('/points/earning/actions/get/' + this.form.defaultActionName).then((response) => {
              if (response.data.action.merchant_action.length === 0) {
                this.form.restrictions.ready = true
              } else {
                let merchant_action = response.data.action.merchant_action[0]
                this.action_id = merchant_action.id
                this.form.program.name = merchant_action.action_name
                this.form.program.icon_name = merchant_action.action_icon_name
                this.form.program.status = merchant_action.active_flag
                this.form.program.emailDefaultText = response.data.action.merchant_action[0].reward_email_text
                this.form.earning.value = merchant_action.point_value
                this.form.earning.goal = merchant_action.goal
                this.form.earning.type = merchant_action.is_fixed
                this.form.restrictions.status = response.data.action.merchant_action[0].restrictions_enabled
                this.form.earning.limit.status = merchant_action.earning_limit
                this.form.earning.limit.value = response.data.action.merchant_action[0].earning_limit_value
                this.form.earning.limit.period = response.data.action.merchant_action[0].earning_limit_period
                this.form.program.rewardText = response.data.action.merchant_action[0].reward_text
                if ((response.data.action.merchant_action[0].reward_default_text)) {
                  this.form.program.rewardTextDefault = response.data.action.merchant_action[0].reward_default_text
                }
                this.form.program.action_icon = response.data.action.merchant_action[0].action_icon
                this.form.emailNotification = response.data.action.merchant_action[0].send_email_notification === '1' ? true : false
                this.form.zap = response.data.action.merchant_action[0].zap_name

                this.getRestrictions(this.action_id, 'action')
              }

              if (!this.form.iconPreview) {
                showPreviewIcon(this.form.program.action_icon, this.icon_default_class, this.icon_parent_el)

              } else {
                clearPreviewIcon(this.icon_default_class, this.icon_parent_el)
              }
              this.generateRewardText()
              this.getReward()
              this.loading = false
              this.loadingSave = false
            }).catch((error) => {
              console.log(error)
              if (error.response && error.response.data) this.errors = error.response.data.errors
              this.loadingSave = false
            })
          },
          getReward: function () {
            axios.get('/points/spending/reward/list').then((response) => {

              let reward_list = response.data.spendingRewards
              if (reward_list.length != 0) {

                this.reward_list = reward_list.map(function (value, key) {
                  return value.reward_name
                })
              } else {
                this.reward_list = ''
              }

            }).catch((error) => {

            })
          },
          saveAction: function () {
            this.loading = true
            axios.post('/api/merchants/' + Spark.state.currentTeam.id + '/earning/actions/goal-spend', this.form).then((response) => {
              this.alert.dismissCountDown = this.alert.dismissSecs
              this.alert.type = 'success'
              this.alert.text = 'Action saved successfully'
              this.loading = false
            }).catch((error) => {
              this.loading = false
              clearErrors(this.$el)
              showErrors(this.$el, error.response.data.errors)
            })
          },
          toogleProgramStatus: function () {
            if (this.form.program.status == 0) {
              this.form.program.status = 1
            } else {
              this.form.program.status = 0
            }
          },
          toogleEarningRestrictions: function () {
            if (this.form.restrictions.status == 0) {
              this.form.restrictions.status = 1
            } else {
              this.form.restrictions.status = 0
            }
          },
          toogleEarningLimit: function () {
            if (this.form.earning.limit.status == 0) {
              this.form.earning.limit.status = 1
            } else {
              this.form.earning.limit.status = 0
            }
          },
          cleariconImage: function (actionId) {
            clearPreviewIcon(this.icon_default_class, this.icon_parent_el)
            let $el = $('.upload-icon')
            resetForm($el)
            if (this.form.program.action_icon) {

              axios.delete('/points/earning/actions/icon/' + actionId).then((response) => {
                this.form.program.icon = null
                this.form.program.icon_name = this.icon_default_class
                this.form.iconPreview = ''
                this.form.program.action_icon = ''
                this.icon_spend = showPreviewIcon(this.form.program.action_icon, this.icon_default_class, this.icon_parent_el)
              }).catch((error) => {

              })
            } else {
              this.form.program.icon = null
              this.form.program.icon_name = this.icon_default_class
              this.form.program.action_icon = ''
              this.form.iconPreview = ''
              showPreviewIcon(this.form.program.action_icon, this.icon_default_class, this.icon_parent_el)
            }
          },
          iconImageChange: function (evt) {
            var $this = this
            var files = evt.target.files
            if (files.length != 0) {
              var f = files[0]
              this.icon_spend = false
              this.form.program.icon_name = f.name
              var reader = new FileReader()

              reader.onload = (function (theFile) {
                return function (e) {
                  $this.form.iconPreview = e.target.result
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
          changePoint: function () {
            this.generateRewardText()
          },
          changeGoal: function () {
            this.generateRewardText()
          },
          changeRewardText: function () {

            var text = this.form.program.rewardText.split(' ')

            this.form.earning.goal = text[1]
            this.form.earning.value = text[3]

          },
          generateRewardText: function () {
            let point = ''
            if (this.form.earning.value > 1) {
              point = this.form.point_namePlural
            } else {
              point = this.form.point_name
            }
            return this.form.program.rewardText = this.form.program.rewardTextDefault
              .replace(/{points}/g, this.form.earning.value ? this.form.earning.value : '')
              .replace(/{points-name}/g, point)
              .replace(/{amount}/g, this.form.earning.goal ? this.form.currency + this.form.earning.goal : '')
              .replace(/\&nbsp\;/g, ' ')
              .replace(/(<([^>]+)>)/ig, '')
          },
          generateRewardEmailText: function () {
            const points_name = this.form.earning.value > 1 ? this.points : this.point
            if (this.form.program.emailDefaultText) {
              return this.rewardEmailTextPreview = this.form.program.emailDefaultText
                .replace(/{points}/g, this.form.earning.value ? this.form.earning.value : '{points}')
                .replace(/{points-name}/g, points_name ? points_name : '{points-name}')
                .replace(/{company-name}/g, '{{$company}}')
                .replace(/{amount}/g, this.form.earning.goal ? this.form.currency + this.form.earning.goal : '{amount}')
            }
          },
          emailTagClick: function (event) {
            let textarea = $('#emailDefaultText')

            const range = $('#emailDefaultText').trumbowyg('getRange')
            if (range === null) {
              return
            }
            let position = range.startOffset,
              html = range.startContainer.textContent.slice(0, position)
            position += html.length - html.replace(/(<([^>]+)>)/ig, '').length
            const text = range.startContainer.textContent.replace(/\&nbsp;/g, ' ')
            let txt = [
              text.slice(0, position),
              event.target.outerText,
              text.slice(position),
            ].join('')
            let parent = range.startContainer.parentNode
            if (parent.nodeName == 'P') {
              let rows = textarea.trumbowyg('html').replace(/\&nbsp;/g, ' ').split('</p>'),
                selectedIndex = 0
              while (parent.previousSibling) {
                parent = parent.previousSibling
                if (parent.nodeName == 'BR') {
                  continue
                }
                selectedIndex++
              }
              rows[selectedIndex] = '<p>' + txt
              textarea.trumbowyg('html', rows.join('</p>'))
            } else {
              textarea.trumbowyg('html', txt)
            }
            return
          },
          blurEmailField: function (event) {
            $('#emailDefaultText').trumbowyg('saveRange')
          },
          EditorCreated: function () {
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
          },
        },
        computed: {
          rewardText: {
            get: function () { return this.generateRewardText() }
          }
        }
      })
    </script>
@endsection