const COLLAPSED_KEY = 'side-menu-collapsed'

export default {
  state: {
    opened: !localStorage.getItem(COLLAPSED_KEY),
  },
  mutations: {
    SET_OPENED(state, payload) {
      localStorage.setItem(COLLAPSED_KEY, payload ? '' : '1')
      state.opened = payload
    },
  },
  actions: {
    toggleOpened({ commit, state }) {
      commit('SET_OPENED', !state.opened)
    },
  },
}
