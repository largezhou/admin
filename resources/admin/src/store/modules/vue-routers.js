import router from '@/router'
import { fixedRoutes } from '@/router/routes'
import { buildRoutes, makeRouteName } from '@/libs/utils'
import _get from 'lodash/get'
import { getVueRouters } from '@/api/configs'
import { SYSTEM_BASIC } from '@/libs/constants'

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
    async getVueRouters({ commit, getters }) {
      const { data } = await getVueRouters().setConfig({ disableLoginDialog: true })
      commit('SET_VUE_ROUTERS', data)
      commit('SET_LOADED', true)

      const { SLUG, HOME_ROUTE_SLUG, DEFAULT_HOME_ROUTE } = SYSTEM_BASIC
      const homeRouteId = getters.getConfig(`${SLUG}.${HOME_ROUTE_SLUG}`, DEFAULT_HOME_ROUTE)
      const { routes, homeRoute } = buildRoutes(data, makeRouteName(homeRouteId))
      router.addRoutes(routes)
      router.addRoutes(fixedRoutes)
      commit('SET_HOME_ROUTE', homeRoute)
    },
  },
}
