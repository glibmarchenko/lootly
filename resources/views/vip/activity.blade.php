@extends('layouts.app')

@section('title', 'VIP Activity')

@section('content')
<div class="p-b-40 m-t-20 m-b-10" id="vip-activity">
        @if(\Illuminate\Support\Facades\Session::get('msg'))
            <b-alert v-cloak
                     :show="dismissCountDown"
                     dismissible
                     variant="danger"
                     @dismissed="dismissCountdown=0">{{\Illuminate\Support\Facades\Session::get('msg')}}
            </b-alert>
        @endif
        <div class="row m-t-20 p-b-10 section-border-bottom">
            <div class="col-md-6 col-12 ">
                <h3 class="page-title m-t-0 color-dark">VIP Activity </h3>
            </div>
            <div class="col-md-6 col-12 text-right table-actions">
                <div class="btn-group date-range-buttons" role="group">
                    <a class="bold color-blue f-s-15 m-r-20 m-t-5"
                        :href="exportUrl"
                    >
                        <i class="icon-export f-s-19 m-r-5"></i> Export
                    </a>
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
                            <option>5</option>
                            <option selected>10</option>
                            <option>25</option>
                            <option>50</option>
                            <option>100</option>
                        </select>
                    </div>
                    <div class="pull-right col-md-5 col-12">
                        <div class="input-group-icon">
                            <div class="input-icon"><span><i aria-hidden="true" class="fa fa-search"></i></span>
                                <input v-model="search" placeholder="Search by customer name, tier, activity" class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <table class="table">
                    <thead>
                        <tr class="f-s-15">
                            <th class="bold color-dark-grey pointer" @click="sort('name')">Customer Name</th>
                            <th class="bold color-dark-grey pointer" @click="sort('activity')">Activity</th>
                            <th class="bold color-dark-grey pointer" @click="sort('current_tier')">Current Tier</th>
                            <th class="bold color-dark-grey pointer" @click="sort('previous_tier')">Previous Tier</th>
                            <th class="bold color-dark-grey pointer" @click="sort('date')">Date</th>
                        </tr>
                    </thead>
                    <tbody v-if="sortedCustomers.length == 0">
                        <tr>
                            <td colspan="5">
                                <div v-html="status"></div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else v-cloak>
                        <tr v-for="customer in sortedCustomers">
                            <td>
                                <a class="bold color-blue f-s-15" v-text="customer.name"></a>
                            </td>
                            <td v-text="customer.activity ? customer.activity : 'No activity'"></td>
                            <td v-text="customer.current_tier"></td>
                            <td v-text="customer.previous_tier ?  customer.previous_tier : 'No tier'"></td>
                            <td> @{{customer.date | date-format}}</td>
                        </tr>
                    </tbody>
                </table> -->
                <!-- <div class="table-footer p-b-15" v-cloak>
                    <div class="row m-t-5">
                        <label class="col-md-4">
                            Showing <span class="bolder" v-text="customers.length != 0 ? ((currentPage - 1) * pageSize) +1 : 0"></span>
                            to <span class="bolder" v-text="currentPage * pageSize > sortedLength ? sortedLength: currentPage * pageSize "></span>
                            of <span class="bolder" v-text="sortedLength"></span>
                        </label>

                        <div class="col-md-4 text-center">
                            <div class="table-pagination">
                                <a @click="prevPage" aria-label="Previous">
                                    <span aria-hidden="true" class="arrow left"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <span class="numbers" v-for="n in pagesNo">
                                    <a @click="showPage(n)" v-bind:class="[currentPage == n ? 'active' : '']">
                                        <span v-text="n"></span>
                                    </a>
                                </span>
                                <a @click="nextPage" aria-label="Next">
                                    <span aria-hidden="true" class="arrow right"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div> -->

                <sortable-table 
                    v-if="!loading"
                    :hide-header="true"
                    :contents="sortedCustomers"
                    sort-by="date"
                    direction="center"
                    :page-size="parseInt(pageSize)"
                    :thead="[{text: 'Customer Name', name: 'name'}, {text: 'Activity', name: 'activity'}, {text: 'Current Tier', name: 'current_tier'}, {text: 'Previous Tier', name: 'previous_tier'}, {text: 'Join Date', name: 'date'}]">
                        
                    <template slot-scope="{row}">
                        <td>
                            <a class="bold color-blue f-s-15" :href="'/customers/profile/' + row.customer_id" v-text="row.name"></a>
                        </td>
                        <td v-text="row.activity"></td>
                        <td v-text="row.current_tier"></td>
                        <td v-text="row.previous_tier"></td>
                        <td>@{{row.date | date-human}}</td>
                    </template> 

                </sortable-table>
                <div v-else v-html="status">
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@section('scripts')
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script>
    var start = moment().subtract(30, 'days');
    var end = moment();

    var customers = new Vue({
        el: '#vip-activity',
        data: {
            search: '',
            customers: {!! json_encode($activities) !!},
            currentSort: '',
            currentSortDir: 'asc',
            pageSize: 10,
            currentPage: 1,
            sortedLength: 0,
            dateRange: {
                selectedRange: '30 days',
                start: moment().startOf('day').add(1, 'day').subtract(30, 'days').format('DD-MM-YYYYTHH:mm:00'),
                end: moment().startOf('day').add(1, 'day').format('DD-MM-YYYYTHH:mm:00')
            },
            text: '',
            type: '',
            dismissSecs: 5,
            dismissCountDown: 5,
            status: '<div class="loading" style="width: 40px;height: 40px;margin: 5px auto 0;"></div>',
            loading: false,
        },
        methods: {
            getDataForRange: function () {
                this.loading = true;
                url = `{{ route("vip.activity.data") }}?start=${this.dateRange.start}&end=${this.dateRange.end}`;
                axios.get(url).then((res) => {
                    if (res.data.activities.length == 0) {
                        this.status = 'There is no activity to show!';
                    } else {
                        this.status = 'User Activity';
                    }
                    this.customers = res.data.activities;
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
                return moment(date).fromNow();

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
            sortedCustomers: function () {
                var customers = this.customers;

                if (this.search) {
                    this.currentPage = 1;
                    customers = customers.filter(
                        (item) => {
                            let flag = false;
                            if(item.name.toUpperCase().includes(this.search.toUpperCase()) || item.activity.toUpperCase().includes(this.search.toUpperCase())) {
                                flag = true;
                            }
                            if(flag)
                                return flag;
                            
                            if(item.current_tier){
                                if(item.current_tier.toUpperCase().includes(this.search.toUpperCase())){
                                    flag = true;
                                }
                            }
                            if(item.previous_tier) {
                                if(item.previous_tier.toUpperCase().includes(this.search.toUpperCase())){
                                    flag = true;
                                }
                            }
                            return flag;
                        }
                    );
                }
                if(customers.length == 0){
                    customers = 'No VIP activity for the selected time period.';
                }

                return customers;
            },
            exportUrl: function(){
                let start = this.dateRange.start;
                let end = this.dateRange.end;
                return `{{ route('vip.activity.export') }}?start=${start}&end=${end}&search=${this.search}`
            }
        }
    })

    /* Date Range Scripts */
    function cb(start, end) {
        customers.dateRange.selectedRange = 'custom';
        start = moment(start).startOf('day');
        end = moment(end).startOf('day');
        end.add(1, 'day'); // it affects on ranges declared below
        customers.dateRange.start = start.format('DD-MM-YYYYTHH:mm:00');
        customers.dateRange.end = end.format('DD-MM-YYYYTHH:mm:00');
        customers.getDataForRange();
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