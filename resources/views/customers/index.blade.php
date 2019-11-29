@extends('layouts.app')

@section('title', 'Customers')

@section('content')
    <div id="customers-index" class="loader p-b-40 m-t-20 m-b-10" v-cloak>
        <b-alert v-cloak
                 v-if="!alert.insideModal"
                 :show="alert.dismissCountDown"
                 dismissible
                 :variant="alert.type"
                 @dismissed="alert.dismissCountdown=0"
                 @dismiss-count-down="countDownChanged">
                @{{alert.text}}
        </b-alert>

        <div class="row m-t-15 p-b-10 section-border-bottom">
            <div class="col-md-3 col-12 m-t-5">
                <h3 class="page-title m-t-0 color-dark">Customers</h3>
            </div>

            @if(!$merchant->checkPermitionByTypeCode('ImportExistingCustomers'))
                <div class="col-md-9 col-12 m-t-5 text-right" style="color: #797979;">
                    <a class="bold f-s-15 m-r-20" @click="openModel('bulk-points')">
                        <i class="fa fa-lock m-r-5" aria-hidden="true"></i> Bulk Adjust Points
                    </a>
                    <a class="bold f-s-15 m-r-20" @click="openModel('import-customers')">
                        <i class="fa fa-lock m-r-5" aria-hidden="true"></i> Import
                    </a>
                    <a class="bold f-s-15  m-r-20" @click="openModel('export-customers')">
                        <i class="fa fa-lock m-r-5" aria-hidden="true"></i> Export
                    </a>
            @else
                <div class="col-md-9 col-12 m-t-5 text-right" style="color: #4969ad;">
                    <a class="bold f-s-15 m-r-20" @click="openModel('bulk-points')">
                        <i class="icon-plus f-s-19 m-r-5"></i> Bulk Adjust Points
                    </a>
                    <a class="bold f-s-15 m-r-20" @click="openModel('import-customers')">
                        <i class="icon-import f-s-19 m-r-5"></i> Import
                    </a>
                    <a class="bold f-s-15 inher-color m-r-20" :href="exportCustomersUrl">
                        <i class="icon-export f-s-19 m-r-5"></i> Export
                    </a>
            @endif
            <!-- <div class="" role="group" aria-label="Basic example"> -->

                <!-- <div class="btn-group date-range-buttons">
                    <button type="button" v-bind:class="[dateRange.selectedRange == 'custom' ? 'active' : '']"
                            id="dataRange" class="btn btn-default">Custom
                    </button>
                    <button type="button" @click="changeDateRange(30)"
                            v-bind:class="[dateRange.selectedRange == '30 days' ? 'active' : '']"
                            class="btn btn-default">30 Days
                    </button>
                    <button type="button" @click="changeDateRange(7)"
                            v-bind:class="[dateRange.selectedRange == '7 days' ? 'active' : '']"
                            class="btn btn-default pull-right">7 Days
                    </button>
                </div> -->
            </div>
        </div>
        
        <div class="row m-t-25">
            <div class="col-md-12">
                <div class="well well-table">
                    <div class="table-header table-header-filters">
                        <div class="pull-left">
                            <label class="bold m-t-5">Display: </label>
                            <select class="form-control" v-model="pageSizeValue">
                                <option>5</option>
                                <option selected>10</option>
                                <option>25</option>
                                <option>50</option>
                                <option>100</option>
                            </select>
                            <label class="bold m-t-5 m-l-25">Tier: </label>
                            <select class="form-control" v-model="tierSearch">
                                <option>All</option>
                                <option v-for="tier in tiersType.data" v-text="tier.text" :value="tier.value"></option>
                            </select><label class="bold m-t-5 m-l-25">Sort: </label>
                            <select class="form-control" v-model="currentSort" style="min-width: 140px">
                                <option value="created_at">Created Date</option>
                                <option value="name">Customer Name</option>
                                <option value="purchases">Purchases</option>
                                <option value="total_spend">Total Spend</option>
                                <option value="earned_points">Points Earned</option>
                                <option value="vip_tier">VIP Tier</option>
                            </select>
                        </div>
                        <div class="col-4 pull-right">
                            <div class="input-group-icon">
                                <div class="input-icon"><span><i aria-hidden="true" class="fa fa-search"></i></span>
                                </div>
                                <input v-model="searchText" placeholder="Search customer name or email"
                                       class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                    <div v-if="searching">
                        <tbody>
                            <tr>
                                <td colspan="5">
                                    <div class="my-4" v-html="loader">
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
                            :page-size="pageSizeValue"
                            :page-num="currentPage"
                            :total="totalActivities"
                            v-on:get-content="data => { this.getContent(data) }"
                            :thead="[{text: 'Customer Name', name: 'name'}, {text: 'Purchases', name: 'purchases'}, {text: 'Total Spend', name: 'total_spend'}, {text: 'Points Earned', name: 'earned_points'}, {text: 'VIP Tier', name: 'vip_tier'}]">
                                
                            <template slot-scope="{row}">
                                <td>
                                    <a class="bold color-blue f-s-15" :href="'/customers/profile/' + row.id" v-text="row.name"></a>
                                </td>
                                <td v-text="row.purchases"></td>
                                <td v-text="row.total_spend"></td>
                                <td v-text="row.points_earned"></td>
                                <td v-text="row.vip_tier"></td>
                            </template> 
                    </custom-table>
                </div>
            </div>
        </div>

        <!-- Bulk Adjust Points Modal -->
        <b-modal class="custom-modal" id="bulk-points" title="Bulk Adjust Points" hide-footer v-cloak>
            <form>

                <div class="row m-b-10 m-t-10">
                    <div class="col-md-12">
                        <label class="light-font">This tool allows you to adjust the point balances of many customers at once.</label>
                    </div>
                </div>
                <div class="row m-b-10">
                    <div class="col-md-12">
                        <div class="form-group m-b-5">
                            <button onclick="document.getElementById('pointsFile').click()"
                                    class="btn custom-primary btn-primary bold" type="button">Choose File
                            </button>
                            <label class="m-l-10"><span
                                        v-text="adjustPointsForm.fields.pointsFile && adjustPointsForm.fields.pointsFile.name ? adjustPointsForm.fields.pointsFile.name : 'No file chosen'"></span></label>

                            <b-form-file v-model="adjustPointsForm.fields.pointsFile" name="pointsFile" class="d-none"
                                         id="pointsFile" plain></b-form-file>
                            {{--<b-form-file @change="bulkPointAdjustFileChange" class="d-none" id="pointsFile" plain></b-form-file>--}}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label>Download <a href="{{route('points.adjust.template')}}" class="bold color-blue">sample
                                template</a> to see an example of the
                            required format.</label>
                    </div>
                </div>
                <div class="row m-t-10 p-t-10 border-top">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="light-font">Tell your customers why you're making the adjustment</label>
                            <input class="form-control" placeholder="ex: Holiday Gift" name="title"
                                   v-model="adjustPointsForm.data.title">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="light-font">Reason for adjustment (internal only)</label>
                            <input class="form-control" placeholder="Add notes" name="reason"
                                   v-model="adjustPointsForm.data.reason">
                        </div>
                    </div>
                </div>

                <div class="row m-t-10 p-b-10 p-t-20 border-top">
                    <div class="col-md-6 offset-md-3 text-center">
                        <span v-if="adjustPointsForm.saving" class="i-loading"></span>
                        <button v-show="!adjustPointsForm.saving" @click.prevent="importPoint" class="btn modal-btn-lg btn-block btn-success btn-glow">
                            Import Points
                        </button>
                    </div>
                </div>
            </form>
        </b-modal>
        <!-- Import Customers Modal -->
        <b-modal class="custom-modal" id="import-customers" title="Import Existing Customers" hide-footer v-cloak>
            <form action="">
                <b-alert v-cloak
                         v-if="alert.insideModal"                
                         :show="alert.dismissCountDown"
                         dismissible
                         :variant="alert.type"
                         @dismissed="alert.dismissCountdown=0"
                         @dismiss-count-down="countDownChanged">

                        @{{alert.text}}
                </b-alert>                
                <div class="row m-b-10 m-t-10">
                    <div class="col-md-12">
                        <label class="light-font">This tool allows you to import existing customers with their point
                            balances.</label>
                    </div>
                </div>
                <div class="row m-b-10">
                    <div class="col-md-12">
                        <div class="form-group m-b-5">
                            <button onclick="document.getElementById('customersFile').click()"
                                    class="btn custom-primary btn-primary bold" type="button">Choose File
                            </button>
                            <label class="m-l-10"><span
                                        v-text="importForm.fields.importFileInput && importForm.fields.importFileInput.name ? importForm.fields.importFileInput.name : 'No file chosen'"></span></label>

                            <b-form-file v-model="importForm.fields.importFileInput" name="importFile" class="d-none"
                                         id="customersFile" plain></b-form-file>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label>Download <a href="{{route('customer.template')}}" class="bold color-blue">sample template</a> to see an example of the required format. Max 1,000 rows per file.</label>
                    </div>
                </div>
                <div class="row m-b-10">
                    <div class="col-md-12">
                        <div class="form-group m-b-5">
                            <b-form-checkbox class="w-100"
                                             v-model="importForm.fields.awardPointsCheckbox"
                                             name="awardPoints">
                                Award "Create an Account" action points
                            </b-form-checkbox>
                        </div>
                    </div>
                </div>
                <div class="row m-t-20 p-b-10 p-t-20 border-top">
                    <div class="col-md-6 offset-md-3 text-center">
                        <span v-if="importForm.saving" class="i-loading"></span>
                        <button class="btn modal-btn-lg btn-block btn-success btn-glow"
                                :disabled="importForm.saving"
                                v-show="!importForm.saving"
                                @click.prevent="importCustomer">
                            Import Customers
                        </button>
                    </div>
                </div>
            </form>
        </b-modal>
        <!-- Export Customers Modal -->
        <!-- <b-modal class="custom-modal" id="export-customers" title="Export Customers" hide-footer v-cloak>
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
                    <b-form-checkbox v-model="allTiers" @change="exportAll"> All</b-form-checkbox>
                    <b-form-checkbox-group id="ch1" stacked v-model="selectedTiers"
                                           :options="tiersType.data"></b-form-checkbox-group>
                </div>
            </div>
            <div class="row m-t-20 p-b-10 p-t-20 border-top">
                <div class="col-md-6 offset-md-3">
                    <a :href="exportCustomersUrl" class="btn modal-btn-lg btn-block btn-success btn-glow">Export
                        Customers
                    </a>
                </div>
            </div>
        </b-modal> -->

    </div>

