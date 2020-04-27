import '@/libs/global'

import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

import 'ant-design-vue/dist/antd.css'

import '@/directives'
import '@/styles/app.less'
import '@/icons'
import '@/libs/error-handle'
import '@/antd'

// 手动维护的该字段，在 activated 中变为 true 的时机，
// 会比 watch 执行的时机要晚，所以可以在组件被缓存时，不执行特定的 watch
// 在 watch 中加入 if (!this.$active) return 即可
Vue.mixin({
  beforeCreate() {
    this.$active = true
  },
  deactivated() {
    this.$active = false
  },
  activated() {
    this.$active = true
  },
})

Vue.config.productionTip = false

const app = new Vue({
  inject: {
    layout: { default: null },
  },
  router,
  store,
  render: h => h(App),
}).$mount('#admin-app')

if (process.env.NODE_ENV === 'development') {
  window.app = app
}
