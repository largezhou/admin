import { getToken, setToken } from '@/libs/token'
import { login, logout } from '@/api/auth'
import { getUser } from '@/api/admin-user'

export default {
  state: {
    token: getToken(),
    user: null,
  },
  getters: {
    loggedIn(state) {
      return !!state.user
    },
    userInfo: state => field => {
      return state.user ? state.user[field] : null
    },
  },
  mutations: {
    SET_TOKEN(state, token) {
      state.token = token
    },
    SET_USER(state, user) {
      state.user = user
    },
  },
  actions: {
    async login({ commit }, payload) {
      const { data } = await login(payload)
      const token = 'bearer ' + data.token
      setToken(token)
      commit('SET_TOKEN', token)
    },
    async logout({ dispatch }) {
      await logout()
      dispatch('feLogout')
    },
    clearAuth({ commit }) {
      commit('SET_TOKEN', '')
      commit('SET_USER', null)
    },
    async getUser({ commit }) {
      const { data } = await getUser()
      commit('SET_USER', data)
    },
  },
}