@endsection

@section('scripts')
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css"/>
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <script>
      var start = moment().startOf('day').subtract(6, 'days');
      var end = moment().endOf('day');

      var customers = new Vue({
        el: '#customers-index',
        data: {
          search: '',
          point: '',
          tierSearch: 'All',
          searching: false,
          customers: [],
          merchant: {},
          currentSort: 'created_at',
          currentSortDir: 'desc',
          pageSize: 10,
          currentPage: 1,
          customersFile: null,
          tiersType: {
            data: [],
            loading: false
          },
          selectedTiers: [],
          allTiers: false,
          text: '',
          points: {
            pointsFile: null,
            title: '',
            reason: '',
          },
          bulkAdjustPoints: {
            saving: false
          },
          adjustPointsForm: {
            data: {
              pointsFile: null,
              title: '',
              reason: '',
            },
            fields: {
              pointsFile: null
            },
            saving: false
          },
          importForm: {
            data: {
              importFile: null,
              awardPoints: false
            },
            fields: {
              importFileInput: null,
              awardPointsCheckbox: false
            },
            saving: false
          },
          alert: {
            type: '',
            text: '',
            dismissSecs: 5,
            dismissCountDown: 0,
            insideModal: false
          },
          sortBy: 'created_at',
          loader: '<div class="loading" style="width: 40px;height: 40px;margin: 5px auto 0;"></div>',
          status: '<div class="loading" style="width: 40px;height: 40px;margin: 5px auto 0;"></div>',
          sortCustomersAZ: true,
          exportAllFlag: false,
          dateRange: {
            selectedRange: '7 days',
            start: moment().startOf('day').subtract(1, 'years'),
            end: moment().endOf('day')
          },
          totalActivities: 0,
          searchTimeout: null,
          customHashChange: true
        },
        created: function () {
          this.currentPage = this.pageInHash;
          Promise.all([this.getMerchant(), this.getContent()])
          this.getTiers()
          window.onhashchange = this.hashChange;
        },
        methods: {
          openModel: function (modelName) {
            var hasAccess = '{{$merchant->checkPermitionByTypeCode("ImportExistingCustomers")}}' === '1';
            if(hasAccess) {

                this.$root.$emit('bv::show::modal', modelName)

            } else {

                if (modelName == 'bulk-points') {
                    var swalTitle = 'Bulk Adjust Points';
                    var swalText = 'Easily adjust the point balances of many customers at once.';

                } else if (modelName == 'import-customers') {
                    var swalTitle = 'Import Existing Customers';
                    var swalText = 'Easily import in all of your existing customers to your new rewards program so their not missing out on earning rewards for previous purchases or special access to VIP Tiers';

                } else if (modelName == 'export-customers') {
                    var swalTitle = 'Export Lootly Customers';
                    var swalText = 'Export all customers from Lootly with 1-click';

                }
                swal({
                    className: "upgrade-swal",
                    title: swalTitle,
                    text: swalText,
                    icon: "/images/permissions/integrations.png",
                    buttons: {
                        catch: {
                            text: 'Upgrade to {!! App\Models\PaidPermission::getByTypeCode(\Config::get("permissions.typecode.ReferralProgram"))->getMinPlan()->name !!}',
                            value: "upgrade",
                        }
                    },
                }).then((value) => {
                    if(value == 'upgrade') {
                        window.location.href = '/account/upgrade';
                    }
                });
            }
          },
          getMerchant: function () {
            axios.get('/current/merchant').then(response => {
              this.merchant = response.data.data
            }).catch(error => {
              console.log(error)
            })
          },
          getTiers: function () {
            const comp = this
            comp.tiersType.loading = true
            axios.get('/vip/tiers/data').then((response) => {
              if (response.data.tiers) {
                comp.tiersType.data = response.data.tiers.filter((item) => {
                  return item.status
                }).map((item) => {
                  return {
                    text: item.name,
                    value: item.id
                  }
                })
              }
            }).catch((error) => {
              console.log(error)
            }).then(() => {
              comp.tiersType.loading = false
            })
          },
          importCustomer: function () {

            const comp = this
            comp.importForm.data.awardPoints = comp.importForm.fields.awardPointsCheckbox
            if (!comp.importForm.saving) {
              comp.importForm.saving = true
              let fileUpload = new Promise(function (resolve, reject) {
                if (comp.importForm.fields.importFileInput) {
                  let file = comp.importForm.fields.importFileInput
                  let reader = new FileReader()
                  reader.onload = function (e) {
                    comp.importForm.data.importFile = e.target.result
                    resolve()
                  }
                  reader.readAsDataURL(file)
                } else {
                  reject()
                }
              })

              fileUpload.then((response) => {
                axios.post('/customer/import', comp.importForm.data).then((response) => {
                  comp.getContent()
                  comp.importForm.data.importFile = null
                  comp.importForm.fields.importFileInput = null
                  comp.importForm.data.awardPoints = false
                  comp.importForm.fields.awardPointsCheckbox = false
                  document.getElementById('customersFile').value = ''

                  comp.$root.$emit('bv::hide::modal', 'import-customers')
                  comp.alert.type = 'success'
                  comp.alert.text = 'Customers successfully imported';
                }).catch((error) => {
                  let text = '';
                  console.log(error.response.data.errors)                  
                  if(error.response.data.errors == undefined) {
                    text = 'Error';
                  } else {
                    var errors = Object.values(error.response.data.errors);  
                    text = errors[0].includes('Undefined index') ? 'Incorrect file headers, please make sure they are: Name, Email, Points' : errors[0];
                  }
                  comp.alert.type = 'danger';
                  comp.alert.text = text;
                  comp.alert.insideModal = true;
                }).then(() => {
                  comp.importForm.saving = false                    
                  comp.alert.dismissCountDown = comp.alert.dismissSecs
                })
              }).catch((error) => {
                comp.importForm.saving = false
                comp.alert.type = 'danger'
                comp.alert.text = 'No file chosen';
                comp.alert.insideModal = true;                
                comp.alert.dismissCountDown = comp.alert.dismissSecs                
              });
            }
          },
          importPoint: function () {
            const comp = this
            if (!comp.adjustPointsForm.saving) {
              comp.adjustPointsForm.saving = true
              let fileUpload = new Promise(function (resolve, reject) {
                if (comp.adjustPointsForm.fields.pointsFile) {
                  let file = comp.adjustPointsForm.fields.pointsFile
                  let reader = new FileReader()
                  reader.onload = function (e) {
                    comp.adjustPointsForm.data.pointsFile = e.target.result
                    resolve()
                  }
                  reader.readAsDataURL(file)
                } else {
                  reject()
                }
              })
              fileUpload.then((response) => {
                axios.post('/point/import', comp.adjustPointsForm.data).then((response) => {
                  comp.getContent()
                  comp.$root.$emit('bv::hide::modal', 'bulk-points')
                  comp.adjustPointsForm.fields.pointsFile = null
                  comp.adjustPointsForm.data = {
                    pointsFile: null,
                    title: '',
                    reason: ''
                  }
                  document.getElementById('pointsFile').value = ''

                  comp.alert.type = 'success'
                  comp.alert.text = 'Bulk Adjust Points successful'
                }).catch((error) => {
                  clearErrors(comp.$el)
                  console.log(error.response)
                  showErrors(comp.$el, error.response.data.errors)

                  comp.alert.type = 'danger'
                  comp.alert.text = error.response.data.message
                }).then(() => {
                  comp.alert.dismissCountDown = comp.alert.dismissSecs
                })
              }).catch((error) => {
                clearErrors(comp.$el)
                showErrors(comp.$el, {
                  'pointsFile': [
                    'No file chosen'
                  ]
                })
              }).then(() => {
                comp.adjustPointsForm.saving = false
              })
            }
          },
          exportAll: function () {
            this.selectedTiers = !this.allTiers ? this.tiersType.data.map((item) => { return item.value }) : []
            this.exportAllFlag = true;
          },
          countDownChanged (dismissCountDown) {
            this.alert.dismissCountDown = dismissCountDown
          },
          changeDateRange: function (range) {
            this.tierSearch = 'All';
            if (range == 7) {
              this.dateRange.selectedRange = '7 days';
              this.dateRange.start = moment().startOf('day').subtract(6, 'days');
              this.dateRange.end = moment().endOf('day');
            } else if (range == 30) {
              this.dateRange.selectedRange = '30 days';
              this.dateRange.start = moment().startOf('day').subtract(29, 'days');
              this.dateRange.end = moment().endOf('day');
            }
            this.getContent({page: 1});
          },
          getContent: function (data = null) {
            this.searching = true;
            if(data){
                this.currentPage = data.page ? data.page : this.currentPage;
                this.currentSort = data.sortBy ? data.sortBy : this.currentSort;
                this.currentSortDir = this.getSortDir(data.sortDir);
                this.dateRange.start = data.start ? data.start : this.dateRange.start;
                this.dateRange.end = data.end ? data.end : this.dateRange.end;
            }
            let url = "{!! route('settings.customer') !!}";
            url += `?` 
                // + `start=${this.formattedDates.start}`
                // + `&end=${this.formattedDates.end}`
                + `sort_by=${this.currentSort}`
                + `&sort_dir=${this.currentSortDir}`
                + `&limit=${this.pageSize}`
                + `&offset=${this.offset}`
                + `&tier=${this.tierSearch ? this.tierSearch : ''}`
                + `&search=${this.search ? this.search : ''}`;
            
            axios.get(url).then(response => {
              if(response.data.status){
                this.customers = response.data.status;
                this.searching = false;
                return
              }
              this.customers = response.data.customers.map((c) => {
                c.total_spend_formatted = c.total_spend_nf
                if (this.merchant && this.merchant.merchant_currency) {
                  let currency = this.merchant.merchant_currency.data.name
                  if (this.merchant.currency_display_sign) {
                    currency = this.merchant.merchant_currency.data.currency_sign
                    c.total_spend_formatted = currency + c.total_spend_nf
                  } else {
                    c.total_spend_formatted = c.total_spend_nf + ' ' + currency
                  }
                }
                return c
              })
              this.totalActivities = response.data.total;
              this.customHashChange = false;
              this.pageInHash = this.currentPage;
              this.searching = false;
            }).catch((error) => {
                console.error(error);
                this.searching = false;
            });
          },
          hashChange: function () {
            this.currentPage = this.pageInHash;
            if(this.customHashChange == true) {
              this.getContent();
            } else {
              this.customHashChange = true;
            }
          },
          getSortDir: function (direction) {
            if(!direction){
                return this.currentSortDir;
            }
            if(this.currentSort == 'name' && this.sortCustomersAZ) {
              this.sortCustomersAZ = false;
              return 'asc';
            } else if(!this.sortCustomersAZ) {
              this.sortCustomersAZ = true;
              return 'desc';
            }
            this.sortCustomersAZ = true;
            return direction;
          },
        },
        computed: {
          exportCustomersUrl: function () {
            start = this.dateRange.start.format('YYYY-MM-DDTHH:mm:ss');
            end = this.dateRange.end.format('YYYY-MM-DDTHH:mm:ss');
            return `{!!route('customer.export')!!}?tier=${this.tierSearch}&search=${this.search}`;
          },
          pageSizeValue: {
            get: function(){
              return parseInt(this.pageSize);
            },
            set: function(val){
              this.currentPage = 1;
              this.pageSize = val;
              this.getContent();
            }
          },
          formattedDates: function () {
            return {
                start: this.dateRange.start.format('YYYY-MM-DDTHH:mm:ss'),
                end: this.dateRange.end.format('YYYY-MM-DDTHH:mm:ss'),
            };
          },
          offset: function(){
            return this.pageSize * (this.currentPage - 1)
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
          pageInHash: {
            cache: false,
            get: function () {
              if(window.location.hash.indexOf('page=') == -1) {
                return this.currentPage;
              }
              let pageNum = parseInt(window.location.hash.split('page=')[1].split('&')[0]);
              let maxPages = parseInt(this.totalActivities / this.pageSize);
              if(pageNum != 1 && pageNum > maxPages) {
                return maxPages == 0 ? 1 : maxPages;
              } else if (pageNum < 1) {
                return 1
              }
              return pageNum;
            },
            set: function (val) {
              return window.location.hash = '#page=' + val; 
            }
          }
        },
        watch: {
          selectedTiers: function () {
              if(this.allTiers){
                this.exportAllFlag = false;
              }
          },
          tierSearch: function () {
            this.getContent();
          },
          'alert.dismissCountDown': function(value){
            if(value == 0) {
                this.alert.insideModal = false;
            }
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
            customers.tierSearch = 'All';
            customers.getContent({data: 1});
        }

        $('#dataRange').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            opens: 'left',
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment().startOf('day'), moment().endOf('day')],
                'Yesterday': [moment().startOf('day').subtract(1, 'days'), moment().endOf('day').subtract(1, 'days')],
                'Last 7 Days': [moment().startOf('day').subtract(6, 'days'), moment().subtract(1, 'days').endOf('day')],
                'Last 30 Days': [moment().startOf('day').subtract(29, 'days'), moment().subtract(1, 'days').endOf('day')],
                'This Month': [moment().startOf('month'), moment().endOf('day')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

    </script>
@endsection
