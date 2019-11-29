<template>
    <section class="widget-wrapper">
        <div class="widget-top-bar" :style="{ 'background-color': $root.globalWidgetSettings.headerBackground, 'color': $root.globalWidgetSettings.headerBackgroundFontColor }">
            <router-link :to="$root.fromRoute" replace>
                <i class="back-icon"></i>
            </router-link>
            <button type="button" class="close" @click.prevent="$root.sendMessageFromWidget('close-widget')">×</button>
        </div>
        <div class="main-full-block">
            <div class="widget-block main-block birthday-block">
                <div class="section-title border-bottom">
                    <p>Celebrate a Birthday</p>
                </div>
                <p class="section-desc">
                    Earn {{action.reward_text}} on your birthday! <br>
                    Simply enter in your birthday below, and once the date occurs, you will be rewarded automatically.
                </p>
                <p>This reward is given once per year.</p>

                <div class="row">
                    <div class="col-8 inline-input">
                        <input type="tel" id="birthdayInput" class="form-control masking m-t-5" placeholder="MM/DD/YYYY"
                               v-model="form.birthday">
                    </div>
                    <div class="col-4">
                        <button class="btn btn-block inline-input-btn" @click="saveBirthday"
                                v-if="!saving"
                                :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}">Save
                        </button>
                        <span class="loading-btn" v-else></span>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-success" v-if="alert">
                            Birthday saved!
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <router-link to="/widget/my-points/2" class="btn btn-block"
                                     style="margin-top: 15px;"
                                     :style="{ background: $root.globalWidgetSettings.buttonColor, color: $root.globalWidgetSettings.buttonFontColor}" replace>
                            View more ways to earn points
                        </router-link>
                    </div>
                </div>
            </div>

            <div class="lootly-footer">
                <a href="/" target="_blank">
                    <img src="/images/logos/logo-inner.png" style="width: 100px; margin: auto; padding-top: 15px;">
                </a>
            </div>
        </div>
    </section>
</template>

<script>
  export default {
    data: function () {
      return {
        action: {
          reward_text: 'points'
        },
        form: {
          birthday: ''
        },
        saving: false,
        alert: false,
        loading: true
      }
    },
    created: function () {
      var vm = this
      var token = this.$root.form.token
      //Call Login data with Token or Store_ID or whatever from $root

      vm.getData()

      vm.loading = false;
    },
    methods: {
      getData: function () {
        let vm = this
        // Get Create Account Action Data
        axios.post('/api/widget/actions/celebrate-birthday', this.$root.query).then((result) => {
          if (result.data && result.data.data) {
            let action = result.data.data
            vm.action = {
              id: action.id,
              action_id: action.action_id,
              action_name: action.action_name,
              point_value: action.point_value,
              reward_text: action.reward_text,
            }
          }
        }).catch((error) => {
          console.log(error)
          vm.$router.replace('/widget')
        }).then(() => {
          vm.loading = false
        })

      },
      saveBirthday: function () {
        let vm = this
        if (!vm.loading) {
          if (this.form.birthday) {
            vm.saving = true;
            vm.loading = true
            document.getElementById('birthdayInput').classList.remove('error')
            let formData = this.$root.query
            formData.birthday = this.form.birthday
            axios.put('/api/widget/customer/birthday', formData).then((result) => {
              this.alert = true
            }).catch((error) => {
              console.log(error)
              document.getElementById('birthdayInput').classList.add('error')
            }).then(() => {
              vm.saving = false;
              vm.loading = false
            })
          } else {
            document.getElementById('birthdayInput').classList.add('error')
          }
        }
      }
    },
    watch: {
      'form.birthday': function (v) {
        if (v.match(/^\d{2}$/) !== null) {
          this.form.birthday = v + '/'
        } else if (v.match(/^\d{2}\/\d{2}$/) !== null) {
          this.form.birthday = v + '/'
        }
      },
      'alert': function(val) {
        var _this = this;
        if(val) {
          setTimeout(function(){
            _this.alert = false;
          }, 5000)
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
    .birthday-block {
        padding-top: 20px;
        padding-bottom: 25px;

        & .section-title {
            margin-bottom: 5px;
            font-weight: bold;

            & p {
                margin-bottom: 10px;
            }
        }
        & .section-desc {
            margin-top: 10px;
            margin-bottom: 15px;
        }
    }

    .points-block {
        padding-top: 25px;
        padding-bottom: 20px;
        border-top: 1px solid #e6e8f0;
    }

    .alert {
        margin: 15px 0 0;
        padding: 8px 15px;
        font-size: 14px;
    }
</style>