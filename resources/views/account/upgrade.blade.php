@extends('layouts.app')

@section('title', 'Upgrade')

@section('header')
    <style type="text/css">
        .contents-block {
            min-height: 1200px;
            background: linear-gradient(to bottom, #f5f8ff 0, #f5f8ff 26%, #f5f8ff 32%, #f5f8ff 38%, #fff 100%)
        }
    </style>
@endsection

@section('content')
    <div id="pricing-page" class="pricing-page loader" :class="{'loading': loading}" v-cloak>
        <div class="pricing-header">
            <div v-if="errors.length">
                <div class="alert alert-danger">
                    <div v-for="error in errors">@{{error}}</div>
                </div>
            </div>
            <h1>
                Simple and <span>Transparent</span> pricing
            </h1>
            <p>
                Unlimited Customers & Unlimited Orders
            </p>
            <div class="switch-input-wrap">
                <strong class="month" :class="{'active': !yearly}">Monthly</strong>
                <label class="switch">
                    <input type="checkbox" v-model="yearly">
                    <span class="switch-slider round"></span>
                </label>
                <strong class="year" :class="{'active': yearly}">Yearly <span>(Save 10%)</span></strong>
            </div>
        </div>
        <section class="pricing-plans">
            <div class="row" id="monthly-row">
                <div class="col-12">
                    <ul class="different-pricing">
                        <li v-for="plan in plans" :class="plan.type+'-plan'">
                            <div v-if="plan.type == 'ultimate'" class="ultimate-top">Most Popular</div>
                            <div class="price-wrap" :class="plan.type">
                                <h2 v-text="plan.name"></h2>
                                <span v-if="plan.type != 'custom'">
                                <i class="i-treasure" :class="plan.type"></i>
                                </span>
                                <span v-else>
                                <img src="{{ url('images/icons/custom-cog.png') }}" height="100">
                                </span>
                                <p class="plan-price">
                                    <sup v-if="plan.type != 'custom'">$</sup>
                                    <span v-if="plan.type != 'custom'">
                                    @{{calcPrice(plan.price) | format-number}}
                                    </span>
                                    <span v-else>Custom Quote</span>
                                </p>
                                <p class="plan-duration">
                                    <span v-if="plan.type != 'custom'">
                                    per <span v-if="yearly">Year</span><span v-else>Month</span>
                                    </span>
                                    <span v-else>Starting at $1,000</span>
                                </p>
                                <p class="plan-desc">
                                    <span>
                                    Unlimited Orders <br>
                                    Unlimited Customers
                                    </span>
                                </p>
                                <button v-if="trialActive && currentPlan === plan.type"
                                        type="button"
                                        class="btn plan-btn current"
                                        @click="showConfirmCancelTrialModal"
                                >
                                    {{ __('Cancel Trial') }}
                                </button>
                                <a v-else-if="plan.type !== 'custom'"
                                   @click="upgradePlan(plan)"
                                   href="#"
                                   :d-href="plan.href"
                                   class="btn plan-btn"
                                   :class="{'current': currentPlan === plan.type}"
                                >
                                    <span v-if="currentPlan === plan.type">Current Plan</span>
                                    <span v-else>Sign Up Now</span>
                                </a>
                                <a v-else
                                   href="{{ url('request-demo') }}"
                                   class="btn btn-primary">
                                    Request a Demo
                                </a>
                                <div class="plan-featured">
                                    <h3 v-text="plan.features.title"></h3>
                                    <ul>
                                        <li v-for="(item, index) in plan.features.items"
                                            :id="plan.type+'Popover-'+index">
                                            <span v-if="item.title">
                                              <i class="fa fa-check" aria-hidden="true"></i>
                                              <span v-text="item.title"></span>
                                            </span>
                                            <b-popover v-if="item.tooltip"
                                                       :target="plan.type+'Popover-'+index"
                                                       placement="bottom"
                                                       triggers="hover">
                                                <span v-html="item.tooltip" class="f-s-14 p-t-5 p-b-5"></span>
                                            </b-popover>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <section class="faq-section">
            <h2>Frequently Asked <span>Questions</span></h2>
            <div class="row">
                <div class="col-md-6 col-12" v-for="faq in faqs">
                    <div class="faq">
                        <h4 v-text="faq.question"></h4>
                        <p v-html="faq.answer"></p>
                    </div>
                </div>
            </div>
        </section>
        <section class="request-demo-section">
            <div class="row">
                <div class="col-12">
                    <div class="">
                        <h2>Not sure which plan is right for you?</h2>
                        <p>One of our team members can easily walk you through everything we offer, <br>
                            and help to answer any questions you have along the way.
                        </p>
                        <div class="">
                            <a href="{{ url('request-demo') }}" class="btn btn-primary btn-lg">
                                Request a Demo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <b-modal ref="bv-cancel-trial-modal"
                 title="{{ __('Trial Cancellation') }}"
                 modal-class="custom-modal"
                 body-class="px-0"
                 hide-footer
                 v-cloak
        >
            <div>
                <div class="row m-b-10 m-t-5">
                    <div class="col-12">
                        <div class="alert alert-warning mx-3" role="alert">
                            <img src="{{ url('images/icons/fa-warning.png') }}" width="45" class="float-left mr-3" alt="warning">
                            {{ __('Are you sure you wish to cancel the trial? If you need help or have any questions, our team is standing by to help you.') }}
                        </div>
                    </div>
                </div>
                <div class="row m-t-10 p-b-10 p-t-20 mx-0 border-top">
                    <div class="col-6">
                        <button @click="cancelTrial" class="btn modal-btn-lg btn-block btn-primary btn-glow">
                            {{ __('Cancel Trial') }}
                        </button>
                    </div>
                    <div class="col-6">
                        <button @click="hideConfirmCancelTrialModal" class="btn modal-btn-lg btn-block btn-secondary btn-glow">
                            {{ __('Close') }}
                        </button>
                    </div>
                </div>
            </div>
        </b-modal>

        <div class="request-demo-overlay" :class="{'overlay-active': overlay}">
            <div class="container">
                <div class="pop-detail" id="detailsPopup">
                    <h2>Interested in <span class="bold">Lootly?</span></h2>
                    <p class="m-b-15">
                        Schedule a demo with one of our team members <span>to learn more.</span>
                    </p>
                    <form id="demoForm">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <input class="form-control" placeholder="Name" name="name" type="text">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Email" name="email" type="email">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Website" name="website" type="text">
                                </div>
                                <button type="button" class="btn btn-primary f-s-18 p-b-10 w-100"
                                        @click="submitRequest();">Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="thanks-pop-up" id="thanksPopup" style="display: none;">
                    <div class="tick-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="148" height="148" viewBox="0 0 72 72">
                            <g fill="none" stroke="#1cc286" stroke-width="2">
                                <circle cx="36" cy="36" r="35"
                                        style="stroke-dasharray:240px, 240px; stroke-dashoffset: 480px;"></circle>
                                <path d="M17.417,37.778l9.93,9.909l25.444-25.393"
                                      style="stroke-dasharray:50px, 50px; stroke-dashoffset: 0px;"></path>
                            </g>
                        </svg>
                    </div>
                    <h3>Thank you</h3>
                    <p>
                        We'll be in touch within
                        <br> a few hours to schedule the demo.
                    </p>
                    <div class="pop-up-btn-wrap">
                        <a href="{{ url('/about') }}" class="btn btn-primary m-r-20">Our Story</a>
                        <!-- <a href="/about/testimonials" class="btn btn-primary">Testimonials</a> -->
                    </div>
                </div>
            </div>
            <div class="close-overlay" @click="overlay = false">
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
      var page = new Vue({
        el: '#pricing-page',
        data: {
          loading: false,
          overlay: false,
          currentPlan: null,
          yearly: false,
          yearlyDiscount: '0.1',
          trialActive: false,
          permanentPlans: [{
            type: 'custom',
            name: 'Custom Solutions',
            price: 'Custom Quote',
            features: {
              title: 'Includes these features:',
              items: [
                {title: 'Ongoing Program Management', tooltip: 'Our enterprise account managers are responsible for working with you on a daily basis to make any adjustments to your program as needed, including custom changes and special requests.'},
                {title: 'Launch Assistance', tooltip: 'Your enterprise account manager will setup a plan on how to present your new loyalty program to buyers, including email marketing and social media awareness. They will also work with you on setting up everything prior to launch.'},
                {title: 'Strategy Planning', tooltip: 'Besides having access to an enterprise account manager, you will also gain access to our success team who can work with you on planning new feature deployments, design roll outs and integrations to work seamlessly with your loyalty program.'},
                {title: 'Custom Development', tooltip: 'Need a special feature, design item or use case built out? Our engineering team can work with your brand to build something from the ground up to work great for your needs.'},
                {title: 'Custom Apps', tooltip: 'Similar to Custom Development, we also offer the ability to build custom integrations into apps such as eCommerce platforms, email, reviews and more. Leverage our expertise in the space, to build something that connects perfectly to your Lootly program.'},
              ]
            }
          }],
          plans: [],
          errors: [],
          faqs: [
            {
              question: 'Are your Unlimited Plans REALLY Unlimited?',
              answer: 'Yes! There are no tricks or hidden fees here. Our plans include Unlimited Orders and Unlimited Customers. While other loyalty platforms limit your growth, we built Lootly to scale with your business without charging you more.'
            },

            {
              question: 'Can I import existing customers into Lootly?',
              answer: 'Absolutely! With Lootly, you simply need to upload a file with your current customers into your account and you’re done. There are no limits on how many customers you can import. <br><br>'
            },

            {
              question: 'What currency are plans charged in?',
              answer: 'All plans are charged in USD regardless of your location.'
            },

            {
              question: 'Do I need to sign a Contract?',
              answer: 'Nope! At Lootly, we’re all about simplicity and transparency. Our plans are month-to-month, with no contracts or setup fees.'
            },

            {
              question: 'How easy is this to setup?',
              answer: 'Lootly is incredibly simple to install to your store, as it only takes a few clicks and less than 1 minute of your time. Once installed, our team is always available via Live Chat, Email or Phone for helping answer any questions you have.'
            },

            {
              question: 'Can I change plans at any time?',
              answer: 'Sure! Since Lootly does not use contracts, you can easily upgrade your plan at any time from your account. This only takes a few seconds, and then you’re all set. <br><br>'
            },
          ]
        },
        created: function () {
          this.getCurrentSubscription();
          this.getCurrentPlan();
          this.getPlans();
        },
        methods: {
          showConfirmCancelTrialModal: function () {
              this.$refs['bv-cancel-trial-modal'].show();

              // NOTE: Alternative modal confirmation window
              // swal({
              //     className: "warning-swal",
              //     icon: "/images/icons/fa-warning.png",
              //     title: "Trial Cancellation",
              //     text: "Are you sure you wish to cancel the trial? If you need help or have any questions, our team is standing by to help you.",
              //     dangerMode: true,
              //     buttons: true,
              // }).then((response) => {
              //     if (response) {
              //         this.cancelTrial();
              //     }
              // });
          },
          hideConfirmCancelTrialModal: function () {
              this.$refs['bv-cancel-trial-modal'].hide();
          },
          cancelTrial: function () {
              axios.put('/api/merchants/' + Spark.state.currentTeam.id + '/cancel-trial-subscription').then((response) => {
                  if (response.data && response.data.data) {
                      window.location.reload();
                  }
              }).catch((error) => {
                  // ...
              });
          },
          getCurrentPlan: function () {
            axios.get('/api/merchants/' + Spark.state.currentTeam.id + '/plan').then((response) => {
              if (response.data && response.data.data) {
                let currentPlan = response.data.data
                this.currentPlan = currentPlan.type
              }
            }).catch((error) => {
              console.log(error)
            })
          },
          getCurrentSubscription: function () {
              axios.get('/api/merchants/' + Spark.state.currentTeam.id + '/subscription').then((response) => {
                  if (response.data && response.data.data) {
                      const data = response.data.data;
                      this.trialActive = data.trial_active ? true : false;
                  }
              }).catch((error) => {
                  // ...
              });
          },
          getPlans: function () {
            this.loading = true
            axios.get('/api/plans').then((response) => {
              if (response.data && response.data.data) {
                let plans = response.data.data
                console.log(plans)
                this.plans = plans.filter((item) => {
                  return item.type !== 'free'
                })
                this.plans = this.plans.concat(this.permanentPlans)
              }
            }).catch((error) => {
              console.log(error)
            }).then(() => {
              this.loading = false
            })
          },
          calcPrice: function (price) {
            if (!this.yearly) {
              return price
            } else {
              return (price * 12 * (1 - this.yearlyDiscount)).toFixed()
            }
          },
          upgradePlan: function (plan) {
            this.errors = []
            if (plan.type === this.currentPlan) {
              return
            }
            this.loading = true
            axios.post('/api/merchants/' + Spark.state.currentTeam.id + '/plan/upgrade', {
              plan_id: plan.id,
              yearly: this.yearly ? 1 : 0
            }).then((response) => {
              if (response.data && response.data.data) {
                let data = response.data.data
                if (!data.payment_provider || ['shopify', 'stripe'].indexOf(data.payment_provider) < 0) {
                  throw {
                    response: {
                      data: {
                        message: 'Unexpected error: Unknown payment provider'
                      }
                    }
                  }
                }
                if (data.payment_provider === 'shopify') {
                  if (data.confirmation_url && data.confirmation_url.length) {
                    window.location = data.confirmation_url
                  }
                }
                if (data.payment_provider === 'stripe') {
                  let stripe = Stripe('<?php echo config('services.stripe.key') ?>')
                  let checkoutRequest = data.checkout_request
                  stripe.redirectToCheckout(checkoutRequest).then(function (result) {
                    // Display result.error.message to your customer
                    console.log(result)
                  })
                }
              }
            }).catch((error) => {
              if (error.response && error.response.data && error.response.data.message) {
                this.errors = [error.response.data.message]
                this.loading = false
              }
            })
          }
        }
      })
    </script>
@endsection

