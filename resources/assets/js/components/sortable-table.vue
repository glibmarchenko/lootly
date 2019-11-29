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
                        <th class="bold color-dark-grey pointer" 
                            :class="[currentSort == th.name ? 'active': '', currentSortDir == 'desc' ? '' : 'asc' ]" 
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
                    <tr v-else-if="typeof(contents) != 'string' && contents.length != 0" v-for="content in sortedContents">
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
                    Showing <span class="bolder" v-text="contents.length != 0 ? ((currentPage - 1) * pageSize) +1 : 0"></span>
                    to <span class="bolder" v-text="currentPage * pageSize > contents.length ? contents.length: currentPage * pageSize "></span> of <span class="bolder" v-text="contents.length"></span>
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
            'hide-header': {
                default: false,
                type: Boolean
            },
            'sort-by': {
                default: '',
                type: String
            },
            'sort-dir': {
                default: 'desc',
                type: String,
            },
            direction: {
                default: 'right',
                type: String
            }
        },
        data: function () {
            return {
                currentSort: this.sortBy,
                currentSortDir: this.sortDir,
                currentPage: 1
            }
        },
        methods: {
            sort: function (s) {
                if (s === this.currentSort) {
                    this.currentSortDir = this.currentSortDir === 'asc' ? 'desc' : 'asc';
                }
                this.currentSort = s;
            },
            nextPage: function () {
                if ((this.currentPage * this.pageSize) < this.contents.length) this.currentPage++;
            },
            prevPage: function () {
                if (this.currentPage > 1) this.currentPage--;
            },
            showPage: function (no) {
                this.currentPage = no;
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
            },
            totalPageNum: function() {
                return Math.ceil(this.contents.length / this.pageSize);
            },
            sortedContents: function () {
                return this.contents.sort((a, b) => {
                    let modifier = 1;

                    if (this.currentSortDir === 'desc') modifier = -1;

                    if(!isNaN(a[this.currentSort]) && !isNaN(b[this.currentSort])) {
                        if (parseInt(a[this.currentSort]) < parseInt(b[this.currentSort])) return -1 * modifier;
                        if (parseInt(a[this.currentSort]) > parseInt(b[this.currentSort])) return 1 * modifier;
                    } else {
                        if (a[this.currentSort] < b[this.currentSort]) return -1 * modifier;
                        if (a[this.currentSort] > b[this.currentSort]) return 1 * modifier;
                    }
                    return 0;

                }).filter((row, index) => {
                    let start = (this.currentPage - 1) * this.pageSize;
                    let end = this.currentPage * this.pageSize;
                    if (index >= start && index < end) return true;
                });
            }
        },
        watch: {
            contents: function(){
                if(this.contents.length < this.pageSize * (this.currentPage - 1)) {
                    return this.currentPage = 1;
                }
            },
            sortBy: function() {
                return this.currentSort = this.sortBy;
            }
        }
    }
</script>
