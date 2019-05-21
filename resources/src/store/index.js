import Vue from 'vue'
import Vuex from 'vuex'
import { getToken, removeToken, setToken } from '@/libs/token'
import { login, logout } from '@/api/auth'

Vue.use(Vuex)

// 配置
const config = JSON.parse(document.querySelector('#admin-app').dataset.config)

export default new Vuex.Store({
  state: {
    config,
    token: getToken(),
  },
  mutations: {
    SET_TOKEN(state, token) {
      state.token = token
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
    },
  },
})
