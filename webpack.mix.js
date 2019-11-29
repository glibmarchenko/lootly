let mix = require('laravel-mix')
let exec = require('child_process').exec
let path = require('path')

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.sass('resources/assets/sass/main.scss', 'public/css')
   .sass('resources/assets/sass/app.scss', 'public/css')
   .sass('resources/assets/sass/widgets/widget.scss', 'public/css') // Widget
   .sass('resources/assets/sass/widgets/_widget.scss', 'public/css') // Widget
   .sass('resources/assets/sass/widgets/rewards-page.scss', 'public/css')
   .js('resources/assets/js/website/main.js', 'public/js')
   .js('resources/assets/js/app.js', 'public/js')
   .js('resources/assets/js/app-spark.js', 'public/js')
   .js('resources/assets/js/widgets/widget/widget.js', 'public/js/') // Widget
   .js('resources/assets/js/widgets/rewards-page/rewards-page.js', 'public/js/')
   .js('resources/assets/js/integrations/shopify/script.js', 'public/js/integrations/shopify/script.js')
   .js('resources/assets/js/integrations/woocommerce/script.js', 'public/js/integrations/woocommerce/script.js')
   .js('resources/assets/js/integrations/common/script.js', 'public/js/integrations/common/script.js')
   .js('resources/assets/js/integrations/bigcommerce/script.js', 'public/js/integrations/bigcommerce/script.js')
   .copy('resources/assets/js/mixins/restrictions-mixin.js', 'public/js/mixins')
   .copy('resources/assets/js/mixins/reward-codes-mixin.js', 'public/js/mixins')
   .copy('resources/assets/js/_partials/html-formater.js', 'public/js/plugins')
   .copy('resources/assets/js/helpers/helper.js', 'public/js/helper.js')
   .copy('node_modules/sweetalert/dist/sweetalert.min.js', 'public/js/sweetalert.min.js')
   .copy('node_modules/vue/dist/vue.min.js', 'public/js/plugins/vue.min.js')
   .copy('node_modules/axios/dist/axios.min.js', 'public/js/plugins/axios.min.js')
   .webpackConfig({
      resolve: {
         modules: [
            path.resolve(__dirname, 'vendor/laravel/spark-aurelius/resources/assets/js'),
            'node_modules',
         ],
         alias: {
            'vue$': mix.inProduction() ? 'vue/dist/vue.min.js' : 'vue/dist/vue.js'
         }
      }
   })
