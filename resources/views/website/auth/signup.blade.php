@extends('website.layout')

@section('title', 'Signup')

@section('navbar', 'blue-nav')

@section('content')
    <div id="signup" class="signup-page">
        <section class="signup {{$data['name']}}">
            <div class="container">
                <div class="row" style="min-width: calc(100% + 30px);">
                    <div class="col-12 col-sm-7" style="display: flex;flex-direction: column;">
                        <span>
                            <h3>{!! $data['title'] !!}</h3>
                            <div class="row">
                                @foreach($data['features'] as $feature)
                                    <div class="col-sm-6">
                                        <div class="feature-box">
                                            <img src="{{ url($feature['icon']) }}">
                                            <p>{{$feature['title']}}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </span>

                        @if($plan != 0)
                        <div class="mt-auto">
                            <div class="flex-center">
                                <div class="card ml-0" style="max-width: none;padding: 30px 20px 20px;">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <img class="m-b-10" src="{{ url('images/assets/main/company/audimods.jpg') }}" width="120">
                                        </div>
                                        <div class="col-sm-9">
                                            <div class="text-left">
                                                <p>"Our referral purchase rate has <b>increased 21%</b> since making the switch to Lootly. Our team is absolutely thrilled with the results."</p>
                                                <p style="font-size: 14px;"><b>Michael Williams</b> <br>
                                                    Director of eCommerce, Audi Mods
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                    <div class="col-12 col-sm-5">
                        <div class="card">
                            <h4>Create a Lootly Account </h4>

                            @if(!app('request')->input('invitation'))
                                <p class="title">
                                    Plan: {{ $data['name'] }}
                                    @if($plan != 0)
                                        ${{ number_format($data['price']) }}/{{ $yearly ? 'yr' : 'mo' }}
                                    @endif
                                </p>
                            @endif

                            <form action="{{ route('website.signup') }}"
                                  method="POST"
                                  class="loader"
                                  :class="{'loading': loading, 'form-loader': loading}"
                                  @submit="onSignUp"
                                  v-cloak
                            >

                                {{csrf_field()}}

                                <div v-if="Object.keys(errors).length || message.length" class="alert alert-danger">
                                    <div v-if="message.length">
                                        @{{ message }}
                                    </div>
                                    <ul v-for="error in errors" class="my-0">
                                        <li v-for="message in error">
                                            @{{ message }}
                                        </li>
                                    </ul>
                                </div>

                                @if($errors->has('plan'))
                                    <div class="alert alert-danger">
                                        {{ $errors->first('plan') }}
                                    </div>
                                @endif

                                @if(!app('request')->input('invitation'))
                                    <input v-model="form.company" type="text" name="company" value="{{ old('company')  }}" class="form-control"
                                           placeholder="Company Name">
                                    @if($errors->has('company'))
                                        <div class="alert alert-danger">
                                            {{ $errors->first('company') }}
                                        </div>
                                    @endif
                                @else
                                    <input v-model="form.invitation" type="hidden" name="invitation"
                                           value="{{ app('request')->input('invitation')  }}" class="form-control">
                                @endif

                                <input v-model="form.email"
                                       type="email"
                                       name="email"
                                       value="{{ old('email')  }}"
                                       class="form-control"
                                       :class="{ 'is-invalid': errors.email }"
                                       placeholder="Email Address"
                                       required
                                >
                                <div v-if="errors.email" class="invalid-feedback d-none">
                                    <ul class="list-unstyled my-0">
                                        <li v-for="error in errors.email">
                                            @{{ error }}
                                        </li>
                                    </ul>
                                </div>

                                @if($errors->has('email'))
                                    <div class="alert alert-danger">
                                        {{ $errors->first('email') }}
                                    </div>
                                @endif

                                <input v-model="form.password"
                                       type="password"
                                       name="password"
                                       class="form-control"
                                       :class="{ 'is-invalid': errors.password }"
                                       placeholder="Password"
                                       required
                                >
                                <div v-if="errors.password" class="invalid-feedback d-none">
                                    <ul class="list-unstyled my-0">
                                        <li v-for="error in errors.password">
                                            @{{ error }}
                                        </li>
                                    </ul>
                                </div>

                                @if($errors->has('password'))
                                    <div class="alert alert-danger">
                                        {{ $errors->first('password') }}
                                    </div>
                                @endif

                                <input v-model="form.plan" type="hidden" name="plan">

                                @if($plan != 0)

                                    <p class="title">Billing Information</p>

                                    <div class="two-cols">
                                        <input v-model="form.first_name" type="text" name="firstName" class="form-control first-child" placeholder="First Name">
                                        <input v-model="form.last_name" type="text" name="lastName" class="form-control last-child" placeholder="Last Name">
                                    </div>

                                    <div class="clearfix"></div>

                                    <div v-if="isFormPlan" class="stripe-elements my-3 d-none">
                                        <label for="card-element">
                                            Credit or debit card
                                        </label>

                                        <div ref="cardElement" id="card-element">
                                            <!-- A Stripe Element will be inserted here. -->
                                        </div>

                                        <!-- Used to display form errors. -->
                                        <div id="card-errors" class="text-danger mt-3" role="alert"></div>
                                    </div>

                                    <div class="input-group">

                                        <div ref="stripeCardNumber" id="stripe-card-number" class="form-control form-control-stripe m-t-10"></div>

                                        <div class="input-group-append m-t-10">
                                            <span class="input-group-text">
                                                <img src="{{ url('images/assets/main/lock-icon.png') }}" width="20">
                                            </span>
                                        </div>
                                    </div>
                                    <div class="two-cols">

                                        <div ref="stripeCardExpiry" id="stripe-card-expiry" class="form-control form-control-stripe first-child"></div>

                                        <div ref="stripeCardCvc" id="stripe-card-cvc" class="form-control form-control-stripe last-child"></div>

                                    </div>

                                    <div class="clearfix"></div>
                                    <div class="mt-3">
                                        <div v-if="stripeErrors.cardNumber" class="text-danger" role="alert">
                                            @{{ stripeErrors.cardNumber }}
                                        </div>
                                        <div v-else-if="stripeErrors.cardToken" class="text-danger" role="alert">
                                            @{{ stripeErrors.cardToken }}
                                        </div>

                                        <div v-if="stripeErrors.cardExpiry" class="text-danger" role="alert">
                                            @{{ stripeErrors.cardExpiry }}
                                        </div>

                                        <div v-if="stripeErrors.cardCvc" class="text-danger" role="alert">
                                            @{{ stripeErrors.cardCvc }}
                                        </div>
                                    </div>

                                @endif

                                <button v-if="isFormPlan" type="submit" class="btn signup-btn">
                                    {{ __('Start 7-Day Free Trial') }}
                                </button>
                                <button v-else type="submit" class="btn signup-btn">
                                    {{ __('Create Account') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ url('js/plugins/vue.min.js') }}"></script>
    <script src="{{ url('js/plugins/axios.min.js') }}"></script>
    <script>
        var page = new Vue({
            el: '#signup',
            data: {
                loading: false,
                form: {
                    plan: '{{ $plan }}',
                    plan_type: '{{ $data['type'] }}',
                    yearly: '{{ $yearly ? 1 : 0 }}',
                    _token: '{{ csrf_token() }}',
                    company: '{{ old('company') }}',
                    invitation: '',
                    email: '{{ old('email') }}',
                    password: '',
                    first_name: '',
                    last_name: '',
                    card_number: '',
                    cvv: '',
                    stripe_token_id: '',
                },
                services: {
                    stripe: {
                        key: '{{ config('services.stripe.key') }}',
                    },
                },
                stripeClient: undefined,
                stripeCard: undefined,
                stripeCardNumber: undefined,
                stripeCardExpiry: undefined,
                stripeCardCvc: undefined,
                stripeErrors: {
                    cardNumber: '',
                    cardExpiry: '',
                    cardCvc: '',
                    cardToken: '',
                },
                errors: {},
                message: '',
            },
            mounted: function () {
                if (this.isFormPlan) {
                    this.initStripe();
                }
            },
            computed: {
                isFormPlan: function () {
                    return this.form && this.form.plan && this.form.plan !== '0';
                },
            },
            methods: {
                initStripe: function () {
                    // Create a Stripe client.
                    const stripeClient = Stripe(this.services.stripe.key);

                    // Create an instance of Elements.
                    const stripeElements = stripeClient.elements({
                        locale: '{{ app()->getLocale() }}',
                    });

                    // Custom styling can be passed to options when creating an Element.
                    const stripeStyle = {
                        base: {
                            color: '#5a5a5a',
                            fontSize: '14px',
                            fontFamily: 'inherit',
                        },
                        invalid: {
                            color: '#dc3545',
                            iconColor: '#dc3545'
                        }
                    };

                    const stripeClasses = {
                        invalid: 'is-invalid',
                    };

                    const cardNumber = stripeElements.create('cardNumber', {
                        style: stripeStyle,
                        classes: stripeClasses,
                    });
                    cardNumber.mount(this.$refs.stripeCardNumber);

                    // Handle real-time validation errors from the card Element.
                    cardNumber.addEventListener('change', (event) => {
                        this.stripeErrors.cardToken = '';
                        this.stripeErrors.cardNumber = event.error && event.error.message ? event.error.message : '';
                    });

                    const cardExpiry = stripeElements.create('cardExpiry', {
                        style: stripeStyle,
                        classes: stripeClasses,
                    });
                    cardExpiry.mount(this.$refs.stripeCardExpiry);

                    // Handle real-time validation errors from the card Element.
                    cardExpiry.addEventListener('change', (event) => {
                        this.stripeErrors.cardToken = '';
                        this.stripeErrors.cardExpiry = event.error && event.error.message ? event.error.message : '';
                    });

                    const cardCvc = stripeElements.create('cardCvc', {
                        style: stripeStyle,
                        classes: stripeClasses,
                    });
                    cardCvc.mount(this.$refs.stripeCardCvc);

                    // Handle real-time validation errors from the card Element.
                    cardCvc.addEventListener('change', (event) => {
                        this.stripeErrors.cardToken = '';
                        this.stripeErrors.cardCvc = event.error && event.error.message ? event.error.message : '';
                    });

                    this.stripeCardNumber = cardNumber;
                    this.stripeCardExpiry = cardExpiry;
                    this.stripeCardCvc = cardCvc;

                    this.stripeClient = stripeClient;
                },
                sendSignUp: function () {
                    this.loading = true;

                    this.message = '';
                    this.errors = {};

                    axios.post('{{ route('website.signup') }}', this.form, {
                        headers: {'X-Requested-With': 'XMLHttpRequest'},

                    }).then((response) => {
                        const data = response.data.data;

                        if (data && data.payment_data) {
                            const paymentData = data.payment_data;

                            /*
                            // NOTE: Stripe Checkout
                            if (paymentData.payment_provider && paymentData.payment_provider === 'stripe') {
                                this.stripeClient.redirectToCheckout(paymentData.checkout_request);
                            }
                            */
                        }

                        if (data && data.redirect_to) {
                            window.location.href = data.redirect_to;
                        } else {
                            window.location.reload();
                        }

                    }).catch((error) => {
                        const { message, errors } = error.response.data;

                        this.message = message;
                        this.errors = errors;

                        this.loading = false;
                    });
                },
                onSignUp: function (e) {
                    this.loading = true;

                    if (! this.isFormPlan) {
                        return true;
                    }

                    let fullName = this.form.first_name;

                    if (this.form.first_name && this.form.last_name) {
                        fullName += ' ' + this.form.last_name;
                    }

                    const additionalData = {
                        name: fullName ? fullName : undefined,
                    };

                    this.stripeClient.createToken(this.stripeCardNumber, additionalData).then((result) => {
                        this.loading = false;

                        this.stripeErrors.cardToken = result.error && result.error.message ? result.error.message : '';

                        if (! result.error) {
                            // Send the token to server.
                            this.form.stripe_token_id = result.token.id;

                            this.sendSignUp();
                        }

                    }).catch((error) => {
                        this.loading = false;
                    });

                    e.preventDefault();
                }
            }
        })
    </script>
@endsection
