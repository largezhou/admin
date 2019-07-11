import { getToken, removeToken, setToken } from '@/libs/token'
import { login, logout } from '@/api/auth'
import { getUser } from '@/api/admin-users'
import router from '@/router'

export default {
  state: {
    token: getToken(),
    user: null,
  },
  getters: {
    loggedIn(state) {
      return !!state.user
    },
    userInfo: state => (field, defaultValue = null) => {
      return state.user ? state.user[field] : defaultValue
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
      removeToken()
      // 由于退出后，有一些状态清理麻烦，直接刷新页面
      window.location.href = router.resolve({ name: 'login' }).href
    },
    async getUser({ commit }) {
      const { data } = await getUser()
      commit('SET_USER', data)
    },
  },
}
