require('../bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

 // Global components
Vue.component(
    'passport-clients',
    require('../components/passport/Clients.vue')
);

Vue.component(
    'passport-authorized-clients',
    require('../components/passport/AuthorizedClients.vue')
);

Vue.component(
    'passport-personal-access-tokens',
    require('../components/passport/PersonalAccessTokens.vue')
);

const app = new Vue({
    el: '#passportThings'
});
