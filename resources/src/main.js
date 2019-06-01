import '@/libs/global'

import Vue from 'vue'
import App from '@/App.vue'
import router from '@/router'
import store from '@/store'

import '@/styles/app.scss'
import '@/plugins/element'

Vue.config.productionTip = false

const app = new Vue({
  router,
  store,
  render: (h) => h(App),
}).$mount('#admin-app')

if (process.env.NODE_ENV === 'development') {
  window.app = app
}
