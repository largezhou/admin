import Vue from 'vue'
import { getConfigsValueByCategorySlug } from '@/api/configs'
import { SYSTEM_BASIC } from '@/libs/constants'
import _get from 'lodash/get'

export default {
  state: {},
  mutations: {
    SET_CONFIG(state, { path, value }) {
      Vue.set(state, path, value)
    },
  },
  getters: {
    getConfig: (state) => (path, defaultVal) => {
      return _get(state, path, defaultVal)
    },
    appName(state) {
      const { SLUG, DEFAULT_APP_NAME, APP_NAME_SLUG } = SYSTEM_BASIC
      return _get(state, `${SLUG}.${APP_NAME_SLUG}`, DEFAULT_APP_NAME)
    },
  },
  actions: {
    async getSystemBasicConfigs({ commit }) {
      const { data } = await getConfigsValueByCategorySlug(SYSTEM_BASIC.SLUG)
      commit('SET_CONFIG', {
        path: SYSTEM_BASIC.SLUG,
        value: data,
      })
    },
  },
}
