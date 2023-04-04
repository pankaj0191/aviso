import Vue from 'vue';
import router from './spa-projects-router.js';
import ElementUI from 'element-ui';
import lang from 'element-ui/lib/locale/lang/en';
import locale from 'element-ui/lib/locale';
import store from './../../vuex/store.js';
import linkify from 'vue-linkify';
import VueQuillEditor from 'vue-quill-editor';
import VueClipboard from 'vue-clipboard2';

// Quill Editor Styles
import 'quill/dist/quill.core.css';
import 'quill/dist/quill.snow.css';
import 'quill/dist/quill.bubble.css';
import 'quill/dist/quill.bubble.css';

// Filters
import './filters/filters';

require('../../../js/bootstrap');



Vue.directive('linkified', linkify);

Vue.use(VueClipboard);

Vue.use(ElementUI);
// configure language
locale.use(lang);

Vue.use(VueQuillEditor);
Vue.config.productionTip = false;
Vue.component('projects-container', require('./ProjectsContainer.vue'));
Vue.component('ProofloCard', require('./v3/components/Card'));
Vue.component('register', require('./v3/Pages/Auth/Registration/Show'));
var app_porojects = new Vue({
    router,
    store,
    created() {
        if (Spark.auth) {
            store.dispatch('bootstrap');
        }
    }

}).$mount('#spa-projects');
