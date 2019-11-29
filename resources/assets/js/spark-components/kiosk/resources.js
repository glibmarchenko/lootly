Vue.component('spark-kiosk-resources', {
    data: function () {
        return {
            columns: [
                'category.name',
                'title',
                'created_at',
                'actions',
            ],
            options: {
                skin: 'table',
                headings: {
                    'category.name': 'Category',
                    'title': 'Article Title',
                    'created_at': 'Date',
                    'actions': 'Actions',
                },
                columnsClasses: {
                    'actions': 'text-center',
                },
                sortable: [
                    'title',
                    'created_at',
                ],
                filterable: false,
                templates: {
                    'category.name': function (h, row, index) {
                        return row.category && row.category.name ? row.category.name : null;
                    },
                    'created_at': function (h, row, index) {
                        if (row.created_at) {
                            return moment(row.created_at).format('MM/DD/YYYY');
                        }
                        return null;
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
                    filterPlaceholder: 'Search query',
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
});
