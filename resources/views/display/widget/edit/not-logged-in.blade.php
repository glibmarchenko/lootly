@extends('layouts.app')

@section('title', 'Widget Design')

@section('content')
    <div id="widget-design" class="loader m-t-20 m-b-10" v-cloak>
        <div>
            <b-alert v-cloak
                     :show="alert.dismissCountDown"
                     id="top-alert"
                     dismissible
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
                    <div class="col-md-6 col-12">
                        <h3 class="page-title m-t-0 color-dark">
                            Widget Design
                        </h3>
                    </div>
                    <div class="col-md-6 col-12 text-right ">
                        <save-button class="text-right" :saving="saving" @event="saveSetting"></save-button>
                    </div>
                </div>
                <div class="row section-border-bottom p-t-25 p-b-25">
                    <div class="col-md-12">
                        <div class="well p-t-15 p-b-15">
                            <div class="row">
                                <div class="col-md-8 col-12">
                                    <p class="m-t-5 m-b-10">Viewing: <span class="bolder">Not Logged In</span></p>
                                </div>
                                <div class="col-md-4 col-12 text-right">
                                    <a class="btn btn-default bold" href="{{ route('display.widget.edit-logged-in') }}">Switch
                                        to Logged In</a>
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
                                            <p class="m-t-5">This is the first screen visitors see when opening the widget.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Background Title</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Welcome to ..."
                                                   v-model="form.welcome.header.title" name="welcome.header.title">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Background Sub-Title</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="{company}"
                                                   v-model="form.welcome.header.subtitle" name="welcome.header.subtitle">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Title</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Welcome text"
                                                   v-model="form.welcome.title" name="welcome.title">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Sub-Title</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Subtitle text"
                                                   v-model="form.welcome.subtitle" name="welcome.subtitle">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-6">
                                        <label class="m-b-10">Create an Account Button Text</label>
                                        <input type="text" class="form-control" v-model="form.welcome.buttonText" name="welcome.buttonText" placeholder="Button Text">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="m-b-10">Button Link</label>
                                        <input type="text" class="form-control" v-model="form.welcome.signupLink" name="welcome.signupLink" placeholder="ex: /signup">
                                    </div>
                                </div>

                                <div class="row m-t-15">
                                    <div class="col-md-6">
                                        <label class="m-b-10">Login Text</label>
                                        <input type="text" class="form-control" placeholder="Login link text"
                                               v-model="form.welcome.loginLinkText" name="welcome.loginLinkText">
                                    </div>
                                    <div class="col-md-6 m-b-5">
                                        <label class="m-b-10">Login Link</label>
                                        <input type="text" class="form-control" placeholder="ex: /login"
                                               v-model="form.welcome.loginLink" name="welcome.loginLink">
                                    </div>
                                </div>

                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <label class="m-b-10">Existing Account Text</label>
                                        <input type="text" class="form-control" placeholder="Login text"
                                               v-model="form.welcome.login" name="welcome.login">
                                    </div>
                                </div>
                                <hr>
                                <div class="row m-t-10">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Points & Rewards Title</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Points & Rewards Title"
                                                   v-model="form.welcome.pointsRewardsTitle" name="welcome.pointsRewardsTitle">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Points & Rewards Sub-Title</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Points & Rewards Sub-Title"
                                                   v-model="form.welcome.pointsRewardsSubtitle" name="welcome.pointsRewardsSubtitle">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Earning Title</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Earning Title"
                                                   v-model="form.welcome.pointsRewardsEarningTitle" name="welcome.pointsRewardsEarningTitle">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Spending Title</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Spending Title"
                                                   v-model="form.welcome.pointsRewardsSpendingTitle" name="welcome.pointsRewardsSpendingTitle">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>VIP Title</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="VIP Title"
                                                   v-model="form.welcome.vipTitle" name="welcome.vipTitle">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>VIP Sub-Title</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="VIP Sub-Title"
                                                   v-model="form.welcome.vipSubtitle" name="welcome.vipSubtitle">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Referral Title</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Referral Title"
                                                   v-model="form.welcome.referralTitle" name="welcome.referralTitle">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Referral Sub-Title</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Referral Sub-Title"
                                                   v-model="form.welcome.referralSubtitle" name="welcome.referralSubtitle">
                                        </div>
                                    </div>
                                </div>
                                <hr>
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
                        <div class="well m-t-20">
                            <div :class="{ 'loading' : loading }" v-cloak>
                                <div class="row section-border-bottom p-b-10 m-b-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="bolder f-s-15 m-b-0">Welcome Design</label>
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
                                <span>
                                    <div class="m-t-15 m-b-10">
                                        <label class="m-b-0">
                                            Background (recommended: 360px by 180px - will auto size to fit)
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
                                                  v-text="form.welcome.background_name">
                                              </span>
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
                                            <input type="text" class="form-control m-b-5" name="welcome.background_opacity"
                                                   v-model="form.welcome.background_opacity"
                                                   placeholder="Background Opacity" @blur="opacityFormat('welcome')">
                                        </div>
                                    </div>
                                </span>
                            @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 col-12">
                        <div class="sticky-top">
                            <div class="well">
                                <h5 class="bold m-b-20">Widget Preview</h5>
                                <widget-preview :company="storeName" :points="points" :welcome="form.welcome" :branding="branding" style="border-radius: 10px; overflow: hidden;box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.12);max-width: 360px; margin: auto; padding: 0;"></widget-preview>
                                <button id="widgetBtn" 
                                        type="button" 
                                        class="btn icon-only"
                                        style="margin-left: auto;display: block;margin-top: 30px;" 
                                        v-bind:style="{ background: branding.tabColor, color: branding.tabFontColor}">
                                    <div class="widget-close-btn">×</div>
                                </button>
                                <div id="widgetStyles"></div>
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
                                            <label class="bolder f-s-15 m-b-0">Ways to Earn Settings</label>
                                            <p>This screen displays after visitors click the “Ways to earn” button.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Title</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Ways to Earn title"
                                                   v-model="form.waysToEarn.title" name="waysToEarn.title">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Ways to Earn text"
                                                   v-model="form.waysToEarn.text" name="waysToEarn.text">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <label class="m-b-5">
                                            Text Position
                                        </label>
                                        <b-form-select v-model="form.waysToEarn.position" name="waysToEarn.position">
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
                                    <div style="width: 350px; margin: auto; max-width: 100%;">
                                        <div class="overflow border-bottom m-b-15">
                                            <h5 class="bold pull-left m-b-10 f-s-16">
                                                @{{ form.waysToEarn.title.replace(/{points-name}/g, points.plural) }}
                                            </h5>
                                        </div>
                                        <div>
                                            <p :class="'text-'+form.waysToEarn.position">
                                                @{{ form.waysToEarn.text.replace(/{points-name}/g, points.plural) }}
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
                                            <label class="bolder f-s-15 m-b-0">Ways to Spend Settings</label>
                                            <p>This screen displays after visitors click the “Ways to spend” button.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Title</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Ways to Spend title"
                                                   v-model="form.waysToSpend.title" name="waysToSpend.title">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Ways to Earn text"
                                                   v-model="form.waysToSpend.text" name="waysToSpend.text">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <label class="m-b-5">
                                            Text Position
                                        </label>
                                        <b-form-select v-model="form.waysToSpend.position" name="waysToSpend.position">
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
                                    <div style="width: 350px; margin: auto; max-width: 100%;">
                                        <div class="overflow border-bottom m-b-15">
                                            <h5 class="bold pull-left m-b-10 f-s-16">
                                                @{{form.waysToSpend.title}}
                                            </h5>
                                        </div>
                                        <div>
                                            <p :class="'text-'+form.waysToSpend.position">
                                                @{{ form.waysToSpend.text.replace(/{points-name}/g, points.plural) }}
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
                                            <label class="bolder f-s-15 m-b-0">Referral Receiver Design</label>
                                            <p class="m-t-5">This screen displays when the person you refer lands on
                                                your website.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group m-b-0">
                                            <label class="d-block m-b-10">
                                                <span>Text</span>
                                            </label>
                                            <input type="text" class="form-control" placeholder="Referral text"
                                                   v-model="form.referral.text" name="referral.text">
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-15">
                                    <div class="col-md-12">
                                        <label class="m-b-5">
                                            Button Text
                                        </label>
                                        <input type="text" class="form-control m-b-5" v-model="form.referral.buttonText"
                                               placeholder="Button Text" name="referral.buttonText">
                                    </div>
                                </div>

                                @if(!$have_referral_customization_permissions)
                                    <no-access :loading="loading"
                                        title="{{$referral_customizations_upsell->upsell_title}}" 
                                        desc="{{$referral_customizations_upsell->upsell_text}}" 
                                        icon="{{$referral_customizations_upsell->upsell_image}}" 
                                        plan="{{$referral_customizations_upsell->getMinPlan()->name}}"></no-access>
                                @else

                                <span>
                                    <div class="m-t-15 m-b-10">
                                        <label class="m-b-0">
                                            Background (recommended: 360px by 430px - will auto size to fit)
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
                                                   placeholder="Background Opacity" @blur="opacityFormat('referral')">
                                        </div>
                                    </div>
                                </span>
                            @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 col-12">
                        <div class="sticky-top">
                            <div class="well p-r-0 p-l-0 p-b-15">
                                <div class="widget-preview" :style="{fontFamily: branding.font}" :class="{ 'loading' : loading || saving }" v-cloak>
                                    <div class="preview-background"
                                         v-bind:style="{'background-image': 'url('+form.referral.background+')', opacity: opacityCalc(form.referral.background_opacity) }"></div>
                                    <button type="button" class="close preview-close" :style="{color: branding.primaryColor}">×</button>

                                    <div class="background-preview-box mobile-padding-x">
                                        <div class="widget-preview-content">
                                            <h5 class="f-s-16 m-b-30" style="max-width: 300px;color: #222;" 
                                                v-bind:class=" 'text-' + form.referral.position ">
                                                <span v-text="referralTextPreview"></span>
                                            </h5>

                                            <div class="m-t-10">
                                                <input class="form-control" placeholder="Enter your email">
                                            </div>

                                            <button class="btn btn-blue btn-block m-t-15 m-b-25" :style="{ background: branding.buttonColor, color: branding.buttonFont }">
                                                <span v-text="form.referral.buttonText"></span>
                                            </button>
                                        </div>
                                        
                                        @if(!$remove_branding)                                        
                                            <div class="m-t-15 text-center">
                                                <div class="light-border-top">
                                                    <a href="">
                                                        <img src="{{ config('app.logo-inner') }}"
                                                            style="width: 100px;margin: auto;padding-top: 15px;">
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
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
          storeName: Spark.state.currentTeam.name,
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
            welcome: {
              header: {
                  title: 'Welcome to',
                  subtitle: '{company}'
              },
              title: 'Join our Rewards Program',
              subtitle: 'Access exciting perks, savings and rewards just by shopping with us!',
              buttonText: 'Create an Account',
              login: 'Already have an account?',
              loginLinkText: 'Login',
              signupLink: '{!! $default_store_links[0] !!}',
              loginLink: '{!! $default_store_links[1] !!}',
              pointsRewardsTitle: '{points-name} & Rewards',
              pointsRewardsSubtitle: 'Earn {points-name} for completing actions, and turn your {points-name} into rewards.',
              pointsRewardsEarningTitle: 'Ways to earn',
              pointsRewardsSpendingTitle: 'Ways to spend',
              vipTitle: 'VIP Tiers',
              vipSubtitle: 'Gain access to exclusive rewards. Reach higher tiers for more exclusive perks.',
              referralTitle: 'Referrals',
              referralSubtitle: 'Tell your friends about us and earn rewards',
              position: 'center',
              // Background Image
              background: '',
              new_background: '',
              background_name: '',
              background_opacity: '100%'
            },
            waysToEarn: {
              title: 'Earn {points-name}',
              text: 'Earn more {points-name} for completing different actions with our rewards program.',
              position: 'left'
            },
            waysToSpend: {
              title: 'Earn Rewards',
              text: 'Redeem your {points-name} into awesome rewards.',
              position: 'left'
            },
            referral: {
              text: '{referral-name} has sent you a coupon for {referral-discount}. Get your coupon now.',
              position: 'left',
              buttonText: 'Get My Coupon',
              background: '',
              new_background: '',
              background_name: '',
              background_opacity: '100%'
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
            customCSS: '',            
            @if($remove_branding)
                hideLootlyLogo: true
            @else
                hideLootlyLogo: false
            @endif
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
            axios.get('/display/widget/widget/get').then((response) => {

              if (response.data.widget_settings) {
                let widget_settings = response.data.widget_settings
                if (widget_settings.widget_welcome_header_title) that.form.welcome.header.title = widget_settings.widget_welcome_header_title;
                if (widget_settings.widget_welcome_header_subtitle) that.form.welcome.header.subtitle = widget_settings.widget_welcome_header_subtitle;
                if (widget_settings.widget_welcome_title) that.form.welcome.title = widget_settings.widget_welcome_title;
                if (widget_settings.widget_welcome_subtitle) that.form.welcome.subtitle = widget_settings.widget_welcome_subtitle;
                if (widget_settings.widget_welcome_button_text) that.form.welcome.buttonText = widget_settings.widget_welcome_button_text;
                if (widget_settings.widget_welcome_login) that.form.welcome.login = widget_settings.widget_welcome_login;
                if (widget_settings.widget_welcome_login_link_text) that.form.welcome.loginLinkText = widget_settings.widget_welcome_login_link_text;

                if (widget_settings.widget_welcome_login_link) that.form.welcome.loginLink = widget_settings.widget_welcome_login_link;
                if (widget_settings.widget_welcome_signup_link) that.form.welcome.signupLink = widget_settings.widget_welcome_signup_link;

                if (widget_settings.widget_welcome_position) that.form.welcome.position = widget_settings.widget_welcome_position
                if (widget_settings.widget_welcome_background) that.form.welcome.background = widget_settings.widget_welcome_background
                if (widget_settings.widget_welcome_background_name) that.form.welcome.background_name = widget_settings.widget_welcome_background_name
                if (widget_settings.widget_welcome_background_opacity) that.form.welcome.background_opacity = widget_settings.widget_welcome_background_opacity

                if (widget_settings.widget_welcome_points_rewards_title) that.form.welcome.pointsRewardsTitle = widget_settings.widget_welcome_points_rewards_title
                if (widget_settings.widget_welcome_points_rewards_subtitle) that.form.welcome.pointsRewardsSubtitle = widget_settings.widget_welcome_points_rewards_subtitle

                if (widget_settings.widget_welcome_points_rewards_earning_title) that.form.welcome.pointsRewardsEarningTitle = widget_settings.widget_welcome_points_rewards_earning_title
                if (widget_settings.widget_welcome_points_rewards_spending_title) that.form.welcome.pointsRewardsSpendingTitle = widget_settings.widget_welcome_points_rewards_spending_title

                if (widget_settings.widget_welcome_vip_title) that.form.welcome.vipTitle = widget_settings.widget_welcome_vip_title
                if (widget_settings.widget_welcome_vip_subtitle) that.form.welcome.vipSubtitle = widget_settings.widget_welcome_vip_subtitle
                if (widget_settings.widget_welcome_referral_title) that.form.welcome.referralTitle = widget_settings.widget_welcome_referral_title
                if (widget_settings.widget_welcome_referral_subtitle) that.form.welcome.referralSubtitle = widget_settings.widget_welcome_referral_subtitle

                if (widget_settings.widget_ways_to_earn_title) that.form.waysToEarn.title = widget_settings.widget_ways_to_earn_title
                if (widget_settings.widget_ways_to_earn_text) that.form.waysToEarn.text = widget_settings.widget_ways_to_earn_text
                if (widget_settings.widget_ways_to_earn_position) that.form.waysToEarn.position = widget_settings.widget_ways_to_earn_position

                if (widget_settings.widget_ways_to_spend_title) that.form.waysToSpend.title = widget_settings.widget_ways_to_spend_title
                if (widget_settings.widget_ways_to_spend_text) that.form.waysToSpend.text = widget_settings.widget_ways_to_spend_text
                if (widget_settings.widget_ways_to_spend_position) that.form.waysToSpend.position = widget_settings.widget_ways_to_spend_position

                if (widget_settings.widget_rr_text) that.form.referral.text = widget_settings.widget_rr_text
                if (widget_settings.widget_rr_button_text) that.form.referral.buttonText = widget_settings.widget_rr_button_text
                if (widget_settings.widget_rr_background) that.form.referral.background = widget_settings.widget_rr_background
                if (widget_settings.widget_rr_background_name) that.form.referral.background_name = widget_settings.widget_rr_background_name
                if (widget_settings.widget_rr_background_opacity) that.form.referral.background_opacity = widget_settings.widget_rr_background_opacity

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
                if (widget_settings.custom_css) that.branding.customCSS = widget_settings.custom_css
                that.branding.hideLootlyLogo = !!widget_settings.brand_remove_in_widget

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
              if (formData.welcome.new_background.length) formData.welcome.background = ''
              if (formData.referral.new_background.length) formData.referral.background = ''

              axios.post('/display/widget/widget/store', formData).then((response) => {
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
                    that.form.welcome.header.title = widget_settings.widget_welcome_header_title
                    that.form.welcome.header.subtitle = widget_settings.widget_welcome_header_subtitle
                    that.form.welcome.title = widget_settings.widget_welcome_title
                    that.form.welcome.subtitle = widget_settings.widget_welcome_subtitle
                    that.form.welcome.buttonText = widget_settings.widget_welcome_button_text
                    that.form.welcome.login = widget_settings.widget_welcome_login
                    that.form.welcome.loginLinkText = widget_settings.widget_welcome_login_link_text

                    that.form.welcome.loginLink = widget_settings.widget_welcome_login_link
                    that.form.welcome.signupLink = widget_settings.widget_welcome_signup_link

                    that.form.welcome.position = widget_settings.widget_welcome_position
                    that.form.welcome.background = widget_settings.widget_welcome_background
                    that.form.welcome.background_name = widget_settings.widget_welcome_background_name
                    that.form.welcome.background_opacity = widget_settings.widget_welcome_background_opacity
                    that.form.welcome.new_background = ''

                    that.form.welcome.pointsRewardsTitle = widget_settings.widget_welcome_points_rewards_title
                    that.form.welcome.pointsRewardsSubtitle = widget_settings.widget_welcome_points_rewards_subtitle
                    that.form.welcome.pointsRewardsEarningTitle = widget_settings.widget_welcome_points_rewards_earning_title
                    that.form.welcome.pointsRewardsSpendingTitle = widget_settings.widget_welcome_points_rewards_spending_title

                    that.form.welcome.vipTitle = widget_settings.widget_welcome_vip_title
                    that.form.welcome.vipSubtitle = widget_settings.widget_welcome_vip_subtitle
                    that.form.welcome.referralTitle = widget_settings.widget_welcome_referral_title
                    that.form.welcome.referralSubtitle = widget_settings.widget_welcome_referral_subtitle

                    that.form.referral.text = widget_settings.widget_rr_text
                    that.form.referral.buttonText = widget_settings.widget_rr_button_text
                    that.form.referral.background = widget_settings.widget_rr_background
                    that.form.referral.background_name = widget_settings.widget_rr_background_name
                    that.form.referral.background_opacity = widget_settings.widget_rr_background_opacity
                    that.form.referral.new_background = ''

                  }
                }
              }).catch((error) => {
                that.saving = false
                clearErrors(this.$el)
                console.log(error.response.data.errors)
                showErrors(this.$el, error.response.data.errors)
                this.alert.dismissCountDown = this.alert.dismissSecs
                this.alert.type = 'danger'
                this.alert.text = error.response.data.message
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
          clearBackgroundImage: function ($type) {
            this.form[$type].background = ''
            this.form[$type].background_name = ''
            this.form[$type].new_background = ''
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
        },
        watch: {
            'branding.customCSS': function() {
                document.getElementById('widgetStyles').innerHTML = '<style>' + prefixCssSelectors(this.branding.customCSS, '.lootly-widget') + '</style>'
            }
        },    
        computed: {
          referralTextPreview: function () {
            return this.form.referral.text.replace('{referral-name}', 'Joe').replace('{referral-discount}', '10%')
          }
        }
      })
    </script>
@endsection