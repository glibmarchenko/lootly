@extends('layouts.app')

@section('title', 'Manage Integrations')

@section('content')
    <div id="apps-page" class="m-t-20 m-b-10">
        <div class="row m-t-20 p-b-10 m-b-30 section-border-bottom">
            <div class="col-md-6 col-6">
                <h3 class="page-title m-t-0 color-dark">Manage Integrations</h3>
            </div>
            <div class="col-md-6 col-6 text-right ">
                <a href="{{route('integrations.overview')}}" class="btn btn-save">Add more</a>
            </div>
        </div>

        <div :class="{ 'loading' : loading }" v-cloak>
            <div v-if="apps.length">
                <div class="row m-b-25" v-for="app in apps" v-cloak>
                    <div class="col-md-12 col-12">
                        <div class="well bg-white p-t-10 p-b-10">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-col-4 table-border-none v-a-m m-b-0">
                                        <tbody>
                                        <tr>
                                            <td class="bolder">
                                                <span class="v-a-m inline-block integration-icon" :style="'background-image: url('+app.icon+')'"></span>
                                                <span class="m-l-10" v-text="app.name"></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge custom-badge lg-badge badge-success" v-if="app.status == 1">Connected</span>
                                                <span class="badge custom-badge lg-badge badge-danger" v-if="app.status == 0">Disabled</span>
                                            </td>
                                            <td class="text-center">
                                                Added on
                                                <span v-text="app.added_on"></span>
                                            </td>
                                            <td>
                                                <a :href="'/integrations/manage/edit/'+app.slug"
                                                   class="bolder f-s-14 color-blue pull-right">Edit Settings</a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                No integrations yet
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script type="text/javascript">

      var rewards = new Vue({
        el: '#apps-page',
        data: {
          apps: [],
          loading: true
        },
        created: function () {
          this.getData()
        },
        methods: {
          getData: function () {
            this.loading = true
            axios.get('/api/merchants/'+Spark.state.currentTeam.id+'/integrations').then(result => {
              if(result.data && result.data.data) {
                this.apps = result.data.data.map((item) => {
                  return {
                    'id': item.integration.id || null,
                    'icon': item.integration.icon || '',
                    'name': item.integration.title || '',
                    'slug': item.integration.slug || '',
                    'status': !!item.status || false,
                    'added_on': moment(item.created).format('MM/DD/YYYY') || 'N/A'
                  }
                })
              }
            }).catch(error => {
              console.log(error)
            }).then(() => {
              this.loading = false
            })

            /*this.apps.push({
              'id': '1',
              'icon': 'shopify-icon',
              'name': 'Shopify',
              'status': 1,
              'added_on': '5/3/2018'
            })

            this.apps.push({
              'id': '2',
              'icon': 'trustspot-icon',
              'name': 'TrustSpot',
              'status': 1,
              'added_on': '1/3/2017'
            })

            this.apps.push({
              'id': '3',
              'icon': 'mailChimp-icon',
              'name': 'MailChimp',
              'status': 0,
              'added_on': '5/3/2017'
            })*/

          }
        }
      })
    </script>
@endsection