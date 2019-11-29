@extends('layouts.app')

@section('title', 'VIP Members')

@section('content')
    <div class="p-b-40 m-t-20 m-b-10" id="members-index">
        @if(\Illuminate\Support\Facades\Session::get('msg'))
            <b-alert v-cloak
                     :show="dismissCountDown"
                     dismissible
                     variant="danger"
                     @dismissed="dismissCountdown=0">{{\Illuminate\Support\Facades\Session::get('msg')}}
            </b-alert>
        @endif
        <div class="row m-t-20 p-b-10 section-border-bottom">
            <div class="col-md-4 col-12">
                <h3 class="page-title m-t-0 color-dark">VIP Members</h3>
            </div>
            <div class="col-md-8 col-12 text-right inline-block">
                <a class="bold color-blue f-s-15 m-r-15" :href="exportUrl">
                    <i class="icon-export f-s-19 m-r-5"></i> Export
                </a>
                <div class="btn-group date-range-buttons" role="group">
                    <button type="button" v-bind:class="[dateRange.selectedRange == 'custom' ? 'active' : '']"
                            id="dataRange" class="btn btn-default pull-right">Custom
                    </button>
                    <button type="button" @click="changeDateRange(30)" v-bind:class="[dateRange.selectedRange == '30 days' ? 'active' : '']" class="btn btn-default">30 Days
                    </button>
                    <button type="button" @click="changeDateRange(7)" v-bind:class="[dateRange.selectedRange == '7 days' ? 'active' : '']" class="btn btn-default">7 Days
                    </button>
                </div>
            </div>
        </div>
    <div :class="{'row m-t-25': true, 'loading': loading}">
        <div class="col-md-12">
            <div class="well well-table">

                <div class="table-header table-header-filters">
                    <div class="pull-left">
                        <label class="bold m-t-5">Display: </label>
                        <select class="form-control" v-model="pageSize">
                            <option selected>10</option>
                            <option>25</option>
                            <option>50</option>
                        </select>
                        <label class="bold m-t-5 m-l-25">Tier: </label>
                        <select class="form-control" v-model="tierSearch">
                            <option>All</option>
                            <option v-for="tier in tiersType" :value="tier" v-text="tier"></option>
                        </select>
                        <!-- <label class="bold m-t-5 m-l-25">
                            <span v-text="members.length"></span> Members
                        </label> -->
                    </div>
                    <div class="pull-right col-md-5 col-12">
                        <div class="input-group-icon">
                            <div class="input-icon"><span><i aria-hidden="true" class="fa fa-search"></i></span>
                                <input v-model="search" placeholder="Search by customer name, tier name" class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </div>

                <sortable-table 
                    v-if="!loading"
                    :hide-header="true"
                    :contents="sortedMembers"
                    sort-by="last_ordered"
                    direction="center"
                    :page-size="parseInt(pageSize)"
                    :thead="[{text: 'Customer Name', name: 'name'}, {text: 'Purchases', name: 'purchases'}, {text: 'Total Spend', name: 'total_spend'}, {text: 'Points Earned', name: 'points_earned'}, {text: 'VIP Tier', name: 'vip_tier'}, {text: 'Last Ordered', name: 'last_ordered'}]">
                        
                    <template slot-scope="{row}">
                        <td>
                            <a class="bold color-blue f-s-15" :href="'/customers/profile/' + row.id" v-text="row.name"></a>
                        </td>
                        <td v-text="row.purchases"></td>
                        <td>
                            @{{ row.total_spend | format-number | currency(currencySign) }}
                        </td>
                        <td v-text="row.points_earned"></td>
                        <td v-text="row.vip_tier"></td>
                        <td>
                            @{{row.last_ordered | date-format}}
                        </td>
                    </template> 
                </sortable-table>
                <div v-else class="loading" style="width: 40px;height: 40px;margin: 5px auto 0;"></div>
            </div>
        </div>
    </div>
</div>

    <!-- Export Members Modal -->
    <b-modal class="custom-modal" hide-footer id="export-members" title="Export Members" v-cloak>
        <form action="{{url('vip/members/export')}}">
            <div class="row m-b-10 m-t-10">
                <div class="col-md-12">
                    <label class="light-font">Select from the options below to export into a CSV file.</label>
                </div>
            </div>
            <div class="row m-b-10">
                <div class="col-md-3">
                    <label class="light-font">Program Tier:</label>
                </div>
                <div class="col-md-9">
                    <b-form-checkbox name="tier[]" v-model="allTiers" @change="exportAll"> All</b-form-checkbox>
                    <b-form-checkbox-group id="ch1" stacked v-model="selectedTiers" :options="tiersType"></b-form-checkbox-group>
                </div>
            </div>
            <div class="row m-t-20 p-b-10 p-t-20 border-top">
                <div class="col-md-6 offset-md-3">
                    <button {{--href="{{route('vip.members.export')}}"--}}
                           class=" btn modal-btn-lg btn-block btn-success btn-glow">Export
                        Members
                    </button>
                </div>
            </div>
        </form>
    </b-modal>
</div>

@endsection

@section('scripts')
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>

