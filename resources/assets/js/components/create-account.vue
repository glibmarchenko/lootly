<template>
    <b-modal class="custom-modal" hide-footer id="create-account-modal" title="Create a Lootly Account" v-cloak>
        <b-alert :show="alert.dismissCountDown"
                 dismissible
                 :variant="alert.type"
                 @dismissed="alert.dismissCountdown=0"
                 @dismiss-count-down="countDownChanged">
            {{alert.text}}
        </b-alert>
        <div class="p-r-5 p-l-5">
            <div class="row m-b-15">
                <div class="col-md-3 col-6 p-r-0">
                    <label class="light-font m-t-5">Company Name:</label>
                </div>
                <div class="col-md-9 col-6">
                    <input class="form-control" name="name" v-model="form.name">
                    <span class="alert-danger form-control" v-if="errors.name">{{ errors.name[0] }}</span>
                </div>
            </div>

            <div class="row m-b-15">
                <div class="col-md-3 col-6">
                    <label class="light-font m-t-5">Website:</label>
                </div>
                <div class="col-md-9 col-6">
                    <input class="form-control" name="website" v-model="form.website">
                    <span class="alert-danger form-control" v-if="errors.website">{{ errors.website[0] }}</span>
                </div>
            </div>

            <div class="row m-b-10">
                <div class="col-md-3 col-6">
                    <label class="light-font m-t-5">Country:</label>
                </div>
                <div class="col-md-9 col-6">
                    <b-form-select v-model="form.selectedCountry" name="selectedCountry" :options="countries"
                                   class="m-b-0">
                        <template slot="first">
                            <option :value="null" disabled>Select your country</option>
                        </template>
                    </b-form-select>
                    <span class="alert-danger form-control" v-if="errors.selectedCountry">
                        {{ errors.selectedCountry[0] }}
                    </span>
                </div>
            </div>

            <div class="row m-b-10">
                <div class="col-md-3 col-6 p-r-0">
                    <label class="light-font m-t-5">Company Logo:</label>
                </div>
                <div class="col-md-9 col-6">
                    <label class="light-font m-t-5"> 250 x 350px recommended (will auto size to fit)</label>
                </div>
            </div>
            <div class="file-drag-drop m-t-5" v-cloak>
                <b-form-file
                        @change="logoImageChange"
                        v-model="logoFile"
                        name="logo"
                        accept="image/*"
                ></b-form-file>

                <div class="custom-file-overlay">
                    <span class="img" v-if="form.logo == ''" style="top: 15px !important;">
                        <i class="icon-image-upload"></i>
                    </span>
                    <span class="img" v-else>
                        <img class="m-b-5" :src="form.logo" style="max-height:70px;max-width: 100%">
                    </span>
                    <h5 class="float f-s-17 bold" v-if="form.logo && form.logo_name">
                        <span class="text">
                            <span v-text="form.logo && form.logo_name"></span>
                        </span>
                    </h5>
                    <h5 class="f-s-17 bold" v-else>
                        Drag files to upload
                    </h5>
                    <i v-if="form.logo && form.logo_name" @click="clearLogoImage"
                       class="fa fa-times color-light-grey pointer"></i>
                </div>
            </div>
            <div class="row m-t-20 p-b-10 p-t-20 border-top">
                <div class="col-md-12 text-center">
                    <span v-if="saving" class="i-loading"></span>
                    <button v-show="!saving"  
                            @click.prevent="createAccount"
                            class="btn btn-block modal-btn-lg btn-success btn-glow">
                        Create Account
                    </button>
                </div>
            </div>
        </div>
    </b-modal>
</template>

<script>
  export default {
    props: {
      switchOnSuccess: {
        type: [Boolean, Number],
        default: false
      }
    },
    data: function () {
      return {
        form: {
          name: '',
          website: '',
          logo: '',
          logo_name: '',
          selectedCountry: null
        },
        logoFile: null,
        alert: {
          type: '',
          text: '',
          dismissSecs: 5,
          dismissCountDown: 0
        },
        countries: [],
        errors: '',
        saving: false
      }
    },
    methods: {
      logoImageChange (evt) {
        let $this = this
        let files = evt.target.files
        let f = files[0]
        if (files.length !== 0) {
          let reader = new FileReader()

          $this.form.logo_name = f.name
          $this.form.logo = ''

          reader.onload = (function (theFile) {
            return function (e) {
              $this.form.logo = e.target.result
              $this.logoFile = e.target.result
            }

          })(f)

          reader.readAsDataURL(f)
        }
      },
      clearLogoImage () {

        this.logoFile = ''
        this.form.logo = ''
        this.form.logo_name = ''
      },
      getCountriesList () {
        let list = require('../_partials/countries-list.js')
        this.countries = []
        for (let i = 0; i < list.length; ++i) {
          let code = list[i].code
          let name = list[i].name
          if (this.$i18n && this.$i18n.country && this.$i18n.country[code]) {
            name = this.$i18n.country[code]
          }
          this.countries.push({
            value: code,
            text: name
          })
        }
      },
      createAccount () {
        const that = this
        if (!that.saving) {
          that.saving = true
          axios.post('/api/merchants', this.form).then((response) => {
            if (response.data.data) {
              let merchant = response.data.data
              if (!!that.switchOnSuccess) {
                window.location.href = '/settings/merchants/' + merchant.id + '/switch'
              } else {
                that.alert.type = 'success'
                that.alert.text = 'Account information saved successfully'
                that.alert.dismissCountDown = that.alert.dismissSecs
                that.$root.$emit('bv::hide::modal', 'create-account-modal')
              }
            } else {
              that.alert.type = 'danger'
              that.alert.text = 'Unexpected error during Lootly account creation. Please, reload page and try again.'
              that.alert.dismissCountDown = that.alert.dismissSecs
            }
          }).catch((error) => {
            try {
              if (error.response && error.response.data && error.response.data.message) {
                that.alert.type = 'danger'
                that.alert.text = error.response.data.message
                that.alert.dismissCountDown = that.alert.dismissSecs
              }
              clearErrors(this.$el)
              if (error.response && error.response.data && error.response.data.errors) {
                showErrors(this.$el, error.response.data.errors)
              } else {
                that.alert.type = 'danger'
                that.alert.text = 'Unexpected error during Lootly account creation. Please, reload page and try again.'
                that.alert.dismissCountDown = that.alert.dismissSecs
              }
            } catch (e) {
              window.location.reload()
            }
          }).then(() => {
            that.saving = false
          })
        }
      },
      countDownChanged: function (dismissCountDown) {
        this.alert.dismissCountDown = dismissCountDown
      },
    },
    created: function () {
      this.getCountriesList()
    }
  }
</script>
<style scoped>
    .file-drag-drop,
    .b-form-file,
    .custom-file-input {
        height: 150px !important;
    }

    .custom-file-overlay .img {
        top: 20px !important;
    }

    .file-drag-drop .custom-file-overlay h5 {
        top: 100px !important;
    }

    .btn-success {
        width: 200px;
        margin: auto;
    }
</style>