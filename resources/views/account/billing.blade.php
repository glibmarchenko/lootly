@extends('layouts.app')

@section('title', 'Billing')

@section('content')
    <div id="billings-index" class="loader p-b-40 m-t-20 m-b-10" v-cloak>
        <div class="row m-t-20 p-b-10 section-border-bottom">
            <div class="col-md-12">
                <h3 class="page-title m-t-0 color-dark">Billing</h3>
            </div>
        </div>
        <div class="row m-t-25">
            <div class="col-md-8">
                <div v-if="billings.length > 0" class="well well-table">
                    <sortable-table
                            :title="'Billing History'"
                            :contents="billings"
                            :page-size="10"
                            :thead="[{text: 'Date', name: 'date'}, {text: 'Amount', name: 'amount'}, {text: 'Description', name: 'description'}, {text: 'Receipt', name: '0'} ]">
                        <template slot-scope="{row}">
                            <td>
                                <span class="bold f-s-15" v-text="dateFormat(row.date)"></span>
                            </td>
                            <td>
                                $<span v-text="row.amount"></span>
                            </td>
                            <td v-text="row.description"></td>
                            <td>
                                <a class="pointer bold color-blue"
                                   target="_self"
                                   :href="'/account/get_pdf/'+ row.id">Download</a>
                            </td>
                        </template>
                    </sortable-table>
                </div>
                <div v-else class="well">
                    <span>
                        There is no billing history.
                    </span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="sticky-top">
                    <div class="well bg-white">
                        <h3 class="bold m-b-15 f-s-16">Plan Tier</h3>
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <img :src="'{{ url('/' )}}/images/icons/plans/'+plan_info.name.toLowerCase()+'.png'">
                            </div>
                            <div class="col-md-9">
                                <p class="m-b-5">
                                    <span v-text="plan_info.name"></span> Plan | <span v-text="plan_info.price"></span>
                                </p>
                                <p v-if="plan_info.name !== 'Free' && ! plan_info.isTrial">
                                    Renews <span v-text="plan_info.renews"></span>
                                </p>
                                <p v-if="plan_info.isTrial">
                                    Trial expires <span v-text="plan_info.renews"></span>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{!! route('account.upgrade') !!}"
                                   class="btn btn-success btn-glow btn-block upgrade-btn f-s-14 m-t-20">Upgrade Plan</a>
                            </div>
                        </div>
                    </div>
                    <div class="well bg-white m-t-20" v-show="plan_info.name != 'Free'">
                        <h3 class="bold m-b-15 f-s-16">Payment Method</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="credit-card m-t-10 m-b-10" :class="creditCard.type"></div>
                                <p class="text-center m-t-5">
                                    <span v-text="creditCard.lastDigits? creditCard.lastDigits : 'N/A'"></span> | Exp.
                                    <span v-text="creditCard.exp"></span>
                                </p>
                            </div>
                        </div>
                        <div class="row m-t-10">
                            <div class="col-md-12 text-center">
                                <a v-b-modal.update-payment-method-modal class="color-blue bolder f-s-15">Update
                                    Card</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('_partials._update-payment-method')
    </div>

@endsection

@section('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>

      var page = new Vue({
        el: '#billings-index',
        data: {
          billings: [
                  <?php foreach($billings as $billing): ?>
            {
              id: '{!! $billing->id !!}',
              amount: '{!! $billing->price !!}',
              description: '{!! $billing->name !!}',
              date: '{!! $billing->date !!}',
            },
              <?php endforeach; ?>
          ],
          plan_info: {
              <?php if ($plan && $plan->type && ! empty($subscription) && $subscription->isTrial()): ?>
                name: '{!! $plan->name !!}',
                price: '${!! $planPrice ?: $plan->price !!} {!! $subscription->length != 30 ? 'per ' . $subscription->length : '' !!}',
                renews: '{!! ($subscription->trial_ends_at ? $subscription->trial_ends_at->format('M d, Y') : '') !!}',
                isTrial: true,
              <?php elseif($plan && $plan->type && ! empty($subscription)): ?>
                name: '{!! $plan->name !!}',
                price: '${!! $planPrice ?: $plan->price !!} {!! $subscription->length != 30 ? 'per ' . $subscription->length : '' !!}',
                renews: '{!! ($subscription->ends_at ? $subscription->ends_at->format('M d, Y') : '') !!}',
                isTrial: false,
              <?php else: ?>
                name: 'Free',
                price: '$0',
                renews: '',
                isTrial: false,
              <?php endif; ?>
          },
          creditCard: {
            type: '{!! strtolower($merchant->card_brand) !!}',
            lastDigits: '{!! $merchant->card_last_four !!}',
            exp: '{!! $merchant->card_expiration !!}'
          },
          currentSort: 'date',
          currentSortDir: 'desc',
          pageSize: 10,
          currentPage: 1
        },
        created: function () {
          this.getData()
        },
        methods: {
          getData: function () {
            // axios.get('/demo-data/billing').then((response) => {
            //     if(response.data.length == 0) {
            //         return this.billings = 'There is no billing to show!';
            //     }
            //     this.billings = response.data;
            // });

            // let cardTypes = ['','visa', 'mastercard', 'american-express'];
            // this.creditCard.type = cardTypes[Math.floor(Math.random() * cardTypes.length)];
            // this.creditCard.lastDigits = '0772';
            // this.creditCard.exp = '04/2019';
          },
          sort: function (s) {
            if (s === this.currentSort) {
              this.currentSortDir = this.currentSortDir === 'asc' ? 'desc' : 'asc'
            }
            this.currentSort = s
          },
          nextPage: function () {
            if ((this.currentPage * this.pageSize) < this.billings.length) this.currentPage++
          },
          prevPage: function () {
            if (this.currentPage > 1) this.currentPage--
          },
          showPage: function (no) {
            this.currentPage = no
          },
          dateFormat: function (date) {
            return moment(date).format('MM/D/YYYY')
          },
          showUpdateCardModal: function () {
            this.$root.$emit('bv::show::modal', 'create-account-modal')
          }
        },
        computed: {
          pagesNo: function () {
            return Math.ceil(this.billings.length / this.pageSize)
          },
          sortedBillings: function () {
            return this.billings.sort((a, b) => {
              let modifier = 1
              if (this.currentSortDir === 'desc') modifier = -1
              if (a[this.currentSort] < b[this.currentSort]) return -1 * modifier
              if (a[this.currentSort] > b[this.currentSort]) return 1 * modifier
              return 0
            }).filter((row, index) => {
              let start = (this.currentPage - 1) * this.pageSize
              let end = this.currentPage * this.pageSize
              if (index >= start && index < end) return true
            })
          }
        }
      })

    </script>
@endsection
