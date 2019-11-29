<spark-update-store :user="user" inline-template>
    <div class="card card-default">
        <div class="card-header">{{__('Store Information')}}</div>

        <div class="card-body">
            <div class="form-group row">
                <label class="col-md-4 col-form-label text-md-right">{{__('Store')}}</label>

                <div class="col-md-6 form-group">
                    <select name="merchants" id="" class="form-control" @change.prevent="updateMerchant">
                        <option :value="merchant.id" v-for="merchant in merchants">
                            @{{merchant.name}}
                        </option>
                    </select>

                </div>
            </div>
        </div>
    </div>
</spark-update-store>
