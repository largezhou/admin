import Vue from 'vue'
import Vuex from 'vuex'

import users from './modules/users'
import menus from './modules/menus'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    users,
    menus,
  },
  actions: {
    feLogout({ dispatch }) {
      dispatch('clearAuth')
    },
  },
})
