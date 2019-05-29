import { getMenus } from '@/api/admin-menus'
import router from '@/router'
import { anyRoute } from '@/router/routes'
import { buildRoutesFromMenus, makeRouteName } from '@/libs/utils'
import _get from 'lodash/get'

export default {
  state: {
    menus: [],
    loaded: false,
    homeRoute: null,
  },
  getters: {
    homeName(state) {
      return _get(state, 'homeRoute.name')
    },
  },
  mutations: {
    SET_MENUS(state, menus) {
      state.menus = menus
    },
    SET_LOADED(state, payload) {
      state.loaded = payload
    },
    SET_HOME_ROUTE(state, route) {
      state.homeRoute = route
    },
  },
  actions: {
    async getMenus({ commit }) {
      const { data } = await getMenus()
      commit('SET_MENUS', data)
      commit('SET_LOADED', true)

      const { routes, homeRoute } = buildRoutesFromMenus(data, makeRouteName(10))
      router.addRoutes(routes)
      router.addRoutes([anyRoute])
      commit('SET_HOME_ROUTE', homeRoute)
    },
    clearAuth({ commit }) {
      commit('SET_MENUS', [])
      commit('SET_LOADED', false)
    },
  },
}
