@extends('layouts.app')

@section('title', 'Widget Tab')

@section('content')
    <div id="tab-design" class="loader m-t-20 m-b-10" v-cloak>
          <b-alert v-cloak
                   :show="alert.dismissCountDown"
                   dismissible
                   :variant="alert.type"
                   @dismissed="alert.dismissCountdown=0"
                   @dismiss-count-down="countDownChanged">
              @{{alert.text}}
          </b-alert>

          <form id="">
              <div class="row m-t-20 p-b-10 section-border-bottom">
                  <div class="col-md-12 m-b-15">
                      <a href="{{ route('display.widget') }}" class="bold f-s-15 color-blue">
                          <i class="arrow left blue"></i>
                          <span class="m-l-5">Widget Overview</span>
                      </a>
                  </div>
                  <div class="col-md-6 col-6">
                      <h3 class="page-title m-t-0 color-dark">
                          Tab Settings
                      </h3>
                  </div>
                  <div class="col-md-6 col-6">
                      <save-button class="text-right" :saving="saving" @event="saveSetting"></save-button>
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
                                              Rewards Tab is
                                              <span class="bolder"
                                                    v-text="form.status == 0 ? 'Hidden' : 'Visible'"></span>
                                          </label>
                                      </div>
                                  </div>
                                  <div class="col-md-4 text-right">
                                      <a @click="toogleTabStatus" v-cloak>
                                        <span v-if="form.status == 0">
                                            <span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">View</span>
                                        </span>
                                        <span v-else>
                                            <span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Hide</span>
                                        </span>
                                      </a>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="well m-t-20">
                          <div :class="{ 'loading' : loading }" v-cloak>
                              <div class="row section-border-bottom p-b-10 m-b-15">
                                  <div class="col-md-12">
                                      <div class="form-group m-b-0">
                                          <label class="bolder f-s-15 m-b-0 m-t-5">General Settings</label>
                                      </div>
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-md-12">
                                      <label class="bold m-b-5">
                                          Position
                                      </label>
                                      <b-form-select v-model="form.position" name="position">
                                          <option value="left">Left</option>
                                          <!-- <option value="center">Center</option> -->
                                          <option value="right">Right</option>
                                      </b-form-select>
                                  </div>
                              </div>
                              <div class="row m-t-15">
                                  <div class="col-md-12">
                                      <div class="form-group m-b-0">
                                          <label class="d-block bold m-b-10">
                                              <span>Spacing</span>
                                          </label>
                                          <p>This controls the space of your tab & launcher relative to the customer's browser window.</p>
                                          <div class="row m-t-15">
                                              <div class="col-sm-6">
                                                  <label class="m-b-10">Side Spacing</label>
                                                  <div class="input-group">
                                                      <input @focus="focus = 'side'" 
                                                             @blur="focus = ''" 
                                                             v-model="form.side_spacing" 
                                                             type="number" 
                                                             name="side_spacing" 
                                                             class="form-control">

                                                      <div class="input-group-append">
                                                          <span class="input-group-text">px</span>
                                                      </div>
                                                  </div>
                                              </div>
                                              <div class="col-sm-6">
                                                  <label class="m-b-10">Bottom Spacing</label>
                                                  <div class="input-group">
                                                      <input @focus="focus = 'bottom'" 
                                                             @blur="focus = ''" 
                                                             v-model="form.bottom_spacing" 
                                                             type="number" 
                                                             name="bottom_spacing" 
                                                             class="form-control">
                                                      <div class="input-group-append">
                                                          <span class="input-group-text">px</span>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="well m-t-20">
                          <div :class="{ 'loading' : loading }" v-cloak>
                              <div class="row section-border-bottom p-b-10 m-b-15">
                                  <div class="col-md-12">
                                      <div class="form-group m-b-0">
                                          <label class="bolder f-s-15 m-b-0 m-t-5">Display Options</label>
                                      </div>
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-md-12">
                                      <label class="bold m-b-0">Desktop Layout</label>
                                      <b-form-radio-group v-model="form.display_on" name="display_on" stacked>
                                        <b-form-radio value="desktop-mobile">Desktop & Mobile</b-form-radio>
                                        <b-form-radio value="desktop-only">Desktop only</b-form-radio>
                                        <b-form-radio value="none">None - Link only</b-form-radio>
                                      </b-form-radio-group>
                                      <span v-if="form.display_on == 'none'">
                                        <p class="m-t-5 m-b-10">To display the launcher with a deep link, copy & paste the below code into your site. To learn more click <a target="_BLANK" href="http://support.lootly.io/design-customization/tab-customization">here</a>.</p>
                                        <input class="form-control" type="text" value='<a onclick="openLootlyWidget(event);">Rewards</a>'>
                                      </span>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="well m-t-20">
                          <div :class="{ 'loading' : loading }" v-cloak>
                              <div class="row section-border-bottom p-b-10 m-b-15">
                                  <div class="col-md-12">
                                      <div class="form-group m-b-0">
                                          <label class="bolder f-s-15 m-b-0 m-t-5">Design</label>
                                      </div>
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-md-12">
                                      <label class="bold m-b-0">Desktop Layout</label>
                                      <b-form-radio-group v-model="form.desktop_layout" name="desktop_layout" stacked>
                                        <b-form-radio value="icon-text">Icon with Text</b-form-radio>
                                        <b-form-radio value="icon-only">Icon only</b-form-radio>
                                        <b-form-radio value="text-only">Text only</b-form-radio>
                                      </b-form-radio-group>
                                  </div>
                              </div><hr>

                              <div class="row m-t-10">
                                  <div class="col-md-12">
                                      <label class="bold m-b-5">Text</label>
                                      <input type="text" class="form-control m-b-10" v-model="form.text" name="text"
                                             placeholder="Text">
                                      <p>Text will not display on mobile devices, only the icon will be visible.</p>
                                  </div>
                              </div><hr>

                              <div class="row m-t-10">
                                    <div class="col-md-6">
                                        <div class="m-b-0">
                                            <label class="light-font m-b-10">
                                                <span>Tab Color</span> 
                                            </label>
                                            <colorpicker :color="form.tabColor" v-model="form.tabColor" name="tabColor"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="m-b-0">
                                            <label class="light-font m-b-10">
                                                <span>Tab Font Color</span>
                                            </label>
                                            <select-color-picker :options="selectFontsOptions" :color="form.tabFontColor" v-model="form.tabFontColor" name="tabFontColor"/>
                                        </div>
                                    </div>
                              </div><hr>

                              <div class="row m-t-10">
                                  <div class="col-md-12">
                                      <p class="bold m-b-10">Icons</p>
                                      <b-form-radio-group class="tab-icon-select"
                                                          buttons
                                                          button-variant="outline-dark"
                                                          size="lg"
                                                          v-model="form.icon" />
                                                <b-form-radio v-for="icon in icons" :value="icon.icon" @change="selectIconChange(icon)">
                                                    <i :class="icon.icon" style="vertical-align: middle;"></i>
                                                </b-form-radio>
                                      </b-form-group>
                                  </div>
                              </div>

                              <div class="m-t-15 m-b-10">
                                  @if(!$have_customization_permissions)
                                      <no-access :loading="loading"
                                          title="{{$customizations_upsell->upsell_title}}" 
                                          desc="{{$customizations_upsell->upsell_text}}" 
                                          icon="{{$customizations_upsell->upsell_image}}" 
                                          plan="{{$customizations_upsell->getMinPlan()->name}}"></no-access>
                                  @else
                                  <hr>
                                  <div class="m-t-20">
                                      <label class="bold m-b-0">
                                          Tab Logo (recommended: 180px by 50px - will auto fit to size)
                                      </label>
                                      <div class="file-drag-drop w-100 m-t-10" v-cloak>
                                          <b-form-file class="upload-icon"
                                                      @change="iconChange"
                                                      name="new_icon"
                                                      accept="image/*">
                                          </b-form-file>
                                          <div class="custom-file-overlay">
                                              <div v-if="form.custom_icon == 1 && form.icon || form.new_icon">
                                                  <span class="img">
                                                      <img :src="form.icon" style="max-height:70px;max-width: 100%">
                                                  </span>
                                                  <h5 class="float f-s-17 bold">
                                                      <span class="text" v-text="form.icon_name"></span>
                                                  </h5>
                                                  <i @click="clearIconImage"
                                                      class="fa fa-times color-light-grey pointer"></i>
                                              </div>
                                              <div v-else>
                                                  <span class="img">
                                                      <i class="icon-image-upload"></i>
                                                  </span>
                                                  <h5 class="float f-s-17 bold">
                                                      <span class="text">Drag files to upload</span>
                                                  </h5>
                                              </div>
                                          </div>
                                      </div>
                                    </div>
                                  @endif
                              </div>
                          </div>
                      </div>
                      <div class="m-t-20">
                          @if($plan->growth_order > 0)
                              <div class="well">
                                  <div :class="{ 'loading' : loading }" v-cloak>
                                      <div class="row p-b-10">
                                          <div class="col-md-12">
                                              <div class="form-group m-b-0">
                                                  <label class="bolder f-s-15 m-b-0 m-t-5">Reward Reminder</label>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="row">
                                          <div class="col-md-12">
                                              <b-form-checkbox v-model="form.enable_reminders" name="enable_reminders">
                                                  Enable Reminders
                                              </b-form-checkbox>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          @endif
                      </div>
                  </div>
                  <div class="col-md-5 col-12">
                      <div class="sticky-top">
                          <div class="well">
                              <div :class="{'loading': loading || saving}" v-cloak>
                                  <h5 class="bold m-b-15">Tab Preview</h5>
                                  <div class="bordered p-t-40">
                                      <div class="row">
                                          <div class="col-md-12" v-bind:class=" 'text-' + form.position ">
                                              <button id="widgetBtn" 
                                                      type="button" 
                                                      class="btn"
                                                      :class="form.desktop_layout"
                                                      v-bind:style="{ background: form.tabColor, color: form.tabFontColor}">
                                                  
                                                  <span class="btn-icon" v-if="form.desktop_layout != 'text-only' && form.icon || form.new_icon ">
                                                      <img v-if="form.custom_icon == 1" :src="form.icon">
                                                      <i v-else :class="form.icon" style="color: inherit;"></i>
                                                  </span>

                                                  <span class="btn-text" v-if="form.desktop_layout != 'icon-only'" v-text="form.text"></span>
                                              </button>

                                              <div class="tab-side-alignment" 
                                                   :class="['pull-'+form.position, focus == 'side'? 'active': '']"> 
                                                <span></span>
                                                <span style="max-width: 220px;" :style="{width: form.side_spacing*1.2+'px'}"></span> 
                                                <span></span>
                                                <span class="text">@{{form.side_spacing}}px</span>
                                              </div>

                                              <div class="tab-bottom-alignment" 
                                                   v-show="form.position == 'left'"
                                                   :class="[focus == 'bottom'? 'active': '']"
                                                   :style="{'margin-left': alignArrowGuide()}"> 
                                                <span></span><span :style="{height: form.bottom_spacing+'px'}"></span> <span></span>
                                                <span class="text"
                                                      style="margin-left: 15px">@{{form.bottom_spacing}}px</span>
                                              </div>

                                              <div class="tab-bottom-alignment" 
                                                   v-show="form.position == 'right'"
                                                   :class="[focus == 'bottom'? 'active': '']"
                                                   :style="{'margin-right': alignArrowGuide()}"> 
                                                <span></span><span :style="{height: form.bottom_spacing+'px'}"></span> <span></span>
                                                <span class="text">@{{form.bottom_spacing}}px</span>
                                              </div>

                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </form>
    </div>
