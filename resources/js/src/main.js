import Vue from 'vue'
import { ToastPlugin, ModalPlugin } from 'bootstrap-vue'
import VueCompositionAPI from '@vue/composition-api'

import i18n from '@/libs/i18n'
import router from './router'
import store from './store'
import App from './App.vue'

// Global Components
import './global-components'

// 3rd party plugins
import '@axios'
import '@/libs/acl'
import '@/libs/portal-vue'
import '@/libs/clipboard'
import '@/libs/toastification'
import '@/libs/sweet-alerts'
import '@/libs/vue-select'
//import '@/libs/tour'

// Axios Mock Adapter
//import '@/@fake-db/db'

// BSV Plugin Registration
Vue.use(ToastPlugin)
Vue.use(ModalPlugin)

// Composition API
Vue.use(VueCompositionAPI)

// Feather font icon - For form-wizard
// * Shall remove it if not using font-icons of feather-icons - For form-wizard
require('@core/assets/fonts/feather/iconfont.css') // For form-wizard

// import core styles
require('@resources/scss/core.scss')

// import assets styles
require('@resources/assets/scss/style.scss')

Vue.config.productionTip = false
Vue.mixin({
    data: function () {
        return {
            /*get detilUser() {
                return user;
            },*/
            user: JSON.parse(localStorage.getItem('userData')),
            semester_id: JSON.parse(localStorage.getItem('semester_id')),
        }
    },
    methods: {
        loggedUser: function(){
            return JSON.parse(localStorage.getItem('userData'));
        },
        hasRole: function (role) {
            for (var i = 0; i < this.user.roles.length; i++) {
                if(Array.isArray(role)){
                    for (var j = 0; j < role.length; j++) {
                        if (this.user.roles[i].name == role[j]) {
                          return true
                        }
                    }
                } else {
                    if (this.user.roles[i].name == role) {
                        return true
                    }
                }
            }
            return false
        },
    }
})
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});

new Vue({
  router,
  store,
  i18n,
  render: h => h(App),
}).$mount('#app')
