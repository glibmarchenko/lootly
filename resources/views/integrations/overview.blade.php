@extends('layouts.app')

@section('title', 'Integrations')

@section('content')
<div id="apps-page" class="integrations-overview loader m-t-20 p-b-30" v-cloak>
    <b-alert v-cloak
             :show="alert.dismissCountDown"
             dismissible
             :variant="alert.type"
             @dismissed="alert.dismissCountdown=0"
             @dismiss-count-down="countDownChanged">
        @{{alert.text}}
    </b-alert>
    <div class="row m-t-20 p-b-10 m-b-5 section-border-bottom">
        <div class="col-md-6 col-12">
            <h3 class="page-title m-t-0 color-dark">Integrations</h3>
        </div> 
        <div class="col-md-6 col-12 text-right ">
            <a class="bold color-blue f-s-15" v-b-modal.suggestion-modal>
                <i class="icon-add f-s-19 m-r-5"></i> Suggest an integration
            </a>
        </div>
    </div>

    <div class="row">
        @foreach($integrations as $integration)
            @if ($integration->showForPlan($plan))
                <div class="col-md-6 col-lg-4 m-t-25">
                    <div class="integration-well well">
                        <div class="text-center border-bottom p-b-25 p-t-5">
                            <img src="{{ asset($integration->logo) }}" height="45">
                        </div>
                        <div class="p-r-20 p-l-20 m-b-20">
                            <p class="bold m-t-15 m-b-10 f-s-18">{!!$integration->title!!}</p>
                            <p style="min-height: 63px;">{!!$integration->description!!}</p>
                        </div>
                        <div class="text-center mt-auto">
                            @if($integration->isActive($merchant))
                            <!-- Connected -->
                                @if(isset($integrationsLinks[$integration->slug]))
                                    <a class="btn btn-integrations btn-connected" data-app="{{ $integration->slug }}" {{ $integrationsLinks[$integration->slug]['connected'][1] }}>
                                        {{ $integrationsLinks[$integration->slug]['connected'][0] }}
                                    </a>
                                @else
                                    <a class="btn btn-integrations btn-connected">Connected</a>
                                @endif
                            @else
                            <!-- Not Connected -->
                                @if($integration->status)
                                    @if(isset($integrationsLinks[$integration->slug]))
                                        <a class="btn btn-integrations btn-connect" data-app="{{ $integration->slug }}" {{ $integrationsLinks[$integration->slug]['not-connected'][1] }}>
                                            {{ $integrationsLinks[$integration->slug]['not-connected'][0] }}
                                        </a>
                                    @else
                                        <a class="btn btn-integrations btn-connect" href="/integrations/manage/edit/{{$integration->slug}}" data-app="{{ $integration->slug }}">Connect</a>
                                    @endif
                                @else
                                    <div class="btn btn-integrations btn-coming-soon">
                                        Coming soon
                                    </div>
                                @endif
                            @endif
                        </div>                        
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Suggestion Modal -->
    <b-modal id="suggestion-modal" class="custom-modal" title="Integration Suggestion" hide-footer v-cloak>
        <div class="row m-b-10 m-t-10">
            <div class="col-md-12">
                <p class="light-font">Know of an app or service that you'd like to see connected to Lootly? Fill out the form below and we will alert you when it becomes available.</p>
            </div>
        </div>
        <div class="row m-b-15">
            <div class="col-md-12">
                <label class="light-font m-b-5">Integration Company Name</label>
                <input class="form-control" v-model="form.companyName" placeholder="e.g. acme">
            </div>
        </div>
        <div class="row m-b-10">
            <div class="col-md-12">
                <label class="light-font m-b-5">Integration Website</label>
                <input class="form-control" v-model="form.website" placeholder="www.google.com">
            </div>
        </div>
        <div class="row m-b-10">
            <div class="col-md-12">
                <label class="light-font m-b-5">How would you like Lootly to work with this new integration?</label>
                <textarea class="form-control min-h-100" v-model="form.body"></textarea>
            </div>
        </div>
        <div class="row m-t-20 p-b-10 p-t-20 border-top">
            <div class="col-md-6 offset-md-3">
                <a @click="submitSuggestion" class="btn modal-btn-lg btn-block btn-success btn-glow">
                    Submit
                </a>
            </div>
        </div>
    </b-modal>

    <!-- Trustspot Modal -->
    <b-modal id="trustspot-modal" class="custom-modal" title="TrustSpot Integration" hide-footer v-cloak>
        <div class="row m-b-10 m-t-10">
            <div class="col-md-12">
                <div class="text-center">
                    <img src="{{ url('images/logos/trustspot.png') }}" class="m-b-15" width="165">
                </div>
                <h5 class="bolder f-s-16 m-b-10">Overview</h5>
                <p class="light-font m-b-5">Since authenticity & social proof are the pillars of consumer trust online, TrustSpot provides brands with a comprehensive solution to capture ratings & reviews, video testimonials, photos, social experiences, product Q&A and more.</p>
                <p class="m-b-15">Today, more than 21,000 Global Brands utilize TrustSpot to help them collect more user generated content, while growing repeat purchases and customer lifetime value. </p>

                <h5 class="bolder f-s-16 m-b-10">TrustSpot & Lootly</h5>
                <p class="light-font">This integration allows for:</p>
                <ul class="p-l-35 m-t-5">
                    <li>Reward customers for writing a review </li>
                </ul>
            </div>
        </div>
        <div class="row m-t-20 p-b-10 p-t-20 border-top">
            <div class="col-md-6 offset-md-3">
                <a onclick="window.open('https://trustspot.io/BusinessTypes/ecommerce', '_blank').focus()" class="btn modal-btn-lg btn-block btn-success btn-glow">
                    Visit TrustSpot
                </a>
            </div>
        </div>
    </b-modal>

</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var integrations = new Vue({
        el: '#apps-page',
        data: {
            form: {
                companyName: '',
                website: '',
                body: ''
            },
            alert: {
                type: '',
                text: '',
                dismissSecs: 5,
                dismissCountDown: 0
            }
        },
        methods: {
            submitSuggestion() {
                axios.post('{{route("integrations.store_suggestion")}}', this.form).then((response) => {
                    this.alert.text = response.data.message;
                    this.alert.type = 'success';
                    this.alert.dismissCountDown = this.alert.dismissSecs;
                    this.$root.$emit('bv::hide::modal', 'suggestion-modal')
                    this.clearSuggestionForm();
                }).catch((error) => {
                    this.alert.text = error.message;
                    this.alert.type = 'danger';
                    this.alert.dismissCountDown = this.alert.dismissSecs;
                });
            },
            countDownChanged(dismissCountDown) {
                this.alert.dismissCountDown = dismissCountDown
            },
            clearSuggestionForm(){
                this.form.companyName = '';
                this.form.website = '';
                this.form.body = '';
            }
        }
    })

</script>
@endsection
