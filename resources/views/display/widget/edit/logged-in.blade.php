@extends('layouts.app')

@section('title', 'Widget Design')

@section('content')
    <div id="widget-design" class="loader m-t-20 m-b-10" v-cloak>
        <div>
            <b-alert :show="alert.dismissCountDown"
                     dismissible
                     id="top-alert"
                     :variant="alert.type"
                     @dismissed="alert.dismissCountdown=0"
                     @dismiss-count-down="countDownChanged">
                @{{alert.text}}
            </b-alert>

            <div id="">
                <div class="row section-border-bottom m-t-20 p-b-10">
                    <div class="col-md-12 m-b-15">
                        <a href="{{ route('display.widget') }}" class="bold f-s-15 color-blue">
                            <i class="arrow left blue"></i>
                            <span class="m-l-5">Widget Overview</span>
                        </a>
                    </div>
                    <div class="col-md-6 col-6">
                        <h3 class="page-title m-t-0 color-dark">
                            Widget Design
                        </h3>
                    </div>
                    <div class="col-md-6 col-6">
                        <save-button class="text-right" :saving="saving" @event="saveSetting"></save-button>
                    </div>
                </div>

                <div class="row section-border-bottom p-t-25 p-b-25">
                    <div class="col-md-12">
                        <div class="well p-t-15 p-b-15">
                            <div class="row">
                                <div class="col-md-8 col-12">
                                    <p class="m-t-5 m-b-10">Viewing: <span class="bolder">Logged In</span></p>
                                </div>
                                <div class="col-md-4 col-12 text-right">
                                    <a class="btn btn-default bold"
                                       href="{{ route('display.widget.edit-not-logged-in') }}">Switch
                                        to Not Logged In</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row section-border-bottom p-t-25 p-b-25">
                    <div class="col-md-7 col-12">
                        <div class="well">
                            <div :class="{ 'loading' : loading }" v-cloak>
                                <div class="row section-border-bottom p-b-10 m-b-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="bolder f-s-15 m-b-0">Welcome Settings</label>
                                            <p class="m-t-5">This is the first screen members see when opening the
                                                widget.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Welcome text"
                                                   v-model="form.welcome.text" name="welcome.text">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <label class="m-b-5">
                                            Text Position
                                        </label>
                                        <b-form-select v-model="form.welcome.position" name="welcome.position">
                                            <option value="left">Left</option>
                                            <option value="center">Center</option>
                                            <option value="right">Right</option>
                                        </b-form-select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(!$have_customization_permissions)
                            <no-access :loading="loading"
                                class="m-t-15"
                                title="{{$customizations_upsell->upsell_title}}" 
                                desc="{{$customizations_upsell->upsell_text}}" 
                                icon="{{$customizations_upsell->upsell_image}}" 
                                plan="{{$customizations_upsell->getMinPlan()->name}}"></no-access>
                        @else
                            <div class="well m-t-20">
                                <div :class="{ 'loading' : loading }" v-cloak>
                                    <div class="row section-border-bottom p-b-10 m-b-15">
                                        <div class="col-md-12">
                                            <div class="form-group m-b-0">
                                                <label class="bolder f-s-15 m-b-0">Welcome Design</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-b-10">
                                        <label class="m-b-0">
                                            Logo (recommended: 180px by 50px - will auto size to fit)
                                        </label>
                                        <div class="file-drag-drop w-100 m-t-10" v-cloak>
                                            <b-form-file class="upload-icon"
                                                         @change="welcomeIconChange"
                                                         name="welcome.icon"
                                                         accept="image/*">
                                            </b-form-file>

                                            <div class="custom-file-overlay">
                                        <span class="img">
                                            <i class="icon-image-upload"
                                               v-if="!form.welcome.icon && !form.welcome.new_icon"></i>
                                            <img :src="form.welcome.icon" style="max-height:70px;max-width: 100%">
                                        </span>
                                                <h5 class="float f-s-17 bold">
                                            <span class="text"
                                                  v-if="form.welcome.icon || form.welcome.new_icon"
                                                  v-text="form.welcome.icon_name">
                                              </span>
                                                    <span v-else>Drag files to upload</span>
                                                </h5>
                                                <i v-if="form.welcome.icon || form.welcome.new_icon"
                                                   @click="clearIconImage"
                                                   class="fa fa-times color-light-grey pointer"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-t-15 m-b-10">
                                        <label class="m-b-0">
                                            Background (recommended: 360px by 140px - will auto size to fit)
                                        </label>
                                        <div class="file-drag-drop w-100 m-t-10"
                                             v-bind:class="form.welcome.background || form.welcome.new_background ? 'background-file': ''"
                                             v-bind:style="{'background-image': 'url('+form.welcome.background+')'}"
                                             v-cloak>
                                            <b-form-file class="upload-icon"
                                                         @change="welcomeBackgroundChange"
                                                         name="welcome.background"
                                                         accept="image/*">
                                            </b-form-file>

                                            <div class="custom-file-overlay">
                                                <span class="img">
                                                    <i class="icon-image-upload"
                                                       v-if="!form.welcome.background && !form.welcome.new_background"></i>
                                                </span>
                                                <h5 class="float f-s-17 bold">
                                                    <span class="text"
                                                      v-if="form.welcome.background || form.welcome.new_background"
                                                      v-text="form.welcome.background_name"></span>
                                                    <span v-else>Drag files to upload</span>
                                                </h5>
                                                <i v-if="form.welcome.background || form.welcome.new_background"
                                                   @click="clearBackgroundImage('welcome')"
                                                   class="fa fa-times color-light-grey pointer"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-t-15">
                                        <div class="col-md-12">
                                            <label class="m-b-5">
                                                Background Opacity
                                            </label>
                                            <input type="text" class="form-control m-b-5"
                                                   v-model="form.welcome.background_opacity"
                                                   name="welcome.background_opacity" placeholder="Background Opacity"
                                                   @blur="opacityFormat('welcome')">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-5 col-12">
                        <div class="sticky-top">
                            <div class="well">
                                <div class="widget-preview" :style="{fontFamily: branding.font}" :class="{ 'loading' : loading || saving }" v-cloak>
                                    <div class="preview-background"
                                         v-bind:style="{'background-image': 'url('+form.welcome.background+')', opacity: opacityCalc(form.welcome.background_opacity) }"></div>
                                    <button type="button" 
                                            class="close preview-close" 
                                            :style="{color: branding.primaryColor}">×</button>
                                    <div class="background-preview-box">
                                        <div class="m-t-15">
                                            <div class="text-center">
                                        <span v-if="form.welcome.icon">
                                            <img :src="form.welcome.icon" style="max-height: 100px; max-height: 50px;">
                                        </span>
                                                <span style="font-size: 18px; font-weight: bold;" v-else>
                                            <img src="{{ asset('images/logos/logo-placeholder.png') }}"
                                                 style="max-height: 50px;">
                                        </span>
                                            </div>
                                            <p class="w-100 m-t-10"
                                               v-bind:class="'text-'+form.welcome.position"
                                               v-text="form.welcome.text.replace('{customer-name}', 'Joe')"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row section-border-bottom p-t-25 p-b-25">
                    <div class="col-md-7 col-12">
                        <div class="well">
                            <div :class="{ 'loading' : loading }" v-cloak>
                                <div class="row section-border-bottom p-b-10 m-b-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="bolder f-s-15 m-b-0">Points</label>
                                            <p>This is the primary screen your members will see when using the
                                                widget.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Balance Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Balance text"
                                                   name="points.balanceText" v-model="form.points.balanceText">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Available Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Available text"
                                                   name="points.availableText" v-model="form.points.availableText">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Earn Button Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Earn button text"
                                                   name="points.earnButtonText" v-model="form.points.earnButtonText">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Spend Button Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Spend button text"
                                                   name="points.spendButtonText" v-model="form.points.spendButtonText">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Rewards Button Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Rewards button text"
                                                   name="points.rewardsButtonText"
                                                   v-model="form.points.rewardsButtonText">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 col-12">
                        <div class="sticky-top">
                            <div class="well">
                                <div class="widget-preview" :style="{fontFamily: branding.font, color: branding.primaryColor}" :class="{ 'loading' : loading || saving }" v-cloak>
                                    <div class="text-center">
                                        <div class="">
                                            <p v-text="form.points.balanceText.replace(/{points-name}/g, points.plural)"></p>
                                            <h3 class="bold f-s-28">350 @{{points.plural}}</h3>
                                        </div>
                                        <div class="progress m-t-40">
                                            <div class="progress-bar" role="progressbar" style="width: 70%;"
                                                 :style="{ background: branding.primaryColor }"
                                                 aria-valuenow="350" aria-valuemin="0" aria-valuemax="500"></div>
                                        </div>
                                        <p class="m-t-10 f-s-15">
                                            350/500
                                        </p>

                                        <p class="m-t-15 m-b-10">
                                            <span v-text="form.points.availableText"></span> 500 @{{points.plural}}
                                        </p>

                                        <h3 class="bold m-b-30 f-s-25">$10 off discount</h3>

                                        <button class="btn btn-lg btn-block" 
                                        :style="{ background: branding.buttonColor, color: branding.buttonFontColor }">
                                            @{{form.points.earnButtonText.replace(/{points-name}/g, points.plural)}}
                                        </button>

                                        <div class="row m-t-15">
                                            <div class="col-md-6">
                                        <button class="btn btn-lg btn-block" 
                                        :style="{ background: branding.buttonColor, color: branding.buttonFontColor }">
                                                    @{{form.points.spendButtonText.replace(/{points-name}/g, points.plural)}}
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button class="btn btn-default btn-lg btn-block">
                                                    <span v-text="form.points.rewardsButtonText"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row section-border-bottom p-t-25 p-b-25">
                    <div class="col-md-7 col-12">
                        <div class="well">
                            <div :class="{ 'loading' : loading }" v-cloak>
                                <div class="row section-border-bottom p-b-10 m-b-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="bolder f-s-15 m-b-0">Points - Rewards & Earn Points</label>
                                            <p class="m-t-5">This screen is shown to the user after they click the "Spend Points" or "Earn More Points" button.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Redeem Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Redeem text"
                                                   name="points.redeemTabText" v-model="form.points.redeemTabText">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Rewards Button Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Button text"
                                                   name="points.rewardsTabButton" v-model="form.points.rewardsTabButton">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Earn Points Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Earn text"
                                                   name="points.earnTabText" v-model="form.points.earnTabText">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Earn Points Button Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Button text"
                                                   name="points.earnTabButton" v-model="form.points.earnTabButton">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Points Needed Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Points Needed Text"
                                                   name="points.pointsNeededText" v-model="form.points.pointsNeededText">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Points Activity Title</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Points Activity Title"
                                                   name="points.pointsActivityTitle" v-model="form.points.pointsActivityTitle">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 col-12">
                        <div class="sticky-top preview-box">
                            <div class="well">
                                <div class="widget-preview" 
                                     :style="{fontFamily: branding.font}" 
                                     :class="{ 'loading' : loading || saving }" v-cloak>
                                    <div class="text-center">
                                        <p v-text="form.points.balanceText.replace(/{points-name}/g, points.plural)"></p>
                                        <h3 class="bold f-s-28">350 @{{points.plural}}</h3>
                                    </div>
                                    <p class="text-left m-t-25 m-b-20">
                                        <span v-if="tabIndex == 1">
                                            @{{ form.points.redeemTabText.replace(/{points-name}/g, points.plural) }}
                                        </span>
                                        <span v-else>
                                            @{{ form.points.earnTabText.replace(/{points-name}/g, points.plural) }}
                                        </span>
                                    </p>
                                    <div class="row m-b-15">
                                        <div class="col-6">
                                            <div class="tab-link" :class="{active: tabIndex == 1}" @click="tabIndex = 1">
                                                <span>
                                                    @{{ form.points.rewardsTabButton.replace(/{points-name}/g, points.plural) }}
                                                </span>
                                            </div>
                                        </div> 
                                        <div class="col-6">
                                            <div class="tab-link" :class="{active: tabIndex == 2}" @click="tabIndex = 2">
                                                <span>
                                                    @{{ form.points.earnTabButton.replace(/{points-name}/g, points.plural) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="launcher-reward-action">
                                            <i class="icon-coin f-s-28"></i> 
                                            <div class="pull-left m-t-5 m-l-15">
                                                <h5 class="bold f-s-15">
                                                    $20 off discount
                                                </h5>
                                                <p>200 Points</p>
                                            </div>
                                            <p class="f-s-14 ml-auto">
                                                @{{ form.points.pointsNeededText.replace(/{#}/g, 200).replace(/{points-name}/g, points.plural) }}
                                            </p>
                                        </div>                                        
                                    </div>
                                    <p class="mt-4 bold">@{{ form.points.pointsActivityTitle.replace(/{points-name}/g, points.plural) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row section-border-bottom p-t-25 p-b-25">
                    <div class="col-md-7 col-12">
                        <div class="well">
                            <div :class="{ 'loading' : loading }" v-cloak>
                                <div class="row section-border-bottom p-b-10 m-b-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="bolder f-s-15 m-b-0">My Rewards</label>
                                            <p class="m-t-5">This screen is shown when a user clicks on "My Rewards" to see all of their available rewards.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>My Rewards Title</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="My Rewards Title"
                                                   name="points.rewardsTitle" v-model="form.points.rewardsTitle">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Rewards Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Rewards text"
                                                   name="points.rewardsText" v-model="form.points.rewardsText">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>No Rewards Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="No Rewards text"
                                                   name="points.noRewardsText" v-model="form.points.noRewardsText">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Button Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Button Text"
                                                   name="points.rewardViewButton" v-model="form.points.rewardViewButton">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 col-12">
                        <div class="sticky-top preview-box">
                            <div class="well">
                                <div class="widget-preview" 
                                     :style="{fontFamily: branding.font}" :class="{ 'loading' : loading || saving }" v-cloak>
                                    <div class="">
                                        <div class="overflow border-bottom m-b-10">
                                            <p class="bold pull-left m-b-10">
                                                @{{form.points.rewardsTitle}}
                                            </p>
                                        </div>
                                        <div class="m-b-10">
                                            <p>
                                                <span v-text="form.points.rewardsText"></span>
                                            </p>
                                        </div>

                                        <div class="launcher-reward-action">
                                            <i class="icon-coin f-s-28"></i>
                                            <div class="pull-left m-t-5 m-l-15">
                                                <h5 class="bold f-s-15">
                                                    $20 off discount
                                                </h5>
                                                <p>15 @{{points.plural}}</p>
                                            </div>
                                            <button class="btn" 
                                                    :style="{ background: branding.buttonColor, color: branding.buttonFontColor }">
                                                @{{ form.points.rewardViewButton }}
                                            </button>
                                        </div>
                                        <div class="launcher-reward-action">
                                            <i class="icon-percentage f-s-28"></i>
                                            <div class="pull-left m-t-5 m-l-15">
                                                <h5 class="bold f-s-15">
                                                    10% off discount 
                                                </h5>
                                                <p>10 @{{points.plural}}</p>
                                            </div>
                                            <button class="btn" 
                                                    :style="{ background: branding.buttonColor, color: branding.buttonFontColor }">
                                                @{{ form.points.rewardViewButton }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row section-border-bottom p-t-25 p-b-25">
                    <div class="col-md-7 col-12">
                        <div class="well">
                            <div :class="{ 'loading' : loading }" v-cloak>
                                <div class="row section-border-bottom p-b-10 m-b-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="bolder f-s-15 m-b-0">VIP</label>
                                            <p class="m-t-5">This screen will display as users scroll down from the
                                                Points section.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Button Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Button text"
                                                   name="vip.buttonText" v-model="form.vip.buttonText">
                                        </div>
                                    </div>
                                </div>

                                @if(!$have_customization_permissions)
                                    <no-access :loading="loading"
                                        class="m-t-15"
                                        title="{{$customizations_upsell->upsell_title}}" 
                                        desc="{{$customizations_upsell->upsell_text}}" 
                                        icon="{{$customizations_upsell->upsell_image}}" 
                                        plan="{{$customizations_upsell->getMinPlan()->name}}">
                                    </no-access>
                                @else
                                    <span>
                                        <div class="m-t-15 m-b-10">
                                            <label class="m-b-0">
                                                Background (recommended: 360px by 300px - will auto size to fit)
                                            </label>
                                            <div class="file-drag-drop w-100 m-t-10"
                                                 v-bind:class="form.vip.background || form.vip.new_background ? 'background-file': ''"
                                                 v-bind:style="{'background-image': 'url('+form.vip.background+')'}"
                                                 v-cloak>
                                                <b-form-file class="upload-icon"
                                                             @change="vipBackgroundChange"
                                                             name="vip.background"
                                                             accept="image/*">
                                                </b-form-file>

                                                <div class="custom-file-overlay">
                                            <span class="img">
                                                <i class="icon-image-upload"
                                                   v-if="!form.vip.background && !form.vip.new_background"></i>
                                            </span>
                                                    <h5 class="float f-s-17 bold">
                                                <span class="text"
                                                      v-if="form.vip.background || form.vip.new_background"
                                                      v-text="form.vip.background_name">
                                                  </span>
                                                        <span v-else>Drag files to upload</span>
                                                    </h5>
                                                    <i v-if="form.vip.background || form.vip.new_background"
                                                       @click="clearBackgroundImage('vip')"
                                                       class="fa fa-times color-light-grey pointer"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row m-t-15">
                                            <div class="col-md-12">
                                                <label class="m-b-5">
                                                    Background Opacity
                                                </label>
                                                <input type="text" class="form-control m-b-5" name="vip.background_opacity"
                                                       v-model="form.vip.background_opacity" placeholder="Background Opacity"
                                                       @blur="opacityFormat('vip')">
                                            </div>
                                        </div>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 col-12">
                        <div class="sticky-top preview-box">
                            <div class="well">
                                <div class="widget-preview" :style="{fontFamily: branding.font}" :class="{ 'loading' : loading || saving }" v-cloak>
                                    <div class="preview-background"
                                         v-bind:style="{'background-image': 'url('+form.vip.background+')', opacity: opacityCalc(form.vip.background_opacity) }"></div>

                                    <div class="background-preview-box">
                                        <div class="overflow border-bottom m-b-15">
                                            <p class="bold pull-left m-b-10">VIP Tiers</p>
                                        </div>
                                        <div class="overflow">
                                            <i style="font-size: 34px;margin-right: 5px;" class="icon-trophy m-t-15 pull-left"></i>
                                            <div class="pull-left m-l-20">
                                                <p class="bolder m-b-0">Bronze</p>
                                                <p>
                                                    $500 spent in the last 365 days <br>
                                                    Earn 1.5 points per $1 spent
                                                </p>
                                            </div>
                                        </div>
                                        <div class="progress m-t-20">
                                            <div class="progress-bar" role="progressbar" style="width: 1%;" :style="{ background: branding.primaryColor }" aria-valuenow="25" aria-valuemin="0" aria-valuemax="500"></div>
                                        </div>
                                        <p class="text-center m-t-10 m-b-20 f-s-15">
                                            $25 / $500
                                        </p>
                                        <button class="btn btn-lg btn-block" 
                                        :style="{ background: branding.buttonColor, color: branding.buttonFontColor }">
                                            <span v-text="form.vip.buttonText"></span>
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row section-border-bottom p-t-25 p-b-25">
                    <div class="col-md-7 col-12">
                        <div class="well">
                            <div :class="{ 'loading' : loading }" v-cloak>
                                <div class="row section-border-bottom p-b-10 m-b-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="bolder f-s-15 m-b-0">Referrals Settings</label>
                                            <p class="m-t-5">This screen displays at the very bottom of the widget.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Main Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Main text"
                                                   name="referral.mainText" v-model="form.referral.mainText">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <label class="m-b-5">
                                            Referral Receiver Text
                                        </label>
                                        <input type="text" class="form-control m-b-5"
                                               placeholder="Referral receiver text"
                                               name="referral.receiverText" v-model="form.referral.receiverText">
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <label class="m-b-5">
                                            Referral Sender Text
                                        </label>
                                        <input type="text" class="form-control m-b-5" placeholder="Referral sender text"
                                               name="referral.senderText" v-model="form.referral.senderText">
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <label class="m-b-5">
                                            Copy Button
                                        </label>
                                        <input type="text" class="form-control m-b-5" placeholder="Copy button text"
                                               name="referral.copyButton" v-model="form.referral.copyButton">
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <label class="m-b-5">
                                            Link Text
                                        </label>
                                        <input type="text" class="form-control m-b-5" placeholder="Link text"
                                               name="referral.LinkText" v-model="form.referral.LinkText">
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(!$have_referral_customization_permissions)
                            <no-access :loading="loading"
                                title="{{$referral_customizations_upsell->upsell_title}}" 
                                desc="{{$referral_customizations_upsell->upsell_text}}" 
                                icon="{{$referral_customizations_upsell->upsell_image}}" 
                                plan="{{$referral_customizations_upsell->getMinPlan()->name}}"></no-access>
                        @else
                            <div class="well m-t-20">
                                <div :class="{ 'loading' : loading }" v-cloak>
                                    <div class="row section-border-bottom p-b-10 m-b-15">
                                        <div class="col-md-12">
                                            <div class="form-group m-b-0">
                                                <label class="bolder f-s-15 m-b-0">Referrals Design</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-t-15 m-b-10">
                                        <label class="m-b-0">
                                            Background (recommended: 360px by 420px - will auto size to fit)
                                        </label>
                                        <div class="file-drag-drop w-100 m-t-10"
                                             v-bind:class="form.referral.background || form.referral.new_background ? 'background-file': ''"
                                             v-bind:style="{'background-image': 'url('+form.referral.background+')'}"
                                             v-cloak>
                                            <b-form-file class="upload-icon"
                                                         @change="referralBackgroundChange"
                                                         name="referral.background"
                                                         accept="image/*">
                                            </b-form-file>

                                            <div class="custom-file-overlay">
                                        <span class="img">
                                            <i class="icon-image-upload"
                                               v-if="!form.referral.background && !form.referral.new_background"></i>
                                        </span>
                                                <h5 class="float f-s-17 bold">
                                            <span class="text"
                                                  v-if="form.referral.background || form.referral.new_background"
                                                  v-text="form.referral.background_name">
                                              </span>
                                                    <span v-else>Drag files to upload</span>
                                                </h5>
                                                <i v-if="form.referral.background || form.referral.new_background"
                                                   @click="clearBackgroundImage('referral')"
                                                   class="fa fa-times color-light-grey pointer"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-t-15">
                                        <div class="col-md-12">
                                            <label class="m-b-5">
                                                Background Opacity
                                            </label>
                                            <input type="text" class="form-control m-b-5" name="referral.background_opacity"
                                                   v-model="form.referral.background_opacity"
                                                   placeholder="Background Opacity"
                                                   @blur="opacityFormat('referral')">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-5 col-12">
                        <div class="sticky-top preview-box">
                            <div class="well">
                                <div class="widget-preview" :style="{fontFamily: branding.font}" :class="{ 'loading' : loading || saving }" v-cloak>
                                    <div class="preview-background"
                                         v-bind:style="{'background-image': 'url('+form.referral.background+')', opacity: opacityCalc(form.referral.background_opacity) }"></div>
                                    <div class="background-preview-box">
                                        <div class="overflow border-bottom m-b-10">
                                            <p class="bold pull-left m-b-10">Referrals</p>
                                        </div>
                                        <div class="m-b-10">
                                            <p>
                                                <span v-text="form.referral.mainText"></span>
                                            </p>
                                        </div>
                                        <div class="launcher-reward-action">
                                            <i class="icon-percentage f-s-28"></i>
                                            <div class="pull-left m-t-5 m-l-15">
                                                <h5 class="bold f-s-15">
                                                    <span v-text="form.referral.receiverText"></span>
                                                </h5>
                                                <p>10% off coupon</p>
                                            </div>
                                        </div>

                                        <div class="launcher-reward-action">
                                            <i class="icon-coin f-s-28"></i>
                                            <div class="pull-left m-t-5 m-l-15">
                                                <h5 class="bold f-s-15">
                                                    <span v-text="form.referral.senderText"></span>
                                                </h5>
                                                <p>$10 off coupon</p>
                                            </div>
                                        </div>

                                        <div class="referrals-field-section">
                                            <input class="form-control" value="http://ref.lootly.io/ASGREG3333" id="referrals-field">
                                            <div>
                                                <button class="btn" 
                                                        style="background: rgb(43, 105, 209); color: rgb(255, 255, 255);" 
                                                        :style="{ background: branding.buttonColor, color: branding.buttonFontColor }"
                                                        @click="copyClipboard">
                                                        <span v-text="form.referral.copyButton"></span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row m-t-25">
                                            <div class="col-12 text-center">
                                                <a href="javascript:void(0)"
                                                   class="social-share email inline-block"></a>
                                                <a href="javascript:void(0)"
                                                   class="social-share facebook inline-block"></a>
                                                <a href="javascript:void(0)"
                                                   class="social-share twitter inline-block"></a>
                                            </div>
                                        </div>
                                        <div class="row m-t-10">
                                            <div class="col-12 text-center">
                                                <a class="bold" href="javascript:void(0)"
                                                   :style="{color: branding.linkColor}" 
                                                   v-text="form.referral.LinkText"></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row section-border-bottom p-t-25 p-b-25">
                    <div class="col-md-7 col-12">
                        <div class="well">
                            <div :class="{ 'loading' : loading }" v-cloak>
                                <div class="row section-border-bottom p-b-10 m-b-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="bolder f-s-15 m-b-0">How it Works Settings</label>
                                            <p class="m-t-5">This screen displays after members click the “How it works” link under Referrals.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Title</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   placeholder="Section title"
                                                   name="howItWorks.title" v-model="form.howItWorks.title">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <label class="m-b-5">
                                            Text
                                        </label>
                                        <textarea class="form-control m-b-5"
                                                  style="min-height: 80px" 
                                                  placeholder="How it works text"
                                                  name="howItWorks.text" v-model="form.howItWorks.text"></textarea>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <label class="m-b-5">
                                            Text Position
                                        </label>
                                        <b-form-select v-model="form.howItWorks.position" name="howItWorks.position">
                                            <option value="left">Left</option>
                                            <option value="center">Center</option>
                                            <option value="right">Right</option>
                                        </b-form-select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 col-12">
                        <div class="sticky-top">
                            <div class="well">
                                <div class="widget-preview" :style="{fontFamily: branding.font}" :class="{ 'loading' : loading || saving }" v-cloak>
                                    <div style="">
                                        <div class="overflow border-bottom m-b-15">
                                            <h5 class="bold pull-left m-b-10 f-s-16">
                                                @{{form.howItWorks.title}}
                                            </h5>
                                        </div>
                                        <div>
                                            <p :class="'text-'+form.howItWorks.position">
                                                @{{ form.howItWorks.text }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row section-border-bottom p-t-25 p-b-25">
                    <div class="col-md-7 col-12">
                        <div class="well">
                            <div :class="{ 'loading' : loading }" v-cloak>
                                <div class="row section-border-bottom p-b-10 m-b-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="bolder f-s-15 m-b-0">Coupon Code Area</label>
                                            <p class="m-t-5">This screen displays the spending reward coupon code to a customer after they redeem their points.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Title</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   placeholder="Title"
                                                   name="coupon.title" v-model="form.coupon.title">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <label class="m-b-5">Copy Button</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   placeholder="Copy Button"
                                                   name="coupon.copy_button" v-model="form.coupon.copy_button">
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <label class="m-b-5">Body Text</label>
                                        <textarea class="form-control m-b-5" style="min-height: 80px" 
                                                  placeholder="Body Text"
                                                  name="coupon.body_text" v-model="form.coupon.body_text"></textarea>
                                    </div>
                                </div> 
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <label class="m-b-5">Button Text</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   placeholder="Button text"
                                                   name="coupon.button_text" v-model="form.coupon.button_text">
                                    </div>
                                </div>                                                               
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 col-12">
                        <div class="sticky-top">
                            <div class="well">
                                <div class="widget-preview" 
                                     :style="{fontFamily: branding.font}" :class="{ 'loading' : loading || saving }">
                                    <div class="widget-block get-coupon py-0">
                                        <div class="get-coupon-head text-center">
                                            <p class="title"><b>@{{ form.coupon.title }}</b></p>
                                            <i class="icon-coin" :style="{color: branding.buttonColor}"></i> 
                                            <p class="discount-text">$20 off Discount</p>
                                        </div>
                                        <div class="row">
                                            <div class="col-8 inline-input">
                                                <input id="coupon-field" class="form-control" value="RH11201987">
                                            </div>
                                            <div class="col-4">
                                                <button class="btn btn-block inline-input-btn"
                                                        style="height: 37px !important; padding: 0 !important;" 
                                                        :style="{ background: branding.buttonColor, color: branding.buttonFontColor }">
                                                    @{{ form.coupon.copy_button }}
                                                </button>
                                            </div>
                                        </div>
                                        <p class="copy-desc mb-4">
                                            @{{ form.coupon.body_text }}
                                        </p>
                                        <a class="btn btn-block" :style="{ background: branding.buttonColor, color: branding.buttonFontColor }">
                                            @{{ form.coupon.button_text }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <b-alert v-cloak
                         :show="alert.dismissCountDown"
                         dismissible
                         id="bottom-alert"
                         class="m-t-15 m-b-0"
                         :variant="alert.type"
                         @dismissed="alert.dismissCountdown=0"
                         @dismiss-count-down="countDownChanged">
                    @{{alert.text}}
                </b-alert>

                <div class="row p-t-25 p-b-15">
                    <div class="col-md-12 col-12">
                        <save-button class="text-right" :saving="saving" @event="saveSetting"></save-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <link rel="stylesheet" type="text/css" href="{{ mix('css/_widget.css') }}">
    <script>
      var page = new Vue({
        el: '#widget-design',
        data: {
          tabIndex: 1,
          points: {
            name: @if($points_settings)
            '{{$points_settings->name}}',
            @else 'Point',
            @endif

            plural: @if($points_settings)
            '{{$points_settings->plural_name}}',
            @else 'Points',
            @endif
          },
          form: {
            fontFamily: 'lato',
            primaryColor: '#4969ad',
            welcome: {
              text: 'Welcome back {customer-name}',
              position: 'center',
              background_opacity: '100%',
              icon: '',
              new_icon: '',
              icon_name: '',
              background: '',
              new_background: '',
              background_name: ''
            },
            points: {
              balanceText: 'Your {points-name} balance',
              availableText: 'Available at',
              earnButtonText: 'Earn more {points-name}',
              spendButtonText: 'Spend {points-name}',
              rewardsButtonText: 'My Rewards',
              redeemTabText: 'Redeem your {points-name} for great discounts',
              rewardsTabButton: 'Rewards',
              earnTabText: 'Earn {points-name} for completing actions, and turn your points into rewards.',
              earnTabButton: 'Earn {points-name}',
              pointsNeededText: 'You need {#} more {points-name}',
              pointsActivityTitle: 'My {points-name} Activity',
              rewardsTitle: 'My Rewards',
              rewardsText: ' All of your earned rewards are below.',
              noRewardsText: 'You don\'t have any earned rewards yet.',
              rewardViewButton: 'View'
            },
            vip: {
              buttonText: 'See Benefits',
              background_opacity: '100%',
              background: '',
              new_background: '',
              background_name: ''
            },
            referral: {
              mainText: 'Tell your friends about us and earn rewards',
              receiverText: 'They will receive',
              senderText: 'You will receive',
              copyButton: 'Copy',
              LinkText: 'How our referral program works',
              background_opacity: '100%',
              background: '',
              new_background: '',
              background_name: '',
            },
            howItWorks: {
                title: 'How It Works',
                text: 'Your referral link gives your friend access to a coupon to immediately save on their first purchase. When they make a purchase using your code, you will be rewarded as well.',
                position: 'left'
            },
            coupon: {
                title: 'Congratulations!',
                copy_button: 'Copy',
                body_text: 'Copy this coupon code and use it on your next purchase with us. The code has also been sent to your email.',
                button_text: 'Continue Shopping'
            }            
          },
          branding: { // Call from DB
            primaryColor: '#2b69d1',
            secondaryColor: '#3d3d3d',
            headerBackground: '#2b69d1',
            headerBackgroundFontColor: '#FFFFFF',
            buttonColor: '#2b69d1',
            buttonFontColor: '#FFFFFF',
            linkColor: '#2b69d1',
            tabColor: '#2b69d1',
            tabFontColor: '#FFFFFF',
            font: 'lato',
          },
          alert: {
            type: '',
            text: '',
            dismissSecs: 5,
            dismissCountDown: 0
          },
          saving: false,
          loading: false
        },
        created: function () {
          this.getData()
        },
        methods: {
          getData: function () {
            this.loading = true
            let that = this
            axios.get('/display/widget/widget/logged/get').then((response) => {

              if (response.data.widget_settings) {
                let widget_settings = response.data.widget_settings
                if (widget_settings.widget_logged_welcome_text) that.form.welcome.text = widget_settings.widget_logged_welcome_text
                if (widget_settings.widget_logged_welcome_position) that.form.welcome.position = widget_settings.widget_logged_welcome_position
                if (widget_settings.widget_logged_welcome_background) that.form.welcome.background = widget_settings.widget_logged_welcome_background
                if (widget_settings.widget_logged_welcome_background_name) that.form.welcome.background_name = widget_settings.widget_logged_welcome_background_name
                if (widget_settings.widget_logged_welcome_background_opacity) that.form.welcome.background_opacity = widget_settings.widget_logged_welcome_background_opacity
                if (widget_settings.widget_logged_welcome_icon) that.form.welcome.icon = widget_settings.widget_logged_welcome_icon
                if (widget_settings.widget_logged_welcome_icon_name) that.form.welcome.icon_name = widget_settings.widget_logged_welcome_icon_name

                if (widget_settings.widget_logged_points_balance_text) that.form.points.balanceText = widget_settings.widget_logged_points_balance_text
                if (widget_settings.widget_logged_points_available_text) that.form.points.availableText = widget_settings.widget_logged_points_available_text
                if (widget_settings.widget_logged_points_earn_button_text) that.form.points.earnButtonText = widget_settings.widget_logged_points_earn_button_text
                if (widget_settings.widget_logged_points_spend_button_text) that.form.points.spendButtonText = widget_settings.widget_logged_points_spend_button_text
                if (widget_settings.widget_logged_points_rewards_button_text) that.form.points.rewardsButtonText = widget_settings.widget_logged_points_rewards_button_text

                if (widget_settings.widget_logged_points_reedem_tab_text) that.form.points.redeemTabText = widget_settings.widget_logged_points_reedem_tab_text
                if (widget_settings.widget_logged_points_rewards_tab_button) that.form.points.rewardsTabButton = widget_settings.widget_logged_points_rewards_tab_button
                if (widget_settings.widget_logged_points_earn_tab_text) that.form.points.earnTabText = widget_settings.widget_logged_points_earn_tab_text
                if (widget_settings.widget_logged_points_earn_tab_button) that.form.points.earnTabButton = widget_settings.widget_logged_points_earn_tab_button

                if (widget_settings.widget_logged_points_needed_text) that.form.points.pointsNeededText = widget_settings.widget_logged_points_needed_text
                if (widget_settings.widget_logged_points_activity_title) that.form.points.pointsActivityTitle = widget_settings.widget_logged_points_activity_title

                if (widget_settings.widget_logged_my_rewards_title) that.form.points.rewardsTitle = widget_settings.widget_logged_my_rewards_title
                if (widget_settings.widget_logged_my_rewards_text) that.form.points.rewardsText = widget_settings.widget_logged_my_rewards_text
                if (widget_settings.widget_logged_no_rewards_text) that.form.points.noRewardsText = widget_settings.widget_logged_no_rewards_text
                if (widget_settings.widget_logged_reward_view_button) that.form.points.rewardViewButton = widget_settings.widget_logged_reward_view_button

                if (widget_settings.widget_logged_vip_button_text) that.form.vip.buttonText = widget_settings.widget_logged_vip_button_text
                if (widget_settings.widget_logged_vip_background) that.form.vip.background = widget_settings.widget_logged_vip_background
                if (widget_settings.widget_logged_vip_background_name) that.form.vip.background_name = widget_settings.widget_logged_vip_background_name
                if (widget_settings.widget_logged_vip_background_opacity) that.form.vip.background_opacity = widget_settings.widget_logged_vip_background_opacity

                if (widget_settings.widget_logged_referral_main_text) that.form.referral.mainText = widget_settings.widget_logged_referral_main_text
                if (widget_settings.widget_logged_referral_receiver_text) that.form.referral.receiverText = widget_settings.widget_logged_referral_receiver_text
                if (widget_settings.widget_logged_referral_sender_text) that.form.referral.senderText = widget_settings.widget_logged_referral_sender_text
                if (widget_settings.widget_logged_referral_copy_button) that.form.referral.copyButton = widget_settings.widget_logged_referral_copy_button
                if (widget_settings.widget_logged_referral_link_text) that.form.referral.LinkText = widget_settings.widget_logged_referral_link_text
                if (widget_settings.widget_logged_referral_background) that.form.referral.background = widget_settings.widget_logged_referral_background
                if (widget_settings.widget_logged_referral_background_name) that.form.referral.background_name = widget_settings.widget_logged_referral_background_name
                if (widget_settings.widget_logged_referral_background_opacity) that.form.referral.background_opacity = widget_settings.widget_logged_referral_background_opacity

                if (widget_settings.widget_how_it_works_title) that.form.howItWorks.title = widget_settings.widget_how_it_works_title
                if (widget_settings.widget_how_it_works_text) that.form.howItWorks.text = widget_settings.widget_how_it_works_text
                if (widget_settings.widget_how_it_works_position) that.form.howItWorks.position = widget_settings.widget_how_it_works_position

                if (widget_settings.widget_logged_coupon_title) that.form.coupon.title = widget_settings.widget_logged_coupon_title
                if (widget_settings.widget_logged_coupon_copy_button) that.form.coupon.copy_button = widget_settings.widget_logged_coupon_copy_button
                if (widget_settings.widget_logged_coupon_body_text) that.form.coupon.body_text = widget_settings.widget_logged_coupon_body_text
                if (widget_settings.widget_logged_coupon_button_text) that.form.coupon.button_text = widget_settings.widget_logged_coupon_button_text

                // Get Branding Settings
                if (widget_settings.brand_primary_color) that.branding.primaryColor = widget_settings.brand_primary_color
                if (widget_settings.brand_secondary_color) that.branding.secondaryColor = widget_settings.brand_secondary_color
                if (widget_settings.brand_header_bg) that.branding.headerBackground = widget_settings.brand_header_bg;
                if (widget_settings.brand_header_bg_font_color ) that.branding.headerBackgroundFontColor = widget_settings.brand_header_bg_font_color;
                if (widget_settings.brand_button_color) that.branding.buttonColor = widget_settings.brand_button_color;
                if (widget_settings.brand_button_font_color) that.branding.buttonFontColor = widget_settings.brand_button_font_color;
                if (widget_settings.tab_bg_color) that.branding.tabColor = widget_settings.tab_bg_color;
                if (widget_settings.tab_font_color) that.branding.tabFontColor = widget_settings.tab_font_color;
                if (widget_settings.brand_button_font_color) that.branding.buttonFontColor = widget_settings.brand_button_font_color;
                if (widget_settings.brand_link_color) that.branding.linkColor = widget_settings.brand_link_color
                if (widget_settings.brand_font) that.branding.font = widget_settings.brand_font

              }

              that.loading = false
              /*
              if (!this.form.iconPreview) {
                showPreviewIcon(this.form.program.reward_icon, this.icon_default_class, this.icon_parent_el);
              } else {
                clearPreviewIcon(this.icon_default_class, this.icon_parent_el);
              }*/

            }).catch((error) => {
              that.loading = false
              this.errors = error.response.data.errors
            })
          },
          saveSetting () {
            const that = this
            if (!that.saving) {
              that.saving = true

              let formData = JSON.parse(JSON.stringify(this.form))
              if (formData.welcome.new_icon.length) formData.welcome.icon = ''
              if (formData.welcome.new_background.length) formData.welcome.background = ''
              if (formData.vip.new_background.length) formData.vip.background = ''
              if (formData.referral.new_background.length) formData.referral.background = ''

              axios.post('/display/widget/widget/logged/store', formData).then((response) => {
                that.saving = false
                this.alert.dismissCountDown = this.alert.dismissSecs
                if (response.data.status == 404) {
                  this.alert.type = 'danger'
                  this.alert.text = response.data.message
                } else {
                  this.alert.type = 'success'
                  this.alert.text = response.data.message
                  if (response.data.widget_settings) {
                    let widget_settings = response.data.widget_settings
                    that.form.welcome.text = widget_settings.widget_logged_welcome_text
                    that.form.welcome.position = widget_settings.widget_logged_welcome_position
                    that.form.welcome.background = widget_settings.widget_logged_welcome_background
                    that.form.welcome.background_name = widget_settings.widget_logged_welcome_background_name
                    that.form.welcome.background_opacity = widget_settings.widget_logged_welcome_background_opacity
                    that.form.welcome.icon = widget_settings.widget_logged_welcome_icon
                    that.form.welcome.icon_name = widget_settings.widget_logged_welcome_icon_name
                    that.form.welcome.new_background = ''
                    that.form.welcome.new_icon = ''

                    that.form.points.balanceText = widget_settings.widget_logged_points_balance_text
                    that.form.points.availableText = widget_settings.widget_logged_points_available_text
                    that.form.points.earnButtonText = widget_settings.widget_logged_points_earn_button_text
                    that.form.points.spendButtonText = widget_settings.widget_logged_points_spend_button_text
                    that.form.points.rewardsButtonText = widget_settings.widget_logged_points_rewards_button_text

                    that.form.points.redeemTabText = widget_settings.widget_logged_points_reedem_tab_text
                    that.form.points.rewardsTabButton = widget_settings.widget_logged_points_rewards_tab_button
                    that.form.points.earnTabText = widget_settings.widget_logged_points_earn_tab_text
                    that.form.points.earnTabButton = widget_settings.widget_logged_points_earn_tab_button

                    that.form.points.pointsNeededText = widget_settings.widget_logged_points_needed_text
                    that.form.points.pointsActivityTitle = widget_settings.widget_logged_points_activity_title

                    that.form.points.rewardsTitle = widget_settings.widget_logged_my_rewards_title
                    that.form.points.rewardsText = widget_settings.widget_logged_my_rewards_text
                    that.form.points.noRewardsText = widget_settings.widget_logged_no_rewards_text
                    that.form.points.rewardViewButton = widget_settings.widget_logged_reward_view_button

                    that.form.vip.buttonText = widget_settings.widget_logged_vip_button_text
                    that.form.vip.background = widget_settings.widget_logged_vip_background
                    that.form.vip.background_name = widget_settings.widget_logged_vip_background_name
                    that.form.vip.background_opacity = widget_settings.widget_logged_vip_background_opacity
                    that.form.vip.new_background = ''

                    that.form.referral.mainText = widget_settings.widget_logged_referral_main_text
                    that.form.referral.receiverText = widget_settings.widget_logged_referral_receiver_text
                    that.form.referral.senderText = widget_settings.widget_logged_referral_sender_text
                    that.form.referral.copyButton = widget_settings.widget_logged_referral_copy_button
                    that.form.referral.LinkText = widget_settings.widget_logged_referral_link_text
                    that.form.referral.background = widget_settings.widget_logged_referral_background
                    that.form.referral.background_name = widget_settings.widget_logged_referral_background_name
                    that.form.referral.background_opacity = widget_settings.widget_logged_referral_background_opacity
                    that.form.referral.new_background = ''

                    that.form.howItWorks.title = widget_settings.widget_how_it_works_title
                    that.form.howItWorks.text = widget_settings.widget_how_it_works_text
                    that.form.howItWorks.position = widget_settings.widget_how_it_works_position

                    that.form.coupon.title = widget_settings.widget_logged_coupon_title
                    that.form.coupon.copy_button = widget_settings.widget_logged_coupon_copy_button
                    that.form.coupon.body_text = widget_settings.widget_logged_coupon_body_text
                    that.form.coupon.button_text = widget_settings.widget_logged_coupon_button_text

                  }
                }
              }).catch((error) => {
                that.saving = false
                clearErrors(this.$el)
                console.log(error.response.data.errors)
                showErrors(this.$el, error.response.data.errors)
                that.alert.dismissCountDown = this.alert.dismissSecs
                that.alert.type = 'danger'
                that.alert.text = error.response.data.message
              }).then(() => {
                //always

                //get scroll position
                let scrollPos = window.scrollY || window.scrollTop || document.getElementsByTagName('html')[0].scrollTop
                let body = document.body,
                  html = document.documentElement
                let height = Math.max(body.scrollHeight, body.offsetHeight,
                  html.clientHeight, html.scrollHeight, html.offsetHeight)

                //check top or bottom half
                let topOrBottom = scrollPos / height
                let elHeight = 0
                if (topOrBottom > 0.5) {
                  //get top alert height
                  this.$nextTick(function () {
                    // DOM updated
                    elHeight = document.getElementById('top-alert').offsetHeight
                    window.scrollTo(0, scrollPos + elHeight + 15)
                  })
                } else {
                  this.$nextTick(function () {
                    // DOM updated
                    elHeight = document.getElementById('top-alert').offsetHeight
                    window.scrollTo(0, scrollPos - elHeight)
                  })
                }
              })
            }
          },
          welcomeIconChange: function (evt) {
            var $this = this
            var files = evt.target.files
            var f = files[0]
            //$this.form.welcome.new_icon = ''

            if (files.length != 0) {

              var reader = new FileReader()

              $this.form.welcome.icon_name = f.name
              $this.form.welcome.new_icon = ''

              reader.onload = (function (theFile) {
                return function (e) {
                  $this.form.welcome.new_icon = e.target.result
                  $this.form.welcome.icon = e.target.result
                }

              })(f)

              reader.readAsDataURL(f)
            }
          },
          welcomeBackgroundChange: function (evt) {
            var $this = this
            var files = evt.target.files
            var f = files[0]
            //$this.form['welcome'].new_background = ''

            if (files.length != 0) {

              var reader = new FileReader()

              $this.form['welcome'].background_name = f.name
              $this.form['welcome'].new_background = ''

              reader.onload = (function (theFile) {
                return function (e) {
                  $this.form['welcome'].new_background = e.target.result
                  $this.form['welcome'].background = e.target.result
                }

              })(f)

              reader.readAsDataURL(f)
            }
          },
          vipBackgroundChange: function (evt) {
            var $this = this
            var files = evt.target.files
            var f = files[0]
            //$this.form['vip'].new_background = ''

            if (files.length != 0) {

              var reader = new FileReader()

              $this.form['vip'].background_name = f.name
              $this.form['vip'].new_background = ''

              reader.onload = (function (theFile) {
                return function (e) {
                  $this.form['vip'].new_background = e.target.result
                  $this.form['vip'].background = e.target.result
                }

              })(f)

              reader.readAsDataURL(f)
            }

          },
          referralBackgroundChange: function (evt) {
            var $this = this
            var files = evt.target.files
            var f = files[0]
            //$this.form['referral'].new_background = ''

            if (files.length != 0) {

              var reader = new FileReader()

              $this.form['referral'].background_name = f.name
              $this.form['referral'].new_background = ''

              reader.onload = (function (theFile) {
                return function (e) {
                  $this.form['referral'].new_background = e.target.result
                  $this.form['referral'].background = e.target.result
                }

              })(f)

              reader.readAsDataURL(f)
            }

          },
          clearIconImage: function () {
            this.form.welcome.icon = ''
            this.form.welcome.icon_name = ''
            this.form.welcome.new_icon = ''
          },
          clearBackgroundImage: function ($type) {
            this.form[$type].background = ''
            this.form[$type].background_name = ''
            this.form[$type].new_background = ''
          },
          copyClipboard: function () {
            let referralsField = document.querySelector('#referrals-field')
            referralsField.select()
            document.execCommand('copy')

            /* unselect the text */
            window.getSelection().removeAllRanges()

          },
          opacityCalc: function ($val) {
            $val = $val.replace('%', '')
            return parseInt($val) / 100
          },
          opacityFormat: function ($type) {
            this.form[$type].background_opacity = this.form[$type].background_opacity.replace('%', '') + '%'
          },
          countDownChanged (dismissCountDown) {
            this.alert.dismissCountDown = dismissCountDown
          },
        }
      })
    </script>
@endsection