@endsection

@section('scripts')
    <link rel="stylesheet" type="text/css" href="https://assets.lootly.io/fonts/_widget-tab-icons.css">
    <script>
      var page = new Vue({
        el: '#tab-design',
        data: {
          form: {
            status: 1,
            text: 'Rewards',
            position: 'right',
            desktop_layout: 'icon-text',
            enable_reminders: false,
            side_spacing: '30',
            bottom_spacing: '30',
            display_on: 'desktop-mobile',
            icon: 'loot-tab-heart',
            new_icon: '',
            icon_name: 'Heart',
            custom_icon: 0,
            tabColor: '#2b69d1',
            tabFontColor: '#FFFFFF',
          },
          icons: [
            {icon: 'loot-tab-heart', name: 'Heart'},
            {icon: 'loot-tab-money', name: 'Money'},
            {icon: 'loot-tab-shopping-cart', name: 'Shopping Cart'},
            {icon: 'loot-tab-star', name: 'Star'},
            {icon: 'loot-tab-crown', name: 'Crown'},
            {icon: 'loot-tab-trophy', name: 'Trophy'},
          ],
          selectFontsOptions: [
            {label: 'White', value: '#FFFFFF'},
            {label: 'Black', value: '#000000'},
          ],      
          alert: {
            type: '',
            text: '',
            dismissSecs: 5,
            dismissCountDown: 0
          },
          focus: '',
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
            axios.get('/display/widget/tab/get').then((response) => {

              if (response.data.widget_settings) {
                let widget_settings = response.data.widget_settings;
                that.form.status = !!widget_settings.tab_rewards_visible
                if (widget_settings.tab_position) that.form.position = widget_settings.tab_position

                if (widget_settings.tab_text) that.form.text = widget_settings.tab_text
                that.form.enable_reminders = !!widget_settings.enable_reminders
                if (widget_settings.tab_desktop_layout) that.form.desktop_layout = widget_settings.tab_desktop_layout
                if (widget_settings.tab_display_on) that.form.display_on = widget_settings.tab_display_on
                if (widget_settings.tab_side_spacing) that.form.side_spacing = widget_settings.tab_side_spacing
                if (widget_settings.tab_bottom_spacing) that.form.bottom_spacing = widget_settings.tab_bottom_spacing
                if (widget_settings.tab_custom_icon) that.form.custom_icon = widget_settings.tab_custom_icon
                if (widget_settings.tab_icon) that.form.icon = widget_settings.tab_icon
                if (widget_settings.tab_icon_name) that.form.icon_name = widget_settings.tab_icon_name
                if (widget_settings.tab_bg_color) that.form.tabColor = widget_settings.tab_bg_color
                if (widget_settings.tab_font_color) that.form.tabFontColor = widget_settings.tab_font_color
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
              console.log(formData)
              if (formData.new_icon.length) formData.icon = ''

              axios.post('/display/widget/tab/store', formData).then((response) => {
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
                    that.form.status = !!widget_settings.tab_rewards_visible
                    that.form.position = widget_settings.tab_position
                    that.form.text = widget_settings.tab_text
                    that.form.tabColor = widget_settings.tab_bg_color
                    that.form.tabFontColor = widget_settings.tab_font_color
                    that.form.icon = widget_settings.tab_icon
                    that.form.icon_name = widget_settings.tab_icon_name
                    that.form.new_icon = ''
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
              })
            }
          },
          iconChange: function (evt) {
            var $this = this
            var files = evt.target.files
            var f = files[0]
            //$this.form.new_icon = ''
            if (files.length != 0) {
              var reader = new FileReader();
              $this.form.custom_icon = 1;
              $this.form.icon_name = f.name
              $this.form.new_icon = ''
              reader.onload = (function (theFile) {
                return function (e) {
                  $this.form.new_icon = e.target.result
                  $this.form.icon = e.target.result
                }
              })(f)
              reader.readAsDataURL(f)
            }

          },
          selectIconChange: function (icon) {
            this.form.custom_icon = 0;
            this.form.icon_name = icon.name;
            this.form.new_icon = '';
          },
          toogleTabStatus: function () {
            if (this.form.status == 1) {
              this.form.status = 0
              this.form.display_on = 'none';
            } else {
              this.form.status = 1
              if(this.form.display_on == 'none') {
                this.form.display_on = 'desktop-mobile';
              }
            }
          },
          clearIconImage: function () {
            this.form.custom_icon = 0;
            this.form.icon = this.icons[0].icon;
            this.form.icon_name = this.icons[0].name;
            this.form.new_icon = ''
          },
          countDownChanged (dismissCountDown) {
            this.alert.dismissCountDown = dismissCountDown
          },
          alignArrowGuide: function() {
            var offset = 0;
            if(this.form.desktop_layout == 'icon-text') {
              offset = 50
            } else if(this.form.desktop_layout == 'icon-only') {
              offset = 15
            } else {
              offset = 40
            }

            return (this.form.side_spacing*1.2) > (280-offset) ? 280+'px' : (this.form.side_spacing*1.2) + offset + 'px'
          }
        }
      })
    </script>
@endsection