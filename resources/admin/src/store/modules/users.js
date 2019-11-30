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
    async login({ commit }, vm) {
      const { data } = await login(vm.form).config({ validationForm: vm })
      const token = 'bearer ' + data.token
      setToken(token)
      commit('SET_TOKEN', token)
    },
    async logout({ dispatch }) {
      try {
        await logout().config({ disableHandle401: true })
        dispatch('frontendLogout')
      } catch (e) {
        const { response: res } = e
        // 如果退出时，返回 401，则直接前端退出就行
        if (res && res.status === 401) {
          dispatch('frontendLogout')
        } else {
          throw e
        }
      }
    },
    async getUser({ commit }) {
      const { data } = await getUser().config({ disableLoginDialog: true })
      commit('SET_USER', data)
    },
    frontendLogout() {
      removeToken()
      // 由于退出后，有一些状态清理麻烦，直接刷新页面
      window.location.href = router.resolve({ name: 'login' }).href
    },
  },
}
