<spark-kiosk-resources inline-template>
    <div>
        <div class="card card-default">
            <div class="card-body px-0 px-0">

                <v-server-table
                        ref="sparkKioskResourcesTable"
                        :url="'{{ route('spark.kiosk.resources.get') }}'"
                        :columns="columns"
                        :options="options"
                >
                    <div slot="beforeFilter" class="w-100">
                        <div class="row">
                            <div class="col-6 text-left">
                                <h4 class="mb-0 ml-3">{{__('Articles')}}</h4>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('spark.kiosk.resources.create') }}" class="btn btn-primary mr-3">
                                    {{__('Add New')}}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div slot="beforeTable">
                        <div class="w-100 mb-0 border-bottom"></div>
                    </div>
                    <div slot="actions" slot-scope="props">
                        <a v-if="props.row.category.slug === 'case-studies'" :href="'{{ route('spark.kiosk.resources.case-studies.edit', ['id' => null]) }}/' + props.row.id">
                            {{__('Edit')}}
                        </a>
                        <a v-else :href="'{{ route('spark.kiosk.resources.edit', ['id' => null]) }}/' + props.row.id">
                            {{__('Edit')}}
                        </a>
                    </div>
                </v-server-table>

            </div>
        </div>
    </div>
</spark-kiosk-resources>
