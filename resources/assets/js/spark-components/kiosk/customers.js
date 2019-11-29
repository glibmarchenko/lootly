Vue.component('spark-kiosk-customers', {
    data: function () {
        return {
            alert: {
                type: 'success',
                title: '',
                messages: {},
            },
            columns: [
                'id',
                'name',
                'owner.email',
                'integrations',
                'plan.name',
                'owner.created_at',
                'actions',
            ],
            tableData: [],
            tableSearchQuery: '',
            searchQuery: '',
            planOptions: [
                {
                    value: { id: null, name: null },
                    text: 'n/a',
                    disabled: true
                },
            ],
            options: {
                skin: 'table',
                headings: {
                    'id': 'Merchant ID',
                    'name': 'Company Name',
                    'owner.email': 'Email Address',
                    'integrations': 'Platform',
                    'plan.name': 'Plan',
                    'owner.created_at': 'Date',
                    'actions': 'Actions',
                },
                columnsClasses: {
                    'actions': 'text-center',
                },
                sortable: [],
                filterable: [
                    'id',
                    'name',
                    'owner.email',
                    'integrations',
                    'plan.name',
                ],
                templates: {
                    'owner.email': function (h, row, index) {
                        return row.owner && row.owner.email ? row.owner.email : null;
                    },
                    'owner.created_at': function (h, row, index) {
                        if (row.owner && row.owner.created_at) {
                            return moment(row.owner.created_at).format('MM/DD/YYYY');
                        }
                        return null;
                    },
                    'plan.name': function (h, row, index) {
                        return row.plan && row.plan.name ? row.plan.name : 'n/a';
                    },
                    'integrations': function (h, row, index) {
                        let text = '';
                        if (row.integrations && row.integrations.length) {
                            row.integrations.forEach(function (value) {
                                if (text) {
                                    text += ', ';
                                }
                                text += value.title;
                            });
                        }
                        return text;
                    },
                },
                sortIcon: {
                    base:'fa',
                    up:'fa-sort-up',
                    down:'fa-sort-down',
                    is:'fa-sort',
                },
                texts:{
                    count: 'Showing {from} to {to} of {count} records|{count} records|One record',
                    first: 'First',
                    last: 'Last',
                    filter: 'Search:',
                    filterPlaceholder: 'company, email, platform, plan',
                    limit: 'Records:',
                    page: 'Page:',
                    noResults: 'No matching records',
                    filterBy: 'Filter by {column}',
                    loading: 'Loading...',
                    defaultOption: 'Select {column}',
                    columns: 'Columns'
                },
                perPage: 10,
                perPageValues: [0, 5, 10, 25, 50, 100],
                pagination: {
                    chunk: 5,
                },
                requestKeys: {
                    query: 'search',
                    limit: 'limit',
                    orderBy: 'orderBy',
                    ascending: 'ascending',
                    page: 'page',
                    byColumn: 'byColumn'
                },
                requestFunction: function (data) {
                    return axios.get(this.url, {
                        params: data
                    }).catch(function (e) {
                        this.dispatch('error', e);
                    }.bind(this));
                },
                requestAdapter(request) {
                    const data = request;

                    if('ascending' in data && 'orderBy' in data) {
                        data['sort'] = data.ascending ? data.orderBy : '-' + data.orderBy
                    }

                    return data;
                },
                responseAdapter: function(response) {
                    const { data, meta } = this.getResponseData(response);

                    return {
                        data,
                        count: meta.total,
                    }
                },
            }
        }
    },
    created: function () {
        this.getPlans();
    },
    methods: {
        onLoaded(data) {
            this.tableData = data.data;
        },
        onFilter(data) {
            this.tableSearchQuery = data;
        },
        onClickSearch() {
            const table = this.$refs.sparkKioskMerchantsTable;

            table.query = this.searchQuery;
            table.refresh();
        },
        clearModelAlert: function () {
            this.alert.title = '';
            this.alert.messages = {};
        },
        openModel: function (modelName) {
            this.clearModelAlert();
            this.$root.$emit('bv::show::modal', modelName);
        },
        closeModel: function (modelName) {
            this.clearModelAlert();
            this.$root.$emit('bv::hide::modal', modelName);
        },
        saveCustomer: function (data) {
            this.clearModelAlert();
            axios.put('/spark/kiosk/merchant/' + data.id, data)
                .then((response) => {
                    // handle success
                    const {type, message} = response.data;

                    this.alert.type = type;
                    this.alert.title = message;

                    // NOTE: Refresh the table
                    // this.$refs.sparkKioskMerchantsTable.refresh();
                })
                .catch((error) => {
                    const {message, errors} = error.response.data;

                    this.alert.type = 'danger';
                    this.alert.title = message;
                    this.alert.messages = errors;
                });
        },
        getPlans: function () {
            const planOptions = this.planOptions;

            axios.get('/spark/base-plans')
                .then(function (response) {
                    // handle success
                    let data;

                    if (response.data && response.data.data) {
                        data = response.data.data;

                        data.forEach(function (value) {
                            planOptions.push({
                                value,
                                text: value.name,
                            });
                        })
                    }

                })
                .catch(function (error) {
                    // handle error
                })
                .finally(function () {
                    // always executed
                });
        },
    }
});
