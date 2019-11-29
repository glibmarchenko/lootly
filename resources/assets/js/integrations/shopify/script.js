import * as mixin from '../integrations-mixin';

(function() {
    console.log('Lootly App Initialized!')

    mixin.init()
    window.lootlyWidgetInit = mixin.init;
}())
