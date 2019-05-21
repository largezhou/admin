import Vue from 'vue'
import Vuex from 'vuex'
import { getToken, removeToken, setToken } from '@/libs/token'
import { login, logout } from '@/api/auth'
import { getUser } from '@/api/admin_user'

Vue.use(Vuex)

// 配置
const config = JSON.parse(document.querySelector('#admin-app').dataset.config)

export default new Vuex.Store({
  state: {
    config,
    token: getToken(),
    user: null,
  },
  getters: {
    loggedIn(state) {
      return !!state.user
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
    feLogout({ commit }) {
      removeToken()
      commit('SET_TOKEN', '')
      commit('SET_USER', null)
    },
    async getUser({ commit }) {
      const { data } = await getUser()
      commit('SET_USER', data)
    },
  },
})
