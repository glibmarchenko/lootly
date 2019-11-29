@extends('website.layout')

@section('title', 'Request a Demo')

@section('navbar', 'white-nav')

@section('content')
    <section id="requestDemoPage" class="signup">
        <div class="container p-b-50">
            <div class="row" style="min-width: calc(100% + 30px);">
                <div class="col-12 col-sm-5 m-b-20">
                    <span class="loader" v-cloak>
                        <div class="card ml-0" v-show="!showCalender">
                            <h4 class="m-b-10">Request a Demo</h4>
                            <p>Schedule a call with one of our team members to learn more about Lootly.</p>
                            <form method="POST" action="/">
                                <input v-model="name" type="text" name="name" id="demo_name" class="form-control" placeholder="First & Last name">
                                <input v-model="email" type="email" name="email" id="demo_email" class="form-control" placeholder="Email Address">
                                <input v-model="website" type="text" name="website" id="demo_website" class="form-control" placeholder="Website">
                                <button @click="submitRequest" type="button" class="btn">Submit</button>
                            </form>
                        </div>

                        <!-- Calendly inline widget begin -->
                        <div id="calendly" 
                             v-show="showCalender" 
                             class="calendly-inline-widget" 
                             data-url="https://calendly.com/trustspot/lootly-demo">
                            <iframe :src="'https://calendly.com/trustspot/lootly-demo?email='+email+'&name='+name" 
                                    width="100%" 
                                    height="100%" 
                                    frameborder="0"></iframe>
                        </div>
                        <!-- Calendly inline widget end -->
                    </span>
                </div>
                <div class="col-12 col-sm-7">
                    <div class="flex-center">
                        <div class="card ml-0" style="max-width: none;padding: 30px 20px 20px; width: 100%;">
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
            </div>
        </div>
    </section>

@endsection

@section('scripts')
@section('scripts')
    <script type="text/javascript" src="{{ url('js/plugins/vue.min.js') }}"></script>
    <script>
        var page = new Vue({
            el: '#requestDemoPage',
            data: {
                name: '',
                email: '{{ $email }}',
                website: '',
                showCalender: 0
            },
            methods: {
                submitRequest: function() {
                    this.showCalender = 1;
                    $.ajax({
                      type: "POST",
                      url: "{{ url('demo-submit') }}" ,
                      data: {
                          _token: "{{ csrf_token() }}",
                          name: $("#demo_name").val(),
                          email: $("#demo_email").val(),
                          website: $("#demo_website").val(),
                          title: $("title").text()
                      },
                      datatype: 'json' ,
                      success: function(res) {
                        gtag_report_conversion();
                        Intercom('update', {anonymous_email: $("#demo_email").val(), name: $("#demo_name").val(), website: $("#demo_website").val(), utm_source: "Demo Request"});
                      },
                      error: function(error) {
                        console.log(error)
                      },
                    });
                }
            }
        })  
    </script>
@endsection
