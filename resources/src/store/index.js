import Vue from 'vue'
import Vuex from 'vuex'

import users from './modules/users'
import menus from './modules/menus'
import { removeToken } from '@/libs/token'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    users,
    menus,
  },
  actions: {
    feLogout({ dispatch }) {
      removeToken()
      dispatch('clearAuth')
    },
  },
})
