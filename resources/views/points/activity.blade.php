@extends('layouts.app')

@section('title', 'Points Activity')

@section('content')
    <div id="points-activity" class="p-b-40 loader" v-cloak>
        <div class="row m-t-20 p-b-10 section-border-bottom">
            <div class="col-md-6 col-12">
                <h3 class="page-title m-t-0 color-dark">Points Activity</h3>
            </div>
            <div class="col-md-6 col-12 text-right table-actions">
                <div class="btn-group date-range-buttons" role="group" aria-label="Basic example">
                    <a :href="exportActivityUrl" class="bold color-blue f-s-15 m-r-20 m-t-5">
                        <i class="icon-export f-s-19 m-r-5"></i> Export
                    </a>
                    <button type="button" v-bind:class="[dateRange.selectedRange == 'custom' ? 'active' : '']"
                            id="dataRange" class="btn btn-default pull-right">Custom
                    </button>
                    <button type="button" @click="changeDateRange(30)"
                            v-bind:class="[dateRange.selectedRange == '30 days' ? 'active' : '']"
                            class="btn btn-default">30 Days
                    </button>
                    <button type="button" @click="changeDateRange(7)"
                            v-bind:class="[dateRange.selectedRange == '7 days' ? 'active' : '']"
                            class="btn btn-default">7 Days
                    </button>
                </div>
            </div>
        </div>
        <div class="row m-t-25">
            <div class="col-md-12">
                <div class="well well-table">

                    <div class="table-header table-header-filters">
                        <div class="pull-left">
                        </div>
                        <div class="pull-right">
                            <div class="input-group-icon">
                                <div class="input-icon"><span><i aria-hidden="true" class="fa fa-search"></i></span>
                                </div>
                                <input v-model="searchText" placeholder="Search" class="form-control" type="text">
                            </div>
                        </div>
                        <label class="bold">Display: </label>
                        <select class="form-control" v-model="getPageSize">
                            <option v-for="item in displayOptions" :value="item" v-text="item">
                            </option>
                        </select>

                        <label class="bold m-t-5 m-l-25">Activity: </label>

                        <select class="form-control w-160" v-model="actionSearch">
                            <option>All</option>
                            <option v-for="action in getActions" v-text="action"></option>
                        </select>
                        
                        <label class="bold m-t-5 m-l-25">Points: </label>
                        <select class="form-control" v-model="typeSearch">
                            <option>All</option>
                            <option value="earned">Earned</option>
                            <option value="spent">Spent</option>
                        </select>
                    </div>
                    <div v-if="searching">
                        <tbody>
                            <tr>
                                <td colspan="5">
                                    <div v-html="loader">
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </div>
                    <custom-table
                        v-else
                        :hide-header="true"
                        :contents="customers"
                        :sort-by="currentSort"
                        :sort-dir="currentSortDir"
                        direction="center"
                        :page-size="getPageSize"
                        :page-num="currentPage"
                        :total="totalActivities"
                        :skip-sort="true"
                        v-on:get-content="data => { getContent(data) }"
                        :thead="[{text: 'Customer Name', name: 'customer_name'}, {text: 'Activity', name: 'action_name'}, {text: 'Points', name: 'points.point_value'}, {text: 'Date', name: 'points.created_at'}]">
                            
                        <template slot-scope="{row}">
                            <td>
                                <a class="bold color-blue f-s-15" :href="'/customers/profile/' + row.id" v-text="row.name"></a>
                            </td>
                            <td v-text="row.action_name"></td>
                            <td>
                                <span v-if="row.point_value > 0">
                                    <span class="badge badge-pill badge-success">+<span v-text="row.point_value"></span></span>
                                </span>
                                <span v-else-if="row.point_value < 0">
                                    <span class="badge badge-pill badge-danger" v-text="row.point_value"></span>
                                </span>
                                <span v-else>
                                    <span class="badge badge-pill" v-text="row.point_value"></span>
                                </span>
                            </td>
                            <td>@{{row.created_at | date-human}}</td>
                        </template> 

                    </custom-table>
                </div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css"/>
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <script>
        var start = moment().subtract(7, 'days');
        var end = moment();

        var customers = new Vue({
            el: '#points-activity',
            data: {
                search: '',
                customers: [],
                actions: [
                        "Admin",
                    @foreach($merchantActions as $action)
                        "{!! addslashes($action->action_name) !!}",
                    @endforeach
                ],
                rewards: [
                    @foreach($merchantRewards as $reward)
                        "{!! addslashes($reward->reward_name) !!}",
                    @endforeach
                ],
                currentSort: 'points.created_at',
                currentSortDir: 'desc',
                pageSize: 10,
                currentPage: 1,
                dateRange: {
                    selectedRange: '7 days',
                    start: moment().startOf('day').subtract(7, 'days'),
                    end: moment().endOf('day')
                },
                loader: '<div class="loading" style="width: 40px;height: 40px;margin: 20px auto;"></div>',
                searching: false,
                actionSearch: 'All',
                typeSearch: '{{ request('typeSearch') ?: 'All' }}',
                displayOptions: [5, 10, 25, 50, 100],
                totalActivities: 0,
                searchTimeout: null,
            },
            created: function () {
                this.getContent();
            },
            methods: {
                sort: function (s) {
                    if (s === this.currentSort) {
                        this.currentSortDir = this.currentSortDir === 'asc' ? 'desc' : 'asc';
                    }
                    this.currentSort = s;
                },
                dateFormat: function (date) {
                    return moment(date).fromNow();

                },
                changeDateRange: function (range) {
                    this.actionSearch = 'All';
                    this.typeSearch = 'All';
                    if (range == 7) {
                        this.dateRange.selectedRange = '7 days';
                        this.dateRange.start = moment().startOf('day').subtract(7, 'days');
                        this.dateRange.end = moment().endOf('day');
                    } else if (range == 30) {
                        this.dateRange.selectedRange = '30 days';
                        this.dateRange.start = moment().startOf('day').subtract(30, 'days');
                        this.dateRange.end = moment().endOf('day');
                    }
                    this.getContent();
                },
                alphanumSort(array) {
                    for (var z = 0, t; t = array[z]; z++) {
                        array[z] = [];
                        var x = 0, y = -1, n = 0, i, j;

                        while (i = (j = t.charAt(x++)).charCodeAt(0)) {
                        var m = (i == 46 || (i >=48 && i <= 57));
                        if (m !== n) {
                            array[z][++y] = "";
                            n = m;
                        }
                        array[z][y] += j;
                        }
                    }

                    array.sort(function(a, b) {
                        for (var x = 0, aa, bb; (aa = a[x]) && (bb = b[x]); x++) {
                            aa = aa.toLowerCase();
                            bb = bb.toLowerCase();
                            if (aa !== bb) {
                                var c = Number(aa), d = Number(bb);
                                if (c == aa && d == bb) {
                                    return c - d;
                                } else 
                                    return (aa > bb) ? 1 : -1;
                            }
                        }
                        return a.length - b.length;
                    });

                    for (var z = 0; z < array.length; z++) {
                        if(array[z] instanceof Array)
                            array[z] = array[z].join("");
                    }
                    return array;
                },
                getContent: function (data = null) {
                    this.searching = true;
                    if(data) {
                        this.currentPage = data.page ? data.page : this.currentPage;
                        this.currentSort = data.sortBy ? data.sortBy : this.currentSort;
                        this.currentSortDir = data.sortDir ? data.sortDir : this.currentSortDir;
                        this.dateRange.start = data.start ? data.start : this.dateRange.start;
                        this.dateRange.end = data.end ? data.end : this.dateRange.end;
                    }
                    let url = "{!! route('points.activity.get_data') !!}";
                    let filtersData = {
                            start:    this.formattedDates.start,
                            end:      this.formattedDates.end,
                            sort_by:  this.currentSort,
                            sort_dir: this.currentSortDir,
                            limit:    this.pageSize,
                            offset:   this.offset,
                            type:     this.typeSearch,
                            search:   this.search,
                            action:   this.actionSearch,
                    };
                    
                    axios.post(url, filtersData).then(response => {
                        if(response.data.actions.length > 0){
                                this.customers = response.data.actions;
                            } else {
                                this.customers = "No points activity for the selected time period.";
                            }
                        this.totalActivities = response.data.total;
                        this.searching = false;
                    }).catch((error) => {
                        console.error(error);
                        this.searching = false;
                    });
                }
            },
            computed: {
                getPageSize: {
                    get: function(){
                        return Number.parseInt(this.pageSize);
                    },
                    set: function (val) {
                        this.currentPage = 1;
                        this.pageSize = val;
                        this.getContent();
                        return this.pageSize;
                    }
                },
                getActions: function(){
                    return this.alphanumSort(this.actions).concat(this.alphanumSort(this.rewards));
                },
                exportActivityUrl: function() {
                    return `{{route('points.activity.export')}}?point=${this.typeSearch}`+
                        `&action=${this.actionSearch}`+
                        `&start=${this.formattedDates.start}`+
                        `&end=${this.formattedDates.end}`+
                        `&search=${this.search}`;
                },
                offset: function(){
                    return this.pageSize * (this.currentPage - 1)
                },
                formattedDates: function () {
                    return {
                        start: this.dateRange.start.format('YYYY-MM-DDTHH:mm:ss'),
                        end: this.dateRange.end.format('YYYY-MM-DDTHH:mm:ss'),
                    };
                },
                searchText: {
                    get: function () {
                        return this.search;
                    },
                    set: function (val) {
                        clearTimeout(this.searchTimeout);
                        if(val.length < 1 || val.length > 2 ) {
                            this.searchTimeout = setTimeout(() => {
                            this.getContent();
                            }, 1000);
                        }
                        return this.search = val;
                    }
                },
            },
            watch: {
                typeSearch: function () {
                    this.getContent();
                },
                actionSearch: function () {
                    this.getContent();
                }
            }
        })

        /* Date Range Scripts */
        function cb(start, end) {
            customers.dateRange.selectedRange = 'custom';
            start = moment(start).startOf('day');
            end = moment(end).endOf('day');
            customers.dateRange.start = start;
            customers.dateRange.end = end;
            customers.actionSearch = 'All';
            customers.typeSearch = 'All';
            customers.getContent();
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
