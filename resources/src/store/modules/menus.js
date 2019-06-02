import { getMenus } from '@/api/admin-menus'
import router from '@/router'
import { pageNotFoundRoute } from '@/router/routes'
import { buildRoutes, makeRouteName } from '@/libs/utils'
import _get from 'lodash/get'

export default {
  state: {
    menus: [],
    loaded: false,
    homeRoute: null,
    opened: true,
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
    SET_OPENED(state, payload) {
      state.opened = payload
    },
  },
  actions: {
    async getMenus({ commit }) {
      const { data } = await getMenus()
      commit('SET_MENUS', data)
      commit('SET_LOADED', true)

      // 暂时写死 1 为首页
      const { routes, homeRoute } = buildRoutes(data, makeRouteName(1))
      router.addRoutes(routes)
      router.addRoutes([pageNotFoundRoute])
      commit('SET_HOME_ROUTE', homeRoute)
    },
    clearAuth({ commit }) {
      commit('SET_MENUS', [])
      commit('SET_LOADED', false)
    },
    toggleOpened({ commit, state }) {
      commit('SET_OPENED', !state.opened)
    },
  },
}
