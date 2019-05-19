import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

// 配置
const config = JSON.parse(document.querySelector('#admin-app').dataset.config)

export default new Vuex.Store({
  state: {
    config,
  },
  mutations: {},
  actions: {},
})
