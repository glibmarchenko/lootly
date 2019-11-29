<spark-shopify :user="user" inline-template>
    <div>

        <!-- Update Store -->
        @include('spark::settings.shopify.update-store')

        @include('spark::settings.shopify.update-shopify-data')

    </div>
</spark-shopify>