import Vue from 'vue'
import Vuex from 'vuex'

import users from './modules/users'
import vueRouters from './modules/vue-routers'
import sideMenu from './modules/side-menu'

Vue.use(Vuex)

/**
 * 存放已经缓存了的组件名，用于 include 去重
 */
const cachedKeys = {
  ParentView: true,
}

export default new Vuex.Store({
  modules: {
    users,
    vueRouters,
    sideMenu,
  },
  state: {
    miniWidth: window.innerWidth <= 768,
    include: ['ParentView'],
  },
  mutations: {
    SET_MINI_WIDTH(state, payload) {
      state.miniWidth = payload
    },
    ADD_INCLUDE(state, name) {
      if (cachedKeys[name]) {
        return
      }

      cachedKeys[name] = true
      state.include.push(name)
    },
  },
})
