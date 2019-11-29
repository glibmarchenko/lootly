<div class="well m-t-20 reward-codes" v-if="showRewardCodes()">
    <div :class="{ 'loading' : loading || rewardCoupons.loading }" v-cloak>
        <div class="row p-b-10 m-b-5">
            <div class="col-md-8">
                <div class="form-group m-b-0">
                    <p class="bolder f-s-15 m-b-5">Reward Codes</p>
                    <label v-if="typeof rewardCoupons.available.data !== 'string'" class="light-font m-b-0">
                        @{{ rewardCoupons.available.data.length }} available
                    </label>
                    <label v-else class="light-font m-b-0">
                        0 available
                    </label>
                </div>
            </div>
            <div class="col-md-4 text-right">
                <a @click="showModalImportCoupons">
                    <span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Add Codes</span>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div :class="['custom-b-tabs', {'loading': rewardCoupons.available.loading || rewardCoupons.all.loading }]">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#available" aria-selected="true">Available</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#all"
                               aria-selected="false">All</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="available">
                            <sortable-table
                                    :hide-header="true"
                                    :contents="rewardCoupons.available.data"
                                    :page-size="10"
                                    :sort-by="'date'"
                                    :thead="[{text: 'Code', name: 'code'}, {text: 'Date', name: 'date'}]">
                                <template slot-scope="{row}">
                                    <td v-text="row.code"></td>
                                    <td>
                                        <span>@{{row.date | date-human}}</span>
                                    </td>
                                </template>
                            </sortable-table>
                        </div>
                        <div class="tab-pane" id="all" role="tabpanel" aria-labelledby="all-tab">
                            <sortable-table
                                    :hide-header="true"
                                    :contents="rewardCoupons.all.data"
                                    :page-size="10"
                                    :sort-by="'date'"
                                    :thead="[{text: 'Code', name: 'code'}, {text: 'Status', name: 'status'}, {text: 'Date', name: 'date'} ]"
                            >
                                <template slot-scope="{row}">
                                    <td v-text="row.code"></td>
                                    <td>
                                        <span v-if="row.status == 0">
                                            <span class="color-green">Available</span>
                                        </span>
                                        <span v-else>
                                            <span class="color-red">Redeemed</span>
                                        </span>
                                    </td>
                                    <td>
                                        <span>@{{row.date | date-human}}</span>
                                    </td>
                                </template>
                            </sortable-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
