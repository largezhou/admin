import '@/libs/global'

import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import Antd from 'ant-design-vue'

import 'ant-design-vue/dist/antd.css'

import '@/directives'
import '@/styles/app.less'
import '@/icons'
import '@/libs/error-handle'

Vue.config.productionTip = false
Vue.use(Antd)

const app = new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#admin-app')

if (process.env.NODE_ENV === 'development') {
  window.app = app
}
