import { getMenus } from '@/api/admin-menus'

export default {
  state: {
    menus: [],
    loaded: false,
  },
  mutations: {
    SET_MENUS(state, menus) {
      state.menus = menus
    },
    SET_LOADED(state, payload) {
      state.loaded = payload
    },
  },
  actions: {
    async getMenus({ commit }) {
      const { data } = await getMenus()
      commit('SET_MENUS', data)
      commit('SET_LOADED', true)
    },
    clearAuth({ commit }) {
      commit('SET_MENUS', [])
      commit('SET_LOADED', false)
    },
  },
}
