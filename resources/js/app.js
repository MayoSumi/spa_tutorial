import Vue from '../../node_modules/vue/dist/vue.esm.browser';

import router from './router';

new Vue({
    el: '#app',
    router,
    components: { App },
    template: '<App />'
})
