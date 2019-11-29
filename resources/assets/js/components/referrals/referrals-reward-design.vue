<template>

    <div class="well bg-white m-t-20">
        <div :class="{ 'loading' : loading }" v-cloak>
            <div class="row section-border-bottom p-b-10 m-b-15">
                <div class="col-md-12">
                    <div class="form-group m-b-0">
                        <label class="bolder f-s-15 m-b-0">
                            Design
                        </label>
                        <a class="bolder f-s-14 color-blue pull-right" href="" @click.prevent="openModal">Preview notification</a>
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
                            <input class="form-control" :placeholder="form.program.nameDefault"
                                   v-model="form.program.name">
                            <div class="row m-t-15 p-b-10 section-border-bottom">
                                <div class="col-md-12">
                                    <span class="custom-tag" v-for="tag in form.reward_name_tags">
                                        <span v-text="tag"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="light-font m-t-15 m-b-5">
                                Reward Text
                            </label>
                            <input class="form-control" :placeholder="`e.g. ${this.form.currency}5 off discount`"
                                   v-model="form.program.rewardTextDefault">
                            <div class="row m-t-15 p-b-10 section-border-bottom">
                                <div class="col-md-12">
                                    <span class="custom-tag" v-for="tag in form.reward_text_tags">
                                        <span v-text="tag"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="light-font m-t-15 m-b-5">
                                Reward Email Text
                            </label>
                            <!-- <textarea class="form-control min-h-100" style="line-height: 25px;height: 150px"
                                      v-model="emailTextPreview"></textarea> -->
                            <div class="reward-email-text">
                                <trumbowyg v-model="form.program.emailTextDefault" @tbw-blur="blurEmailField"
                                           id="emailDefaultText" :config="config" class="editor"></trumbowyg>
                            </div>
                            <div class="row m-t-15 p-b-10 section-border-bottom">
                                <div class="col-md-12">
                                    <span class="custom-tag email-tag" v-for="tag in form.reward_email_tags">
                                        <span v-text="tag" v-on:click="emailTagClick"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="light-font m-t-15 m-b-5">
                            <p>Custom Icon</p>
                            <span class="f-s-13 bolder color-light-grey">Recommended 250px x 250px - will auto size to fit</span>
                        </div>
                        <div class="file-drag-drop m-t-15" v-cloak>
                            <b-form-file @change="iconImageChange" v-model="form.program.icon"
                                         accept="image/*"
                                         ref="referralFileInput"></b-form-file>
                            <div class="custom-file-overlay">
                                            <span class="img" v-if="form.program.reward_icon || form.iconPreview">
                                                <img v-if="form.iconPreview" class="m-b-5 programsd"
                                                     :src="form.iconPreview"
                                                     style="max-height:70px;max-width: 100%">

                                                <img v-else-if="form.program.reward_icon" class="m-b-5"
                                                     :src="form.program.reward_icon"
                                                     style="max-height:70px;max-width: 100%">
                                            </span>
                                <span class="img" v-else>
                                                <i class="icon-image-upload"></i>
                                            </span>
                                <h5 class="float f-s-17 bold">
                                                <span class="text"
                                                      v-if="form.program.icon_name && form.program.reward_icon">
                                                    <span v-text="form.program.icon_name"></span>
                                                </span>
                                    <span class="text" v-else-if="form.program.icon && form.program.icon.name">
                                                    <span v-text="form.program.icon && form.program.icon.name"></span>
                                                </span>
                                    <span v-else>
                                                    Drag files to upload
                                                </span>
                                </h5>
                                <i v-if="(form.program.icon && form.program.icon.name) || form.program.reward_icon"
                                   @click="cleariconImage(form.reward_id)"
                                   class="fa fa-times color-light-grey pointer"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <custom-modal
                title="Preview email notification"
                id="preview-email"
                :toggle_modal="isModalOpen"
                :close_callback="hideModal">
            <referrals-reward-notify-preview
                    :body_text="emailTextPreview"
                    :logo="merchantSettings.logo">
            </referrals-reward-notify-preview>
        </custom-modal>
    </div>

</template>


<script type="text/javascript">

  export default {
    props: {
      iconDefaultClass: {
        required: true
      },
      emailTextPreview: {
        required: true
      },
      form: {
        required: true
      },
      loading: {
        required: true
      },
      config: {
        required: true
      },
      merchantSettings: {
        default: function () {
          return {logo: null}
        }
      }
      // logo: {
      //     required: true
      // },
    },
    data: function () {
      return {
        icon_parent_el: 'icon_el',
        isModalOpen: false,
      }
    },
    mounted () {
    },
    methods: {
      cleariconImage: function (rewardId) {
        this.$refs.referralFileInput.reset()
        clearPreviewIcon('test', this.icon_parent_el)
        if (this.form.program.reward_icon) {
          this.form.program.icon = null
          this.form.program.reward_icon = null
          this.form.program.icon_name = this.iconDefaultClass;
          this.form.iconPreview = ''
          showPreviewIcon(this.form.program.reward_icon, this.iconDefaultClass, this.icon_parent_el)
          // axios.delete('/referrals/rewards/receiver/icon/' + rewardId).then((response) => {
          //     this.form.program.icon = null;
          //     this.form.program.reward_icon = null;
          //     this.form.iconPreview = '';
          //     showPreviewIcon(this.form.program.reward_icon, this.iconDefaultClass, this.icon_parent_el);
          // }).catch((error) => {

          // });
        } else {
          this.form.program.icon = null
          this.form.program.action_icon = null
          this.form.iconPreview = ''
          showPreviewIcon(this.form.program.reward_icon, this.iconDefaultClass, this.icon_parent_el)
        }
      },
      iconImageChange: function (evt) {
        var $this = this
        var files = evt.target.files
        var f = files[0]
        $this.form.iconPreview = ''

        if (files.length != 0) {
          var reader = new FileReader()
          $this.form.program.icon_name = f.name
          $this.form.program.reward_icon = ''

          reader.onload = (function (theFile) {
            return function (e) {
              $this.form.iconPreview = e.target.result
              clearPreviewIcon($this.iconDefaultClass, $this.icon_parent_el)
              showPreviewIcon($this.form.iconPreview, $this.iconDefaultClass, $this.icon_parent_el)
            }

          })(f)
          reader.readAsDataURL(f)
        }

      },
      openModal: function () {
        this.isModalOpen = true
      },
      hideModal: function () {
        this.isModalOpen = false
      },

      blurEmailField: function (event) {
        $('#emailDefaultText').trumbowyg('saveRange')
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
    },

//        computed: {
//            emailTextPreview: function () {
//                if (this.form.reward.values || this.form.coupon.prefix ) {
//                    return this.form.program.emailText = this.form.program.emailTextDefault.replace('{discount}', this.form.reward.values).replace('{receiver-coupon}', this.form.coupon.prefix)
//                } else {
//                    return this.form.program.emailTextDefault;
//                }
//            },

//        },

  }
</script>