<script>
    var start = moment().subtract(30, 'days');
    var end = moment();

    var members = new Vue({
        el: '#members-index',
        data: {
            search: '',
            point: '',
            tierSearch: 'All',
            members: {!! json_encode($members) !!},
            currentSort: 'date',
            currentSortDir: 'desc',
            pageSize: 10,
            currentPage: 1,
            sortedLength: 0,
            tiersType: {!! json_encode($tiersType) !!},
            selectedTiers: [],
            dateRange: {
                selectedRange: '30 days',
                start: moment().startOf('day').add(1, 'day').subtract(30, 'days').format('DD-MM-YYYYTHH:mm:00'),
                end: moment().startOf('day').add(1, 'day').format('DD-MM-YYYYTHH:mm:00')
            },
            allTiers: false,
            text: '',
            type: '',
            dismissSecs: 5,
            dismissCountDown: 5,
            loading: false,
            currencySign: '{!! $currencySign !!}',
        },
        created: function () {
            // this.getMembers();
            // this.getTiers();
        },
        methods: {
            getMembers: function () {
                axios.get("{{  route('vip.members.data')  }}").then((response) => {
                    console.log('members ', response);
                    this.members = response.data.members;
                    this.loading = false;
                }).catch((error) => {
                    this.loading = false;
                });
            },
            getTiers: function () {
                axios.get("{{route('vip.tiers.data')}}").then((response) => {
                    console.log('tiers ', response.data);
                    this.tiers = response.data.tiers;
                    this.tiers.forEach((tier) => {
                        this.tiersType.push(tier.name);
                    });
                    console.log(this.tiersType);
                });
            },
            getDataForRange: function () {
                this.loading = true;
                url = `{{ route("vip.members.data") }}?start=${this.dateRange.start}&end=${this.dateRange.end}`;
                axios.get(url).then((res) => {
                    if (res.data.members.length == 0) {
                        this.status = 'There is no members to show!';
                    } else {
                        this.status = 'Members Activity';
                    }
                    this.members = res.data.members;
                    this.loading = false;
                }).catch((error) => {
                    this.loading = false;
                    this.status = 'Error retrieving customer data, contact Lootly Support';
                    console.log(error)
                });
            },
            sort: function (s) {
                if (s === this.currentSort) {
                    this.currentSortDir = this.currentSortDir === 'asc' ? 'desc' : 'asc';
                }
                this.currentSort = s;
            },
            nextPage: function () {
                if ((this.currentPage * this.pageSize) < this.sortedLength) this.currentPage++;
            },
            prevPage: function () {
                if (this.currentPage > 1) this.currentPage--;
            },
            showPage: function (no) {
                this.currentPage = no;
            },
            dateFormat: function (date) {
                return moment(date).format('MM/D/YYYY');

            },
            exportAll: function () {
                this.selectedTiers = !this.allTiers ? this.tiersType : [];
            },
            countDownChanged(dismissCountDown) {
                this.alert.dismissCountDown = dismissCountDown
            },
            changeDateRange: function (range) {
                if(range == 7){
                    this.dateRange.selectedRange = '7 days';
                    this.dateRange.start = moment().startOf('day').add(1, 'day').subtract(7, 'days').format('DD-MM-YYYYTHH:mm:00');
                    this.dateRange.end = moment().startOf('day').add(1, 'day').format('DD-MM-YYYYTHH:mm:00');
                }else if (range == 30){
                    this.dateRange.selectedRange = '30 days';
                    this.dateRange.start = moment().startOf('day').add(1, 'day').subtract(30, 'days').format('DD-MM-YYYYTHH:mm:00');
                    this.dateRange.end = moment().startOf('day').add(1, 'day').format('DD-MM-YYYYTHH:mm:00');
                }
                this.getDataForRange()
            }

        },
        computed: {
            pagesNo: function () {
                return Math.ceil(this.sortedLength / this.pageSize);
            },
            sortedMembers: function () {
                var members = this.members;
                if (this.tierSearch != 'All') {
                    this.currentPage = 1;
                    members = members.filter(
                        item => item.vip_tier.toUpperCase().includes(this.tierSearch.toUpperCase())
                    );
                }
                if (this.search) {
                    this.currentPage = 1;
                    members = members.filter(
                        item => item.name.toUpperCase().includes(this.search.toUpperCase()) || item.vip_tier.toUpperCase().includes(this.search.toUpperCase())
                    );
                }
                if(members.length == 0){
                    members = 'No VIP members for the selected time period.';
                }
                return members;
            },
            exportUrl: function(){
                return `{{ route('vip.members.export') }}?start=${this.dateRange.start}&end=${this.dateRange.end}&tier=${this.tierSearch}&search=${this.search}`
            }
        }
    })

function cb(start, end) {
    members.dateRange.selectedRange = 'custom';
    start = moment(start).startOf('day');
    end = moment(end).startOf('day');
    end.add(1, 'day'); // it affects on ranges declared below
    members.dateRange.start = start.format('DD-MM-YYYYTHH:mm:00');
    members.dateRange.end = end.format('DD-MM-YYYYTHH:mm:00');
    members.getDataForRange();
}

$('#dataRange').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD'
        },
        startDate: start,
        endDate: end,
        ranges: {
                'Today': [moment().startOf('day'), moment().endOf('day')],
                'Yesterday': [moment().startOf('day').subtract(1, 'days'), moment().endOf('day').subtract(1, 'days')],
                'Last 7 Days': [moment().startOf('day').subtract(7, 'days'), moment().subtract(1, 'days').endOf('day')],
                'Last 30 Days': [moment().startOf('day').subtract(30, 'days'), moment().subtract(1, 'days').endOf('day')],
                'This Month': [moment().startOf('month'), moment().endOf('day')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
    }, cb);
</script>
@endsection