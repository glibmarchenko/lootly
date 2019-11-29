@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div id="page" class="m-t-20 m-b-40">
    <div class="row m-t-20 p-b-10 m-b-30 section-border-bottom">
        <div class="col-md-12">
            <h3 class="page-title m-t-0 color-dark">Notifications</h3>
        </div> 
    </div>

    <div class="notifications table-responsive bg-white">
        <table class="table bg-white m-b-0">
            <thead>
                <tr>
                    <th style="width: 95px">Type</th>
                    <th>Subject</th>
                    <th style="width: 120px">Date</th>
                    <th style="width: 130px;"></th>
                    <th style="width: 70px;"></th>
                </tr>
            </thead>
            <tbody v-if="sortedNotifications.length == 0" >
                    <tr class="bg-white">
                        <td colspan="5">
                            <div v-html="status"></div>
                        </td>
                    </tr>
            </tbody>
            <tbody v-else v-cloak>
                <tr :data-id="index" v-for="(notification, index) in sortedNotifications" v-bind:class="notification.unread == 1 ? 'unread': ''">
                    <td class="bolder">
                        <span class="v-a-m inline-block" v-bind:class="notification.icon"></span>
                    </td>
                    <td>
                        <h5 v-text="notification.title"></h5>
                        <p class="description" v-bind:class="notification.description.length > 85 ? '': 'brief'">
                            <span v-text="notification.description"></span>
                        </p>
                    </td>
                    <td>
                        <p v-text="notification.date"></p>
                    </td>
                    <td class="text-center">
                        <span v-if="notification.unread == 1" class="badge custom-badge lg-badge badge-success">New</span>
                    </td>
                    <td class="text-center">
                        <a v-if="notification.description.length > 85" class="notification-toggle" @click="toggleNotification(index, notification.id)">
                            <i class="arrow"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="table-footer p-b-15" v-cloak>
            <div class="row m-t-5">
                <label class="col-md-4">
                    Showing <span class="bolder"
                                  v-text="notifications.length != 0 ? ((currentPage - 1) * pageSize) +1 : 0"></span>
                    to <span class="bolder"
                             v-text="currentPage * pageSize > notifications.length ? notifications.length: currentPage * pageSize "></span>
                    of <span class="bolder" v-text="notifications.length"></span>
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
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var notifications = new Vue({
        el: '#page',
        data: {
            notifications: [],
            pageSize: 5,
            currentPage: 1,
            status: '<div class="spinner" style="width: 40px;height: 40px;margin: 5px auto 0;"></div>'
        },
        created: function () {
            this.getData();
        },
        methods: {
            getData: function () {
                setTimeout(function(){ 
                    this.notifications = [
                        { 
                            id: 1,
                            icon: 'icon-rocket',
                            title: 'Instagram Follow now available',
                            description: 'Now, you can allow customers to follow you on Instagram and reward',
                            date: '1 hour ago',
                            unread: 1
                        },
                        { 
                            id: 2,
                            icon: 'icon-rocket',
                            title: 'Integration with ProofOwl',
                            description: 'With our latest integration you can now show prospective site visitors when reward actions occur in real time or over time. \n \n Now when a visitor is on their site they will see a small message on the bottom left that says "Customer just redeemed 100 points for a $10 off coupon" .. This is surely to get them excited and want to further engage with your site. \n Click on the Integrations Tab and than select ProofOwl for more information.',
                            date: '2 days ago',
                            unread: 0
                        },
                        { 
                            id: 3,
                            icon: 'icon-coin',
                            title: 'Lootly Paymnet Received',
                            description: 'Hello TrustGlasses, your June 2018 receipt is now available within Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                            date: '5 days ago',
                            unread: 1
                        },
                        { 
                            id: 4,
                            icon: 'icon-customers',
                            title: 'Notification Title Here',
                            description: 'Brief notification message here',
                            date: '5 days ago',
                            unread: 0
                        }
                    ]
                 }.bind(this), 1000);
            },
            nextPage: function () {
                if ((this.currentPage * this.pageSize) < this.notifications.length) this.currentPage++;
            },
            prevPage: function () {
                if (this.currentPage > 1) this.currentPage--;
            },
            showPage: function (no) {
                this.currentPage = no;
            },
            toggleNotification: function (index, id) {
                this.notifications.getObjectByKey('id', id).unread = 0;

                setTimeout(function(){ 
                    $('[data-id="'+index+'"]').addClass('opening');
                    $('.notifications tbody tr:not(.opening)').removeClass('opened');
                    $('[data-id="'+index+'"]').toggleClass('opened');
                    $('[data-id="'+index+'"]').removeClass('opening');
                 }.bind(this), 100);
            }
        },
        computed: {
            pagesNo: function () {
                return Math.ceil(this.notifications.length / this.pageSize);
            },
            sortedNotifications: function () {
                return this.notifications.filter((row, index) => {
                    let start = (this.currentPage - 1) * this.pageSize;
                    let end = this.currentPage * this.pageSize;
                    if (index >= start && index < end) return true;
                });
            }
        }
    })
</script>
@endsection