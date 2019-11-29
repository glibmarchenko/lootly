<template>
    <section id="" class="">
        <div class="table-header table-header-filters" v-if="!hideHeader">
            <p class="bold f-s-16" 
               v-if="title"
               v-text="title"></p>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr class="f-s-15">
                        <th 
                            :class="[currentSort == th.name ? 'active': '', currentSortDir == 'desc' ? '' : 'asc', skipSort ? 'bold color-dark-grey' : 'pointer bold color-dark-grey']"
                            v-for="th in thead" @click="sort(th.name)">
                            <span v-text="th.text"></span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="typeof(contents) != 'string' && contents.length == 0">
                        <td :colspan="thead.length">
                            <div class="loading" style="width: 40px;height: 40px;margin: 5px auto 0;"></div>
                        </td>
                    </tr>
                    <tr v-else-if="typeof(contents) != 'string' && contents.length != 0" v-for="content in contents">
                        <slot :row="content"></slot>
                    </tr>
                    <tr v-else>
                        <td :colspan="thead.length">
                            <p v-if="contents == ''">There are no records to show</p>
                            <p v-else v-html="contents"></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="table-footer p-b-15" v-if="typeof(contents) != 'string'">
            <div class="row m-t-5">
                <label class="col-md-4">
                    Showing <span class="bolder" v-text="total != 0 ? ((currentPage - 1) * pageSize) + 1 : 0"></span>
                    to <span class="bolder" v-text="currentPage * pageSize > total ? total : currentPage * pageSize"></span>
                    of <span class="bolder" v-text="total"></span>
                </label>

                <div :class="direction == 'center' ? 'col-md-4 text-center' : 'offset-md-2 col-md-6 text-right'">
                    <div class="table-pagination">
                        <a v-if="currentPage > 1" @click="prevPage" aria-label="Previous">
                            <span aria-hidden="true" class="arrow left"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <span class="numbers" v-for="n in pagesNo">
                        <a @click="showPage(n)" v-bind:class="[currentPage == n ? 'active' : '']">
                            <span v-text="n"></span>
                        </a>
                    </span>
                        <a v-if="currentPage != totalPageNum" @click="nextPage" aria-label="Next">
                            <span aria-hidden="true" class="arrow right"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        props: {
            title: {
                default: '',
                type: [String, Number]
            },
            thead: [Array],
            contents: [Array, Object, String],
            'page-size': {
                default: 10,
                type: Number
            },
            'page-num': {
                type: Number
            },
            'hide-header': {
                default: false,
                type: Boolean
            },
            'sort-by': {
                default: 'created_at',
                type: String
            },
            'sort-dir': {
                default: 'desc',
                type: String,
            },
            direction: {
                default: 'right',
                type: String
            },
            total: {
                type: Number,
                required: true
            },
            'skip-sort': {
                type: Boolean,
                default: false,
            }
        },
        data: function () {
            return {
                currentSort: this.sortBy,
                currentSortDir: this.sortDir,
                currentPage: this.pageNum,
                formattedUrl: this.url
            }
        },
        methods: {
            sort: function (s) {
                if(this.skipSort){
                    return
                }
                if (s === this.currentSort) {
                    this.currentSortDir = this.currentSortDir === 'asc' ? 'desc' : 'asc';
                }
                this.currentSort = s;
                this.getContent();
            },
            nextPage: function () {
                if ((this.currentPage * this.pageSize) < this.total){
                    this.currentPage++;
                    this.getContent();
                }
            },
            prevPage: function () {
                if (this.currentPage > 1){
                    this.currentPage--;
                    this.getContent();
                }
            },
            showPage: function (no) {
                this.currentPage = no;
                this.getContent();
            },
            getContent: function(){
                return this.$emit('get-content', {
                    page: this.currentPage,
                    sortBy: this.currentSort == this.sortBy ? null : this.currentSort,
                    sortDir: this.currentSortDir == this.sortDir ? null : this.currentSortDir
                });
            }
        },
        computed: {
            pagesNo: function () {
                let responseArray = [];
                if(this.totalPageNum <= 5){
                    for(var i = 1; i <= this.totalPageNum; i++){
                        responseArray.push(i);
                    }
                    return responseArray;
                }
                if(this.currentPage < 3) {
                    for(var i = 1; i <= 5; i++) {
                        responseArray.push(i);
                    }
                    return responseArray
                }
                if(this.totalPageNum - this.currentPage < 3) {
                    for(var i = 5; i >= 1; i--) {
                        responseArray.push(this.totalPageNum - i);
                    }
                    return responseArray;
                }
                for(var i = 2; i >= -2; i--) {
                    responseArray.push(this.currentPage - i);
                }
                return responseArray;
                // return Math.ceil(this.total / this.pageSize);
            },
            totalPageNum: function() {
                return Math.ceil(this.total / this.pageSize);
            }
        },
        watch: {
            sortBy: function() {
                this.currentSort = this.sortBy;
                this.getContent();
            },
        }
    }
</script>
