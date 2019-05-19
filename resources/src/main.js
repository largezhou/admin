import '@/libs/global'

import Vue from 'vue'
import App from '@/App.vue'
import router from '@/router'
import '@/router/permission'
import store from '@/store'
import '@/plugins/iview.js'

import '@/styles/app.scss'

Vue.config.productionTip = false

new Vue({
  router,
  store,
  render: (h) => h(App),
}).$mount('#admin-app')
