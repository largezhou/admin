import Vue from 'vue'
import Vuex from 'vuex'

import users from './modules/users'
import vueRouters from './modules/vue-routers'
import sideMenu from './modules/side-menu'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    users,
    vueRouters,
    sideMenu,
  },
  actions: {
    feLogout({ dispatch }) {
      dispatch('clearAuth')
    },
  },
})
