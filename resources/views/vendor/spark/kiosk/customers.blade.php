<spark-kiosk-customers inline-template>
    <div>
        <div class="card card-default card-spark-kiosk-customers">
            <div class="card-body px-0 px-0">

                <v-server-table
                        ref="sparkKioskMerchantsTable"
                        :url="'{{ route('spark.kiosk.merchants.get') }}'"
                        :columns="columns"
                        :options="options"
                        @loaded="onLoaded"
                        @filter="onFilter"
                >
                    <div slot="beforeFilter">
                        <h4 class="mb-0 ml-3">{{__('Customers')}}</h4>
                    </div>
                    <div slot="afterFilter" class="table-search-box">
                        <div class="col">
                            <div class="input-group">
                                <input v-model="searchQuery"
                                       @keyup.enter="onClickSearch"
                                       type="text"
                                       class="form-control"
                                       placeholder="{{__('company, email, platform, plan')}}"
                                       aria-label="{{__('company, email, platform, plan')}}"
                                       aria-describedby="button-search"
                                >
                                <div class="input-group-append">
                                    <button @click="onClickSearch" id="button-search" class="btn btn-outline-secondary" type="button">
                                        {{__('Search')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div slot="beforeTable">
                        <div class="w-100 mb-0 border-bottom"></div>
                    </div>
                    <div slot="actions" slot-scope="props">
                        <button class="btn btn-link" :disabled="! props.row.owner" @click="openModel('bv-modal-edit-customer-' + props.row.id)">
                            {{__('Edit')}}
                        </button>

                        <b-modal v-if="props.row.owner"
                                 :id="'bv-modal-edit-customer-' + props.row.id"
                                 title="{{__('Edit Customer')}}"
                                 body-class="px-0 py-0"
                                 modal-class="spark-kiosk-modal"
                                 hide-footer
                        >
                            <div>
                                <div class="p-3 text-left">
                                    <div class="mt-1 mb-3">
                                        {{__('You are now editing')}} @{{ props.row.name }}
                                    </div>
                                    <div class="my-0">
                                        <form>
                                            <div v-if="alert.title || alert.messages.length"
                                                 class="alert"
                                                 :class="'alert-' + alert.type"
                                                 role="alert"
                                            >
                                                <p class="my-0">@{{ alert.title  }}</p>
                                                <ul class="my-0" v-for="(items, name) in alert.messages">
                                                    <li v-for="(message, index) in items">
                                                        @{{ message }}
                                                    </li>
                                                </ul>
                                            </div>

                                            <b-form-group
                                                    label="{{__('Email Address')}}"
                                                    :label-for="'email-input-' + props.row.id"
                                                    label-cols-sm="4"
                                                    label-cols-lg="3"
                                            >
                                                <b-form-input
                                                        :id="'email-input-' + props.row.id"
                                                        v-model="props.row.owner.email"
                                                        type="email"
                                                        required
                                                ></b-form-input>
                                            </b-form-group>

                                            <b-form-group
                                                    label="{{__('Plan')}}"
                                                    :label-for="'plan-input-' + props.row.id"
                                                    label-cols-sm="4"
                                                    label-cols-lg="3"
                                            >
                                                <b-form-select
                                                        :id="'plan-input-' + props.row.id"
                                                        v-model="props.row.plan"
                                                        :options="planOptions"
                                                        required
                                                ></b-form-select>
                                            </b-form-group>
                                        </form>
                                    </div>
                                </div>
                                <div class="py-3 border-top text-center">
                                    <button @click="saveCustomer(props.row)" type="button" class="btn btn-primary mr-3">
                                        {{__('Save')}}
                                    </button>
                                    <button @click="closeModel('bv-modal-edit-customer-' + props.row.id)" type="button" class="btn btn-secondary mx-3">
                                        {{__('Cancel')}}
                                    </button>
                                </div>
                            </div>
                        </b-modal>
                    </div>
                </v-server-table>

            </div>
        </div>
    </div>
</spark-kiosk-customers>
