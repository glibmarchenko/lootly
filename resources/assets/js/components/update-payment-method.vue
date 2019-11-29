<template>
    <b-modal class="custom-modal" hide-footer id="update-payment-method-modal" title="Update Payment Method" v-cloak>
        <div class="p-r-5 p-l-5">
            <div class="row m-b-15">
                <div class="col-md-3 col-6 p-r-0">
                    <label class="light-font m-t-5">Cardholder's Name:</label>
                </div>
                <div class="col-md-9 col-6">
                    <input class="form-control" name="name" v-model="cardForm.name">
                    <span class="alert-danger form-control"
                          v-show="cardForm.errors.has('name')">{{ cardForm.errors.get('name') }}</span>
                </div>
            </div>

            <div class="row m-b-15">
                <div class="col-md-3 col-6">
                    <label class="light-font m-t-5">Card:</label>
                </div>
                <div class="col-md-9 col-6">
                    <div id="payment-card-element"></div>
                    <span class="alert-danger form-control"
                          v-show="cardForm.errors.has('card')">{{ cardForm.errors.get('card') }}</span>
                </div>
            </div>

            <!--<div class="row m-b-15">
                <div class="col-md-3 col-6">
                    <label class="light-font m-t-5">ZIP / Postal Code:</label>
                </div>
                <div class="col-md-9 col-6">
                    <input class="form-control" name="website" v-model="form.zip">
                    <span class="alert-danger form-control"
                          v-show="form.errors.has('zip')">{{ cardForm.errors.get('zip') }}</span>
                </div>
            </div>-->

            <div class="row m-t-20 p-b-10 p-t-20 border-top">
                <div class="col-md-12">
                    <button class="btn btn-block modal-btn-lg btn-success btn-glow" @click.prevent="update"
                            :disabled="form.busy">
                        Update
                    </button>
                </div>
            </div>
        </div>
    </b-modal>
</template>

<script>
  export default {
    props: {},
    data: function () {
      return {
        stripe: Spark.stripeKey ? Stripe(Spark.stripeKey) : null,
        cardElement: null,

        form: new GeneralForm({
          stripe_token: '',
          address: '',
          address_line_2: '',
          city: '',
          state: '',
          zip: '',
          country: 'US'
        }),

        cardForm: new GeneralForm({
          name: '',
        }),

        alert: {
          text: '',
          success: '',
        },

        errors: '',
        saving: false
      }
    },
    mounted () {
      this.cardElement = this.createCardElement('#payment-card-element')
    },
    methods: {
      /**
       * Update the billable's card information.
       */
      update () {
        this.form.busy = true
        this.form.errors.forget()
        this.form.successful = false
        this.cardForm.errors.forget()

        // Here we will build out the payload to send to Stripe to obtain a card token so
        // we can create the actual subscription. We will build out this data that has
        // this credit card number, CVC, etc. and exchange it for a secure token ID.
        const payload = {
          name: this.cardForm.name,
          address_line1: this.form.address || '',
          address_line2: this.form.address_line_2 || '',
          address_city: this.form.city || '',
          address_state: this.form.state || '',
          address_zip: this.form.zip || '',
          address_country: this.form.country || '',
        }

        // Once we have the Stripe payload we'll send it off to Stripe and obtain a token
        // which we will send to the server to update this payment method. If there is
        // an error we will display that back out to the user for their information.
        this.stripe.createToken(this.cardElement, payload).then(response => {
          if (response.error) {
            this.cardForm.errors.set({
              card: [
                response.error.message
              ]
            })

            this.form.busy = false
          } else {
            this.sendUpdateToServer(response.token.id)
          }
        })
      },

      /**
       * Send the credit card update information to the server.
       */
      sendUpdateToServer (token) {
        this.form.stripe_token = token

        this.form.startProcessing()

        axios.put('/api/merchants/' + Spark.state.currentTeam.id + '/payment-method', this.form).then((response) => {
          this.cardForm.name = ''
          this.cardForm.number = ''
          this.cardForm.cvc = ''
          this.cardForm.month = ''
          this.cardForm.year = ''
          this.form.zip = ''

          window.location.reload()

        }).catch((errors) => {
          this.form.setErrors(errors.response.data.errors)
        }).then(() => {
          this.form.finishProcessing()
        })
      },
      createCardElement (container) {
        if (!this.stripe) {
          throw 'Invalid Stripe Key/Secret'
        }

        var card = this.stripe.elements().create('card', {
          hideIcon: true,
          hidePostalCode: true,
          style: {
            base: {
              '::placeholder': {
                color: '#aab7c4'
              },
              fontFamily: 'Whitney, Lato, -apple-system, BlinkMacSystemFont,"Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji","Segoe UI Emoji", "Segoe UI Symbol"',
              color: '#495057',
              fontSize: '15px'
            }
          }
        })

        card.mount(container)

        return card
      }
    },
    created: function () {

    }
  }
</script>
<style scoped>
    .btn-success {
        width: 200px;
        margin: auto;
    }
</style>