import Vue from 'vue'
import Router from 'vue-router'
import routes, { menuRoutes, anyRoute } from '@/router/routes'
import { LoadingBar } from 'iview'
import { getToken } from '@/libs/token'
import store from '@/store'

Vue.use(Router)

const router = new Router({
  mode: 'history',
  base: process.env.NODE_ENV === 'development' ? 'admin-dev' : 'admin',
  routes,
})

router.addRoutes(menuRoutes)
router.addRoutes([anyRoute])

const loginRoute = to => ({
  name: 'login',
  query: {
    redirect: to.path,
  },
})

const getNeededData = async requests => {
  await Promise.all(requests)
}

router.beforeEach(async (to, from, next) => {
  LoadingBar.start()

  if (getToken()) { // 有 token 暂定为已登录
    if (to.name === 'login') { // 有 token，访问登录页，跳转到首页
      next('/')
    } else { // 否则应该获取用户信息和菜单
      const requests = []
      try {
        !store.getters.loggedIn && requests.push(store.dispatch('getUser'))
        !store.state.menus.loaded && requests.push(store.dispatch('getMenus'))
        await getNeededData(requests)
        next()
      } catch ({ response: res }) {
        if (res && res.status === 401) {
          next(loginRoute(to))
        } else {
          LoadingBar.error()
          next(false)
        }
      }
    }
  } else if (to.name !== 'login') { // 没 token 访问后台，跳到登录页
    next(loginRoute(to))
  } else { // 没 token 访问登录页，通过
    next()
  }
})

router.afterEach(() => {
  LoadingBar.finish()
})

export default router
