<spark-update-sopify :user="user"  :billable-type="billableType" inline-template>
    <div class="shopify shopify-default">
        <!-- Update Payment Method Heading -->
        <div class="shopify-header">



            <div class="clearfix"></div>
        </div>

        <div class="shopify-body">
            <div class="alert alert-success" v-if="form.successful">
                {{__('Your contact information has been updated!')}}
            </div>

            <form role="form">

                <div class="form-group row">
                    <label for="name" class="col-md-4 col-form-label text-md-right" >{{__('Shop Domain')}}</label>

                    <div class="col-md-6">
                        <input type="text" class="form-control" v-model="shopifyForm.shop_domain">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="name" class="col-md-4 col-form-label text-md-right">{{__('API Key')}}</label>

                    <div class="col-md-6">
                        <input type="text" class="form-control" v-model="shopifyForm.api_key">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="name" class="col-md-4 col-form-label text-md-right">{{__('API Secret')}}</label>

                    <div class="col-md-6">
                        <input type="password" class="form-control"v-model="shopifyForm.api_secret" >
                    </div>
                </div>



                <!-- Update Button -->
                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary" @click.prevent="update" >
                            {{--<span v-if="form.busy">--}}
                                {{--<i class="fa fa-btn fa-spinner fa-spin"></i> {{__('Updating')}}--}}
                            {{--</span>--}}

                            {{--<span v-else>--}}
                                {{__('Update')}}
                            {{--</span>--}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</spark-update-sopify>
