@extends('onboarding.layout')

@section('title', 'Shopify')

@section('content')
    <div class="shopify-main">
        <div class="well p-l-0 p-r-0 p-b-20 text-center">
            <div class="loader" v-cloak>
                <div class="wizard-wrapper">
                    <span v-if="routeName == 'welcome'" >
                        <img class="m-t-10" src="{{ asset('images/icons/treasure-chest.png') }}" width="120">

                        <h1 class="bold m-t-40" style="font-size: 30px;">
                            Lootly Account
                        </h1>

                        <p class="m-t-20 f-s-16">Before we get started, we need to check if you already have an account with us.</p>
                        <div class="m-t-40 m-b-5">
                            <a class="btn btn-success m-l-5 m-r-5 m-b-10" href="{{ route('onboarding.shopify.setup') }}">I need a New Account</a>
                            <button class="btn btn-primary m-l-5 m-r-5 m-b-10" @click="nextRoute('login')">
                                I already have an Account
                            </button>
                        </div>
                    </span>

                    <span v-if="routeName == 'login'">

                        <img class="m-t-10" src="{{ asset('images/icons/treasure-chest.png') }}" width="60">

                        <h1 class="bold m-t-30" style="font-size: 30px;">
                            Login to your Lootly Account
                        </h1>

                        <p class="m-t-20 f-s-15">In order to connect your Shopify Store to your existing Lootly account, you need to login and click Connect</p>


                        <form class="shopify-login-form text-left m-t-25">
                            <div class="m-t-15">
                                <label>Email Address:</label>
                                <input type="email" name="email" placeholder="Email Address" class="form-control" v-model="login.email">
                            </div>
                            <div class="m-t-10">
                                <label>Password:</label>
                                <input type="password" name="password" placeholder="Password" class="form-control" v-model="login.password">
                            </div>

                            <button type="button" @click="accountLogin" class="btn btn-success btn-block bold m-t-15">
                                Log in
                            </button>

                            <p class="text-center m-t-25">
                                Don't have an account? 
                                <a class="" href="javascript::void(0)" @click="nextRoute('welcome')">Click here</a> 
                                to go back
                            </p>
                        </form>

                    </span>

                    <span v-if="routeName == 'accounts'">

                        <img class="m-t-10" src="{{ asset('images/icons/treasure-chest.png') }}" width="60">

                        <h1 class="bold m-t-30" style="font-size: 30px;">
                            Your Lootly Accounts
                        </h1>

                        <p class="m-t-20 f-s-16">Click the Connect button next to the account that you would like to connect to your Shopify store. If your store is not listed, click the New Account button the bottom.</p>
                        <div class="row m-t-20">
                            <div class="col-md-8 offset-md-2 col-12">
                                <div class="action-item" v-for="account in accounts">
                                    <p class="pull-left">Merchant Name: @{{account.title}}</p>
                                    <span v-if="account.status == '0'" class="btn btn-primary badge pull-right">Connect</span>
                                    <span v-else class="f-s-14 m-r-5 pull-right">Connected</span>
                                </div>
                            </div>
                        </div>

                        <a class="btn btn-success m-t-30" href="{{ route('onboarding.shopify.setup') }}">
                            Not listed, Create New Account
                        </a>

                    </span>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">
        var app = new Vue({
            el: "#app",
            data: {
                routeName: '',
                login: {
                    email: '',
                    password: ''
                },
                accounts: []
            },
            created: function () {
                if(findGetParameter('page')) {
                    this.routeName = findGetParameter('page');
                } else {
                    this.routeName = 'welcome';
                }
            },
            methods: {
                nextRoute: function ($name) {
                    this.routeName = $name;
                    $("html,body").animate({scrollTop: 0 }, 500);
                },
                accountLogin: function () {
                    if (this.login.email == '' || this.login.password == '') {
                        if(this.login.email == '')
                            showErrors(this.$el, { 'email': 'Email is required' });

                        if(this.login.password == '')
                            showErrors(this.$el, { 'password': 'Please enter your Password' });

                        return false;
                    }
                    
                    this.accounts = [
                        {id: '1', title: 'Larry\'s Tacos', status: '0'},
                        {id: '2', title: 'Bonnie\'s Sunglasses', status: '1'},
                        {id: '3', title: 'Aria\'s Disney Stuff', status: '0'},
                    ];

                    this.routeName = 'accounts';

                }
            }
        })
    </script>
@endsection