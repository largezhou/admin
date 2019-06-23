import router from '@/router'
import { fixedRoutes } from '@/router/routes'
import { buildRoutes, makeRouteName } from '@/libs/utils'
import _get from 'lodash/get'
import { getVueRouters } from '@/api/admin-configs'

export default {
  state: {
    vueRouters: [],
    loaded: false,
    homeRoute: null,
  },
  getters: {
    homeName(state) {
      return _get(state, 'homeRoute.name')
    },
  },
  mutations: {
    SET_VUE_ROUTERS(state, vueRouters) {
      state.vueRouters = vueRouters
    },
    SET_LOADED(state, payload) {
      state.loaded = payload
    },
    SET_HOME_ROUTE(state, route) {
      state.homeRoute = route
    },
  },
  actions: {
    async getVueRouters({ commit }) {
      const { data } = await getVueRouters()
      commit('SET_VUE_ROUTERS', data)
      commit('SET_LOADED', true)

      // 暂时写死 1 为首页
      const { routes, homeRoute } = buildRoutes(data, makeRouteName(1))
      router.addRoutes(routes)
      router.addRoutes(fixedRoutes)
      commit('SET_HOME_ROUTE', homeRoute)
    },
    clearAuth({ commit }) {
      commit('SET_VUE_ROUTERS', [])
      commit('SET_LOADED', false)
    },
  },
}
