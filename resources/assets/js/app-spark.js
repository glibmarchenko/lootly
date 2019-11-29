/*
 |--------------------------------------------------------------------------
 | Laravel Spark Bootstrap
 |--------------------------------------------------------------------------
 |
 | First, we will load all of the "core" dependencies for Spark which are
 | libraries such as Vue and jQuery. This also loads the Spark helpers
 | for things such as HTTP calls, forms, and form validation errors.
 |
 | Next, we'll create the root Vue application for Spark. This will start
 | the entire application and attach it to the DOM. Of course, you may
 | customize this script as you desire and load your own components.
 |
 */

require('spark-bootstrap');
require('./components/bootstrap');

import SparkForm from '../../../spark/resources/assets/js/forms/form';
import SparkFormErrors from '../../../spark/resources/assets/js/forms/errors';

import BootstrapVue from 'bootstrap-vue';

Vue.use(BootstrapVue);

import {ServerTable, ClientTable, Event} from 'vue-tables-2';

Vue.use(ClientTable, {}, false, 'bootstrap4', 'default');
Vue.use(ServerTable, {}, false, 'bootstrap4', 'default');

var app = new Vue({
    mixins: [SparkForm, SparkFormErrors, require('spark')]
    //mixins: []

});
