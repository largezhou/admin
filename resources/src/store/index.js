import Vue from 'vue'
import Vuex from 'vuex'

import users from './modules/users'
import vueRouters from './modules/vue-routers'
import sideMenu from './modules/side-menu'
import { getConfigsValueByCategorySlug } from '@/api/configs'
import { SYSTEM_BASIC } from '@/libs/constants'
import _get from 'lodash/get'

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
    include: [],
    /**
     * 通过 path，query 来对比过后匹配到的菜单的菜单链
     */
    matchedMenusChain: [],
    /**
     * 系统基础设置
     */
    configs: null,
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
    SET_MATCHED_MENUS_CHAIN(state, menus) {
      state.matchedMenusChain = menus
    },
    SET_CONFIGS(state, configs) {
      state.configs = configs
    },
  },
  getters: {
    appConfigs: (state) => (key, defaultVal) => {
      if (!state) {
        return state.configs
      } else {
        return _get(state.configs, key, defaultVal)
      }
    },
    appName(state) {
      return _get(state.configs, SYSTEM_BASIC.APP_NAME_SLUG, SYSTEM_BASIC.DEFAULT_APP_NAME)
    },
    matchedMenu(state) {
      return state.matchedMenusChain[0] || null
    },
  },
  actions: {
    async getSystemBasicConfigs({ commit }) {
      const { data } = await getConfigsValueByCategorySlug(SYSTEM_BASIC.SLUG)
      commit('SET_CONFIGS', data)
    },
  },
})
